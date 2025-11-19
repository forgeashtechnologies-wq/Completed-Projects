<?php
// Ensure config is loaded
$config_path = realpath(dirname(__FILE__) . '/../admin/config.php');
if (!$config_path || !file_exists($config_path)) {
    throw new Exception("Error: Could not find config.php at: " . dirname(__FILE__) . '/../admin/config.php');
}
require_once($config_path);

class CloudBlogPoster {
    private $db;
    private $openai_key;
    private $image_dir;
    private $topics = [
        'bds' => [
            'title' => 'Understanding BDS: Your Path to Becoming a Dentist',
            'prompt' => 'Write a comprehensive, SEO-optimized blog post about Bachelor of Dental Surgery (BDS) education and career path. Include:
                        1. What is BDS and why choose it
                        2. Course duration and structure
                        3. Career opportunities after BDS
                        4. Scope in Chennai, especially Velachery area
                        5. Tips for aspiring dental students
                        Make it engaging, informative, and include local context for Chennai readers.',
            'image_prompt' => 'Professional dental student learning in modern dental clinic, dental chair and equipment visible, bright lighting, clean environment'
        ],
        'mds' => [
            'title' => 'MDS Specializations: Advanced Dental Education Guide',
            'prompt' => 'Write a detailed, SEO-optimized blog post about Master of Dental Surgery (MDS) specializations. Cover:
                        1. Different MDS specialties available
                        2. How to choose your specialization
                        3. Career prospects for each specialty
                        4. Scope in Chennai\'s dental healthcare sector
                        5. Advanced dental procedures and technologies
                        Include local context and make it relevant for Chennai audience.',
            'image_prompt' => 'Dental specialist performing advanced procedure, modern dental clinic setting, advanced dental equipment, professional environment'
        ],
        'patient_education' => [
            'title' => 'Your Complete Guide to Dental Treatment Procedures',
            'prompt' => 'Create an informative, SEO-optimized blog post about dental treatment procedures for patients. Include:
                        1. Common dental procedures explained
                        2. What to expect during treatment
                        3. Modern dental technologies used
                        4. Tips for maintaining oral health
                        5. Why choose a dental clinic in Velachery
                        Make it patient-friendly and focus on addressing common concerns.',
            'image_prompt' => 'Dentist explaining dental procedure to patient using dental model, friendly consultation setting, modern dental office background'
        ]
    ];

    // Getter for topics
    public function getTopics() {
        return $this->topics;
    }
    
    // Setter for topics
    public function setTopics($topics) {
        $this->topics = $topics;
    }

    public function __construct() {
        $this->setupDatabase();
        $this->openai_key = getenv('OPENAI_API_KEY');
        $this->image_dir = dirname(dirname(__FILE__)) . '/images/blog';
        $this->ensureImageDirectory();
    }

    private function setupDatabase() {
        try {
            error_log("Setting up database connection...");
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            error_log("DSN: " . $dsn);
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->db = new PDO($dsn, DB_USER, DB_PASS, $options);
            error_log("Database connection successful!");
        } catch (PDOException $e) {
            $error = "Database connection failed: " . $e->getMessage();
            error_log($error);
            throw new Exception($error);
        } catch (Exception $e) {
            $error = "General error during database setup: " . $e->getMessage();
            error_log($error);
            throw new Exception($error);
        }
    }

    private function reconnectDatabase() {
        error_log("Reconnecting to database...");
        try {
            // Close existing connection if it exists
            if ($this->db) {
                $this->db = null;
            }
            
            // Create new connection
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->db = new PDO($dsn, DB_USER, DB_PASS, $options);
            error_log("Database reconnection successful!");
            return true;
        } catch (Exception $e) {
            error_log("Database reconnection failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function ensureImageDirectory() {
        if (!file_exists($this->image_dir)) {
            if (!mkdir($this->image_dir, 0755, true)) {
                throw new Exception("Failed to create image directory");
            }
        }
    }

    private function generateContent($prompt) {
        error_log("Making OpenAI API call for content...");
        try {
            $response = $this->makeOpenAIRequest('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional dental content writer. Create SEO-optimized blog posts for a dental clinic in Velachery, Chennai.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000
            ]);

            if (empty($response['choices'][0]['message']['content'])) {
                throw new Exception("Empty response from OpenAI");
            }

            error_log("Successfully received content from OpenAI");
            return [
                'title' => 'Generated Blog Post', // You might want to extract this from the content
                'content' => $response['choices'][0]['message']['content']
            ];
        } catch (Exception $e) {
            error_log("Error in generateContent: " . $e->getMessage());
            throw $e;
        }
    }

    private function makeOpenAIRequest($url, $data) {
        error_log("Making request to OpenAI: " . $url);
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->openai_key
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            if ($error) {
                throw new Exception("cURL Error: " . $error);
            }

            if ($info['http_code'] !== 200) {
                error_log("OpenAI Error Response: " . $response);
                throw new Exception("OpenAI API Error: HTTP " . $info['http_code']);
            }

            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON decode error: " . json_last_error_msg());
            }

            error_log("Successfully made OpenAI request");
            return $decoded;
        } catch (Exception $e) {
            error_log("Error in makeOpenAIRequest: " . $e->getMessage());
            throw $e;
        }
    }

    private function generateImage($prompt) {
        error_log("Making OpenAI API call for image...");
        try {
            $response = $this->makeOpenAIRequest('https://api.openai.com/v1/images/generations', [
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024',
                'response_format' => 'url'
            ]);

            if (empty($response['data'][0]['url'])) {
                throw new Exception("Empty image URL from OpenAI");
            }

            error_log("Successfully received image URL from OpenAI");
            return $response['data'][0]['url'];
        } catch (Exception $e) {
            error_log("Error in generateImage: " . $e->getMessage());
            throw $e;
        }
    }

    private function saveImage($url, $topic) {
        error_log("Saving image for topic: " . $topic);
        try {
            // Create a unique filename
            $filename = $topic . '_' . time() . '.png';
            $filepath = $this->image_dir . '/' . $filename;
            
            error_log("Downloading image from: " . $url);
            error_log("Saving to: " . $filepath);

            // Download the image
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $image_data = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception("Failed to download image: " . curl_error($ch));
            }
            curl_close($ch);

            // Save the image
            if (!file_put_contents($filepath, $image_data)) {
                throw new Exception("Failed to save image to disk");
            }

            error_log("Image saved successfully");
            return 'images/blog/' . $filename;
        } catch (Exception $e) {
            error_log("Error in saveImage: " . $e->getMessage());
            throw $e;
        }
    }

    private function createPost($title, $content, $image_path, $category) {
        error_log("Creating blog post: " . $title);
        try {
            // Reconnect to database before transaction
            $this->reconnectDatabase();
            
            // Start transaction
            error_log("Starting database transaction");
            $this->db->beginTransaction();

            // Format content with proper HTML structure
            $formatted_content = $this->formatContent($title, $content['content'], $image_path);

            // Insert post
            $sql = "INSERT INTO blog_posts (title, content, author, image_url, created_at, updated_at) 
                   VALUES (:title, :content, 'AI Generated', :image_url, NOW(), NOW())";
            
            error_log("Executing insert query: " . $sql);
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                ':title' => $title,
                ':content' => $formatted_content,
                ':image_url' => $image_path
            ]);

            if (!$result) {
                throw new Exception("Failed to insert blog post");
            }

            $post_id = $this->db->lastInsertId();
            error_log("Post inserted with ID: " . $post_id);

            // Add category if it doesn't exist
            $stmt = $this->db->prepare("SELECT id FROM blog_categories WHERE name = :name");
            $stmt->execute([':name' => $category]);
            $category_id = $stmt->fetchColumn();

            if (!$category_id) {
                $stmt = $this->db->prepare("INSERT INTO blog_categories (name) VALUES (:name)");
                $stmt->execute([':name' => $category]);
                $category_id = $this->db->lastInsertId();
                error_log("Created new category: " . $category . " (ID: " . $category_id . ")");
            }

            // Link post to category
            $stmt = $this->db->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (:post_id, :category_id)");
            $stmt->execute([
                ':post_id' => $post_id,
                ':category_id' => $category_id
            ]);
            error_log("Linked post to category: " . $category);

            // Commit transaction
            error_log("Committing transaction");
            $this->db->commit();

            return $post_id;
        } catch (Exception $e) {
            error_log("Error in createPost: " . $e->getMessage());
            // Rollback transaction if something went wrong
            if ($this->db && $this->db->inTransaction()) {
                error_log("Rolling back transaction");
                $this->db->rollBack();
            }
            throw new Exception("Failed to create blog post: " . $e->getMessage());
        }
    }

    private function formatContent($title, $content, $image_path) {
        // Split content into paragraphs
        $paragraphs = explode("\n\n", trim($content));
        
        // Start with featured image
        $html = '<div class="featured-image mb-4">';
        $html .= '<img src="' . $image_path . '" alt="' . htmlspecialchars($title) . '" class="img-fluid">';
        $html .= '</div>';
        
        // Add title as H1
        $html .= '<h1>' . htmlspecialchars($title) . '</h1>';
        
        // Process each paragraph
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            
            // Skip empty paragraphs
            if (empty($paragraph)) continue;
            
            // Check if it's a list
            if (strpos($paragraph, '- ') === 0) {
                // Convert dash list to proper HTML list
                $items = explode("\n- ", $paragraph);
                array_shift($items); // Remove empty first item
                
                $html .= '<ul class="mb-4">';
                foreach ($items as $item) {
                    $html .= '<li>' . htmlspecialchars($item) . '</li>';
                }
                $html .= '</ul>';
            }
            // Check if it's a heading (starts with #)
            else if (strpos($paragraph, '# ') === 0) {
                $heading = substr($paragraph, 2);
                $html .= '<h2 class="mt-4 mb-3">' . htmlspecialchars($heading) . '</h2>';
            }
            // Regular paragraph
            else {
                $html .= '<p class="mb-4">' . htmlspecialchars($paragraph) . '</p>';
            }
        }
        
        return $html;
    }

    public function generatePosts() {
        $results = [];
        
        error_log("Starting post generation...");
        error_log("OpenAI Key exists: " . (!empty($this->openai_key) ? 'Yes' : 'No'));
        
        foreach ($this->topics as $topic => $config) {
            error_log("Generating post for topic: " . $topic);
            try {
                // Generate content
                error_log("Generating content...");
                $content = $this->generateContent($config['prompt']);
                if (empty($content)) {
                    throw new Exception("Failed to generate content");
                }
                error_log("Content generated successfully");

                // Generate image
                error_log("Generating image...");
                $image_url = $this->generateImage($config['image_prompt']);
                if (empty($image_url)) {
                    throw new Exception("Failed to generate image");
                }
                error_log("Image generated successfully: " . $image_url);

                // Save to database
                error_log("Saving to database...");
                $image_path = $this->saveImage($image_url, $topic);
                $post_id = $this->createPost(
                    $config['title'],
                    $content,
                    $image_path,
                    ucfirst($topic)
                );
                error_log("Post saved with ID: " . $post_id);

                $results[$topic] = [
                    'status' => 'success',
                    'post_id' => $post_id,
                    'title' => $config['title']
                ];
            } catch (Exception $e) {
                error_log("Error generating post for " . $topic . ": " . $e->getMessage());
                $results[$topic] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Run the poster if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    try {
        $poster = new CloudBlogPoster();
        $results = $poster->generatePosts();
        
        echo "\nFinal Results:\n";
        echo "==============\n";
        foreach ($results as $topic => $result) {
            if ($result['status'] === 'success') {
                echo "✓ $topic: Post ID {$result['post_id']} - {$result['title']}\n";
            } else {
                echo "✗ $topic: Failed - {$result['message']}\n";
            }
        }
    } catch (Exception $e) {
        echo "Fatal error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

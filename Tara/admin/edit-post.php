<?php
require_once 'config.php';

// Check admin login
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDBConnection();
$isNewPost = true;
$post = [
    'title' => '',
    'content' => '',
    'author' => '',
];
$currentCategories = [];

// Get the post ID from URL
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Only fetch post if we have an ID and it's not a new post
if ($post_id > 0) {
    // Fetch post details
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $fetchedPost = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($fetchedPost) {
        $isNewPost = false;
        $post = $fetchedPost;
        
        // Fetch current post categories
        $stmt = $db->prepare("
            SELECT category_id 
            FROM post_categories 
            WHERE post_id = ?
        ");
        $stmt->execute([$post_id]);
        $currentCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Fetch all categories
$stmt = $db->query("SELECT * FROM blog_categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Sanitize input
        $title = sanitizeInput($_POST['title']);
        $content = $_POST['content']; // TinyMCE content
        $author = sanitizeInput($_POST['author']);
        $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];
        
        if ($isNewPost) {
            // Insert new post
            $stmt = $db->prepare("
                INSERT INTO blog_posts (title, content, author, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$title, $content, $author]);
            $post_id = $db->lastInsertId();
        } else {
            // Update existing post
            $stmt = $db->prepare("
                UPDATE blog_posts 
                SET title = ?, content = ?, author = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$title, $content, $author, $post_id]);
        }
        
        // Handle categories
        if (!$isNewPost) {
            // Remove existing categories for updates
            $stmt = $db->prepare("DELETE FROM post_categories WHERE post_id = ?");
            $stmt->execute([$post_id]);
        }
        
        // Add categories
        if (!empty($selectedCategories)) {
            $stmt = $db->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
            foreach ($selectedCategories as $category_id) {
                $stmt->execute([$post_id, $category_id]);
            }
        }

        $db->commit();
        header('Location: edit-post.php?id=' . $post_id . '&status=success');
        exit;
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error " . ($isNewPost ? "creating" : "updating") . " post: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isNewPost ? 'Create New' : 'Edit'; ?> Blog Post - <?php echo htmlspecialchars(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?></title>
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/vphzsdeorpvxsxbatqv5ye6vz4kuxr1rowc1x8hg7p62zza2/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <style>
        /* Custom styles for editor */
        .tox-tinymce { border-radius: 0.375rem !important; }
        .select2-container--default .select2-selection--multiple { border-color: #d1d5db !important; border-radius: 0.375rem !important; min-height: 42px; }
        .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
        .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #e5edff !important; border-color: #c7d2fe !important; color: #4338ca !important; }
        .image-preview { max-width: 100%; max-height: 200px; object-fit: contain; }
        .image-preview-container { position: relative; display: inline-block; margin-top: 10px; }
        .image-preview-container .remove-image { position: absolute; top: -10px; right: -10px; background: #ff4d4d; color: white; border-radius: 50%; width: 25px; height: 25px; text-align: center; line-height: 25px; cursor: pointer; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><?php echo $isNewPost ? 'Create New' : 'Edit'; ?> Blog Post</h1>
                <p class="text-gray-600 mt-1">Enter the details for your blog post</p>
            </div>
            <a href="dashboard.php" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
        
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <p>Post <?php echo $isNewPost ? 'created' : 'updated'; ?> successfully!</p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <form method="POST" enctype="multipart/form-data" class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content Column (2/3 width on large screens) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Title Field -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Post Title</label>
                            <input type="text" id="title" name="title" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                   value="<?php echo htmlspecialchars(html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>" required>
                        </div>
                        <!-- Content Editor -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Post Content</label>
                            <textarea id="content" name="content" rows="12">
                                <?php echo htmlspecialchars($post['content']); ?>
                            </textarea>
                        </div>
                        
                        <!-- Featured Image Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition-colors cursor-pointer" id="image-drop-area">
                                        <div class="space-y-1 text-center">
                                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                            <div class="text-sm text-gray-600">
                                                <label for="featured_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                                    <span>Upload an image</span>
                                                    <input id="featured_image" name="featured_image" type="file" class="sr-only" accept="image/*">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP up to 5MB</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="w-1/3" id="image-preview-container">
                                    <?php if (!empty($post['image_url'])): ?>
                                    <div class="image-preview-container">
                                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Featured image" class="image-preview rounded-md border border-gray-300">
                                        <span class="remove-image" title="Remove image" id="remove-image"><i class="fas fa-times"></i></span>
                                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($post['image_url']); ?>">
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar Column (1/3 width on large screens) -->
                    <div class="space-y-6">
                        <!-- Author Field -->
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="author" name="author" 
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                       value="<?php echo htmlspecialchars($post['author']); ?>" required>
                            </div>
                        </div>
                        
                        <!-- Categories Field -->
                        <div>
                            <label for="categories" class="block text-sm font-medium text-gray-700 mb-1">Categories</label>
                            <select id="categories" name="categories[]" class="w-full border border-gray-300 rounded-md" multiple>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            <?php echo in_array($category['id'], $currentCategories) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <!-- Quick Add Category -->
                            <div class="mt-3">
                                <div class="flex gap-2" id="categoryAddForm">
                                    <input type="text" id="newCategoryName" 
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="New category name">
                                    <button type="button" onclick="addCategory()" 
                                            class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Publishing Options -->
                        <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Publishing Options</h3>
                            
                            <!-- Save Draft Button -->
                            <div class="mb-3">
                                <button type="submit" name="save_draft" value="1" class="w-full flex justify-center items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                                    <i class="fas fa-save mr-2"></i> Save Draft
                                </button>
                            </div>
                            
                            <!-- Publish Button -->
                            <div>
                                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                                    <i class="fas fa-paper-plane mr-2"></i> <?php echo $isNewPost ? 'Publish' : 'Update'; ?> Post
                                </button>
                            </div>
                        </div>
                        
                        <?php if (!$isNewPost): ?>
                        <!-- Delete Post Option -->
                        <div class="mt-6 text-center">
                            <a href="#" onclick="confirmDelete(<?php echo $post_id; ?>, '<?php echo htmlspecialchars(addslashes($post['title'])); ?>'); return false;" 
                               class="inline-flex items-center text-red-600 hover:text-red-800">
                                <i class="fas fa-trash-alt mr-2"></i> Delete this post
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

            
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" class="form-control" 
                       value="<?php echo htmlspecialchars($post['author']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control">
                    <?php echo htmlspecialchars($post['content']); ?>
                </textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary">
                    <?php echo $isNewPost ? 'Create' : 'Update'; ?> Post
                </button>
                <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
                <?php if (!$isNewPost): ?>
                <button type="button" class="btn-danger" onclick="confirmDelete(<?php echo $post_id; ?>)">Delete Post</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        // Initialize Select2 for categories
        $(document).ready(function() {
            $('#categories').select2({
                placeholder: 'Select categories',
                allowClear: true,
                tags: false,
                tokenSeparators: [','],
                theme: 'classic'
            });
            
            // Handle image preview and upload
            const imageInput = document.getElementById('featured_image');
            const previewContainer = document.getElementById('image-preview-container');
            
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            previewContainer.innerHTML = `
                                <div class="image-preview-container">
                                    <img src="${e.target.result}" alt="Preview" class="image-preview rounded-md border border-gray-300">
                                    <span class="remove-image" title="Remove image"><i class="fas fa-times"></i></span>
                                </div>
                            `;
                            
                            // Add event listener to remove button
                            const removeBtn = previewContainer.querySelector('.remove-image');
                            if (removeBtn) {
                                removeBtn.addEventListener('click', function() {
                                    previewContainer.innerHTML = '';
                                    imageInput.value = '';
                                });
                            }
                        };
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
            
            // Handle existing image removal
            const removeImageBtn = document.getElementById('remove-image');
            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function() {
                    const container = this.closest('.image-preview-container');
                    if (container) {
                        container.remove();
                        // Add a hidden input to indicate image should be removed
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'remove_image';
                        hiddenInput.value = '1';
                        previewContainer.appendChild(hiddenInput);
                    }
                });
            }
            
            // Setup drag and drop for image upload
            const dropArea = document.getElementById('image-drop-area');
            if (dropArea) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, preventDefaults, false);
                });
                
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropArea.addEventListener(eventName, highlight, false);
                });
                
                ['dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, unhighlight, false);
                });
                
                function highlight() {
                    dropArea.classList.add('bg-gray-100');
                }
                
                function unhighlight() {
                    dropArea.classList.remove('bg-gray-100');
                }
                
                dropArea.addEventListener('drop', handleDrop, false);
                
                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    
                    if (files.length) {
                        imageInput.files = files;
                        // Trigger change event
                        const event = new Event('change', { bubbles: true });
                        imageInput.dispatchEvent(event);
                    }
                }
            }
        });

        // Initialize TinyMCE with enhanced configuration
        tinymce.init({
            selector: '#content',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount quickbars preview fullscreen code',
            toolbar: 'undo redo | styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat code fullscreen',
            height: 500,
            menubar: 'file edit view insert format tools table help',
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            branding: false,
            promotion: false,
            
            // Enhanced image upload settings
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', 'upload.php');

                    xhr.upload.onprogress = (e) => {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = function() {
                        if (xhr.status === 403) {
                            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                            return;
                        }
                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }

                        const json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }

                        resolve(json.location);
                    };

                    xhr.onerror = () => {
                        reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    xhr.send(formData);
                });
            },
            
            // Quick insert toolbar
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
            quickbars_insert_toolbar: 'image media table',
            
            // Image settings
            image_caption: true,
            image_dimensions: false,
            object_resizing: true,
            resize_img_proportional: true,
            image_class_list: [
                { title: 'None', value: '' },
                { title: 'Responsive', value: 'img-fluid' },
                { title: 'Left Aligned', value: 'float-left mr-4 mb-2' },
                { title: 'Right Aligned', value: 'float-right ml-4 mb-2' },
                { title: 'Centered', value: 'mx-auto d-block' }
            ],
            
            // Content styling
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px; line-height:1.6; }',
            
            // Additional settings
            paste_data_images: true,
            smart_paste: true,
            contextmenu: 'link image table',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
        
        // Category management AJAX functions
        function addCategory() {
            const nameInput = document.getElementById('newCategoryName');
            const name = nameInput.value.trim();
            
            if (!name) {
                alert('Please enter a category name');
                return;
            }
        
            const formData = new FormData();
            formData.append('action', 'create');
            formData.append('category_name', name);
            
            fetch('category-actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    nameInput.value = ''; // Clear input
                    // Refresh the categories dropdown
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        }
        
        function editCategory(id, name) {
            const newName = prompt('Edit category name:', name);
            if (newName && newName !== name) {
                const formData = new FormData();
                formData.append('action', 'edit');
                formData.append('category_id', id);
                formData.append('name', newName);
                
                fetch('category-actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            }
        }
        
        function deleteCategory(id, name) {
            if (confirm(`Are you sure you want to delete category "${name}"? This will remove it from all posts.`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('category_id', id);
                
                fetch('category-actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            }
        }
        
        function confirmDelete(postId, postTitle) {
            if (confirm(`Are you sure you want to delete the post "${postTitle}"? This action cannot be undone.`)) {
                window.location.href = `delete-post.php?id=${postId}`;
            }
        }

        // Category management AJAX functions
        function editCategory(id, name) {
            const newName = prompt('Edit category name:', name);
            if (newName && newName !== name) {
                updateCategory(id, newName);
            }
        }

        function deleteCategory(id, name) {
            if (confirm(`Are you sure you want to delete category "${name}"? This will remove it from all posts.`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('category_id', id);
                
                fetch('category-actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                });
            }
        }

        function updateCategory(id, newName) {
            const formData = new FormData();
            formData.append('action', 'edit');
            formData.append('category_id', id);
            formData.append('name', newName);
            
            fetch('category-actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }

        // Handle category form submission
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('category-actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        });
        // Add this after your existing JavaScript
function addCategory() {
    const nameInput = document.getElementById('newCategoryName');
    const name = nameInput.value.trim();
    
    if (!name) {
        alert('Please enter a category name');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('category_name', name);
    
    fetch('category-actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            nameInput.value = ''; // Clear input
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Modify the other category functions to include error catching
function editCategory(id, name) {
    const newName = prompt('Edit category name:', name);
    if (newName && newName !== name) {
        const formData = new FormData();
        formData.append('action', 'edit');
        formData.append('category_id', id);
        formData.append('name', newName);
        
        fetch('category-actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }
}

function deleteCategory(id, name) {
    if (confirm(`Are you sure you want to delete category "${name}"? This will remove it from all posts.`)) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('category_id', id);
        
        fetch('category-actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }
}

function confirmDelete(postId) {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        window.location.href = 'delete-post.php?id=' + postId;
    }
}
    </script>
</body>
</html>
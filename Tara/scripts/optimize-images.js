const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

// Configuration
const QUALITY = 80; // Adjust quality (0-100)
const MAX_WIDTH = 1200; // Maximum width for large images
const THUMBNAIL_WIDTH = 300; // Width for thumbnails
const LAZY_LOAD_PLACEHOLDER_WIDTH = 20; // Small placeholder width

async function createPlaceholderImage(inputPath, outputPath) {
    try {
        await sharp(inputPath)
            .resize(LAZY_LOAD_PLACEHOLDER_WIDTH, null, {
                fit: 'inside',
                withoutEnlargement: true
            })
            .blur(10)
            .toFile(outputPath);
        console.log(`Created placeholder: ${path.basename(outputPath)}`);
    } catch (error) {
        console.error(`Error creating placeholder for ${inputPath}:`, error);
    }
}

async function optimizeImage(inputPath, outputPath, options = {}) {
    const { width = MAX_WIDTH, quality = QUALITY } = options;
    
    try {
        // Create WebP version
        const webpOutputPath = outputPath.replace(path.extname(outputPath), '.webp');
        await sharp(inputPath)
            .resize(width, null, {
                fit: 'inside',
                withoutEnlargement: true
            })
            .webp({ quality }) // Convert to WebP format
            .toFile(webpOutputPath);
        
        // Create original format with optimization
        await sharp(inputPath)
            .resize(width, null, {
                fit: 'inside',
                withoutEnlargement: true
            })
            .toFile(outputPath);
            
        console.log(`Optimized: ${path.basename(inputPath)} -> ${path.basename(outputPath)} & ${path.basename(webpOutputPath)}`);
    } catch (error) {
        console.error(`Error processing ${inputPath}:`, error);
    }
}

async function processDirectory(inputDir) {
    // Create optimized and placeholder directories if they don't exist
    const optimizedDir = path.join(path.dirname(inputDir), 'optimized');
    const placeholderDir = path.join(path.dirname(inputDir), 'placeholders');
    
    [optimizedDir, placeholderDir].forEach(dir => {
        if (!fs.existsSync(dir)) {
            fs.mkdirSync(dir, { recursive: true });
        }
    });

    const files = fs.readdirSync(inputDir);

    for (const file of files) {
        const inputPath = path.join(inputDir, file);
        const stats = fs.statSync(inputPath);

        if (stats.isDirectory()) {
            // Skip the optimized and placeholder directories to prevent infinite recursion
            if (!['optimized', 'placeholders'].includes(path.basename(inputPath))) {
                await processDirectory(inputPath);
            }
        } else if (/\.(jpg|jpeg|png|gif)$/i.test(file)) {
            // Process image files
            const filename = path.parse(file).name;
            
            // Optimize image
            const optimizedPath = path.join(optimizedDir, `${filename}-optimized${path.extname(file)}`);
            await optimizeImage(inputPath, optimizedPath);
            
            // Create placeholder
            const placeholderPath = path.join(placeholderDir, `${filename}-placeholder.jpg`);
            await createPlaceholderImage(inputPath, placeholderPath);
        }
    }
}

// Directory paths
const imagesDir = path.join(__dirname, '..', 'images');

// Run optimization
processDirectory(imagesDir)
    .then(() => console.log('Image optimization complete!'))
    .catch(error => console.error('Error:', error));

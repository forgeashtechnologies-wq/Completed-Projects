#!/bin/bash

# Comprehensive Image Optimization Script

# Ensure required tools are installed
if ! command -v convert &> /dev/null; then
    echo "Installing ImageMagick..."
    brew install imagemagick
fi

if ! command -v cwebp &> /dev/null; then
    echo "Installing WebP converter..."
    brew install webp
fi

# Create necessary directories
mkdir -p converted_images optimized_images responsive_images

# Function to optimize and resize images
optimize_image() {
    local input_file="$1"
    local filename=$(basename "$input_file")
    local extension="${filename##*.}"
    local filename_base="${filename%.*}"

    # Convert to WebP
    cwebp -q 75 "$input_file" -o "converted_images/${filename_base}.webp"

    # Resize for responsive design
    convert "$input_file" -resize 800x600\> "responsive_images/${filename}"

    # Compress original image
    convert "$input_file" -quality 75 "optimized_images/${filename}"
}

# Optimize slider images
for img in images/slider*.{jpg,png}; do
    if [ -f "$img" ]; then
        optimize_image "$img"
    fi
done

# Optimize all images
for img in images/*.{jpg,jpeg,png}; do
    if [ -f "$img" ]; then
        optimize_image "$img"
    fi
done

echo "Image optimization complete!"

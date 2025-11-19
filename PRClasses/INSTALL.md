# PR Classes Website Installation Guide

This guide will help you set up the PR Classes website on your Hostinger hosting account.

## Prerequisites

- A Hostinger hosting account with PHP and MySQL support
- FTP access to your hosting account
- Basic knowledge of web hosting and database management

## Step 1: Database Setup

1. Log in to your Hostinger control panel
2. Navigate to the MySQL Databases section
3. Create a new database (e.g., `prclasses_db`)
4. Create a new database user with a strong password
5. Assign all privileges to the user for the created database
6. Import the database structure using the provided SQL file:
   - Download the `database/prclasses_db_setup.sql` file from this repository
   - In Hostinger, use phpMyAdmin to import this file into your database

## Step 2: File Upload

1. Download all files from this repository
2. Connect to your hosting account using FTP
3. Upload all files to the public_html directory (or a subdirectory if you prefer)

## Step 3: Configuration

1. Edit the `includes/config.php` file to update the database connection details:
   ```php
   // Database Configuration
   define('DB_HOST', 'localhost'); // Usually 'localhost' on Hostinger
   define('DB_USER', 'your_database_username'); // The username you created
   define('DB_PASS', 'your_database_password'); // The password you set
   define('DB_NAME', 'your_database_name'); // The database name you created

   // Site Configuration
   define('SITE_NAME', 'PR Classes');
   define('SITE_URL', 'https://your-domain.com/'); // Update with your actual domain
   ```

2. Create the uploads directory structure and set permissions:
   - Create directories: `uploads/gallery` and `uploads/testimonials`
   - Set permissions to 755 for directories

## Step 4: Admin Setup

1. Access the admin panel at `https://your-domain.com/admin/`
2. Log in with the default credentials:
   - Username: `admin`
   - Password: `admin123`
3. **Important**: After logging in, immediately go to Settings and change the default password

## Step 5: Initial Content

1. Add your courses through the admin panel
2. Upload gallery images and videos
3. Review and approve any testimonials

## Security Considerations

1. Make sure to change the default admin password immediately
2. Consider setting up SSL for your domain if not already enabled
3. Regularly backup your database and files

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify your database credentials in `includes/config.php`
   - Check if the database exists and the user has proper permissions

2. **Upload Permission Issues**
   - Ensure the `uploads` directory and its subdirectories have write permissions (755)

3. **Blank Page or PHP Errors**
   - Check your PHP version (recommended: PHP 7.4 or higher)
   - Enable error reporting temporarily for debugging

### Getting Help

If you encounter any issues during installation, please contact the developer for assistance.

## Maintenance

- Regularly update your PHP version for security
- Backup your database periodically
- Monitor disk space usage, especially if you upload many gallery images

---

© 2025 PR Classes. All rights reserved.
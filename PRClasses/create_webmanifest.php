<?php
// Create site.webmanifest file
$manifest_content = '{
  "name": "PR Classes",
  "short_name": "PR Classes",
  "icons": [
    {
      "src": "/images/favicon/android-chrome-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/images/favicon/android-chrome-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ],
  "theme_color": "#ffffff",
  "background_color": "#ffffff",
  "display": "standalone"
}';

$root_path = $_SERVER['DOCUMENT_ROOT'];
$manifest_path = $root_path . '/site.webmanifest';

if (file_put_contents($manifest_path, $manifest_content)) {
    echo "site.webmanifest created successfully!";
} else {
    echo "Failed to create site.webmanifest";
}
?> 
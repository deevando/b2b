RewriteEngine On

# for when VisualQuery is placed in sub-directory
RewriteBase ${visual_query_dir}

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

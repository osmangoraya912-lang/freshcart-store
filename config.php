<?php
/**
 * FreshCart Store - Configuration
 * After running install.php this file is filled in automatically.
 * You can also edit the values below by hand.
 */
define('SITE_NAME', 'FreshCart Store');
define('SITE_TAGLINE', 'Quality products, delivered to your doorstep');
define('CURRENCY', 'Rs. ');

// Database - fill these in (install.php does it for you)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'freshcart');
define('DB_USER', 'root');
define('DB_PASS', '');

// Store rules
define('FREE_DELIVERY_OVER', 5000);   // free delivery above this subtotal
define('DELIVERY_FEE', 200);          // flat delivery fee below the threshold
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('UPLOAD_URL', 'uploads');

session_start();

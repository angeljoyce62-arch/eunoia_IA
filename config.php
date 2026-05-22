<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$servername = "localhost";
$username   = "root";
$password   = ""; // leave empty if no password is set in XAMPP
$database   = "eunoia_db";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve dynamic shop settings from database (table may not exist if DB was recreated)
$shop_settings = [
    'shop_name' => 'eunoia_IA',
    'shop_logo' => '',
    'shop_description' => 'Your clean, modern, and user-friendly standard e-commerce shop.',
    'shop_phone' => '09123456789',
    'shop_email' => 'contact@eunoia.com'
];

$shop_query = @mysqli_query($conn, "SELECT * FROM shop_settings WHERE id = 1");
if ($shop_query && mysqli_num_rows($shop_query) > 0) {
    $shop_settings = mysqli_fetch_assoc($shop_query);
}


// Curated luxury images fallback helper
function getProductImage($imageName, $category = 'General') {
    if (!empty($imageName) && file_exists(__DIR__ . '/images/' . $imageName)) {
        return 'images/' . $imageName;
    }
    
    $cat = strtolower($category);
    $name = strtolower($imageName);
    
    if (strpos($cat, 'furn') !== false) {
        return 'https://images.unsplash.com/photo-1592078615290-033ee584e267?auto=format&fit=crop&q=80&w=600';
    } elseif (strpos($cat, 'app') !== false || strpos($cat, 'cloth') !== false) {
        return 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=600';
    } elseif (strpos($cat, 'light') !== false) {
        return 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&q=80&w=600';
    } elseif (strpos($cat, 'dec') !== false) {
        if (strpos($name, 'incense') !== false) {
            return 'https://images.unsplash.com/photo-1602872030219-aa047913341b?auto=format&fit=crop&q=80&w=600';
        }
        return 'https://images.unsplash.com/photo-1578500494198-246f612d3b3d?auto=format&fit=crop&q=80&w=600';
    }
    
    return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&q=80&w=600';
}
?>

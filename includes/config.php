<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bitech_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if they don't exist
$sql_products = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql_contacts = "CREATE TABLE IF NOT EXISTS contacts (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql_products)) {
    echo "Error creating products table: " . $conn->error;
}

if (!$conn->query($sql_contacts)) {
    echo "Error creating contacts table: " . $conn->error;
}

if (!$conn->query($sql_users)) {
    echo "Error creating users table: " . $conn->error;
}

// Insert sample products if table is empty
$check_products = "SELECT COUNT(*) as count FROM products";
$result = $conn->query($check_products);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $sample_products = [
        ["Gaming Desktop Pro", "High-performance gaming computer with RTX 4080", 1499.99, "computers", "computer1.jpg"],
        ["Business Laptop Elite", "Professional laptop for business users", 899.99, "laptops", "laptop1.jpg"],
        ["Smartphone X200", "Latest smartphone with 5G and advanced camera", 699.99, "mobiles", "phone1.jpg"],
        ["Mechanical Keyboard", "RGB mechanical gaming keyboard", 129.99, "parts", "keyboard1.jpg"],
        ["Office Desktop Basic", "Reliable desktop for office work", 599.99, "computers", "computer2.jpg"],
        ["Ultrabook Air", "Lightweight and powerful ultrabook", 1199.99, "laptops", "laptop2.jpg"]
    ];
    
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($sample_products as $product) {
        $stmt->bind_param("ssdss", $product[0], $product[1], $product[2], $product[3], $product[4]);
        $stmt->execute();
    }
    $stmt->close();
}
?>

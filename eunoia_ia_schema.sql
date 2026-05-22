

-- Drop and recreate the database
DROP DATABASE IF EXISTS eunoia_db;
CREATE DATABASE eunoia_db;
USE eunoia_db;

-- Disable foreign key checks for clean creation
SET FOREIGN_KEY_CHECKS=0;

-- Create all tables
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('customer','seller','admin') NOT NULL DEFAULT 'customer'
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    category VARCHAR(50) DEFAULT 'General',
    -- Comma-separated list of available colors (e.g. "Red,Blue,Black")
    available_colors VARCHAR(255) DEFAULT '',
    seller_id INT,
    FOREIGN KEY (seller_id) REFERENCES users(id)
);


CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(100),
    gcash_number VARCHAR(20),
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    payment_method VARCHAR(20) DEFAULT 'GCash',
    delivery_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE shop_settings (
    id INT PRIMARY KEY,
    shop_name VARCHAR(100),
    shop_description TEXT,
    shop_phone VARCHAR(20),
    shop_email VARCHAR(100),
    shop_logo VARCHAR(255)
);

INSERT INTO shop_settings (id, shop_name, shop_description, shop_phone, shop_email) 
VALUES (1, 'eunoia_IA', 'Your clean, modern, and user-friendly standard e-commerce shop.', '09123456789', 'contact@eunoia.com');

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample data with explicit IDs
INSERT INTO users (id, username, password, email, role) VALUES
    (1, 'customer1', '$2y$10$examplehashforcustomer1', 'customer1@email.com', 'customer'),
    (2, 'seller1', '$2y$10$examplehashforseller1', 'seller1@email.com', 'seller'),
    (3, 'admin1', '$2y$10$examplehashforadmin1', 'admin1@email.com', 'admin'),
    (4, 'seller2', '$2y$10$examplehashforseller2', 'seller2@email.com', 'seller');

INSERT INTO products (id, name, description, price, image, stock, category, available_colors, seller_id) VALUES
    (1, 'Blue T-Shirt', 'Comfortable cotton t-shirt', 299.00, 'blue_tshirt.jpg', 50, 'Clothing', 'Blue,Black,White', 2),
    (2, 'Red Dress', 'Elegant red dress for all occasions', 799.00, 'red_dress.jpg', 20, 'Clothing', 'Red,Black', 2),
    (3, 'Wireless Headphones', 'Noise-canceling over-ear headphones', 2500.00, 'headphones.jpg', 15, 'Electronics', 'Black,Silver', 4),
    (4, 'Smart Watch', 'Fitness tracker with heart rate monitor', 1500.00, 'watch.jpg', 30, 'Electronics', 'Black,Gray', 4),
    (5, 'Canvas Backpack', 'Durable backpack for daily use', 850.00, 'backpack.jpg', 10, 'Accessories', 'Beige,Brown,Olive', 2);


INSERT INTO orders (id, user_id, total, status) VALUES
    (1, 1, 299.00, 'Pending');

INSERT INTO order_items (id, order_id, product_id, quantity, price) VALUES
    (1, 1, 1, 1, 299.00);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

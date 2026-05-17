

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
    seller_id INT,
    FOREIGN KEY (seller_id) REFERENCES users(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample data with explicit IDs
INSERT INTO users (id, username, password, email, role) VALUES
    (1, 'customer1', '$2y$10$examplehashforcustomer1', 'customer1@email.com', 'customer'),
    (2, 'seller1', '$2y$10$examplehashforseller1', 'seller1@email.com', 'seller'),
    (3, 'admin1', '$2y$10$examplehashforadmin1', 'admin1@email.com', 'admin'),
    (4, 'seller2', '$2y$10$examplehashforseller2', 'seller2@email.com', 'seller');

INSERT INTO products (id, name, description, price, image, stock, category, seller_id) VALUES
    (1, 'Blue T-Shirt', 'Comfortable cotton t-shirt', 299.00, 'blue_tshirt.jpg', 50, 'Clothing', 2),
    (2, 'Red Dress', 'Elegant red dress for all occasions', 799.00, 'red_dress.jpg', 20, 'Clothing', 2),
    (3, 'Wireless Headphones', 'Noise-canceling over-ear headphones', 2500.00, 'headphones.jpg', 15, 'Electronics', 4),
    (4, 'Smart Watch', 'Fitness tracker with heart rate monitor', 1500.00, 'watch.jpg', 30, 'Electronics', 4),
    (5, 'Canvas Backpack', 'Durable backpack for daily use', 850.00, 'backpack.jpg', 10, 'Accessories', 2);

INSERT INTO orders (id, user_id, total, status) VALUES
    (1, 1, 299.00, 'Pending');

INSERT INTO order_items (id, order_id, product_id, quantity, price) VALUES
    (1, 1, 1, 1, 299.00);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

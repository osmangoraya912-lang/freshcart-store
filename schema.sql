-- FreshCart Store - Database schema (MySQL 8 / MariaDB)
-- Run through install.php, or: mysql -u root -p < schema.sql

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) DEFAULT '',
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) DEFAULT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT '',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(30) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(80) NOT NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'cod',
    subtotal DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed categories
INSERT INTO categories (id, name, description) VALUES
(1, 'Electronics', 'Headphones, watches, speakers and gadgets'),
(2, 'Fashion', 'Shoes, wallets, bags and everyday style'),
(3, 'Home & Kitchen', 'Useful products for your home and kitchen')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Seed products
INSERT INTO products (id, category_id, name, description, price, old_price, stock, image, featured) VALUES
(1, 1, 'Wireless Bluetooth Headphones', 'Crystal-clear sound, 30-hour battery life and active noise cancelling. Comfortable over-ear design for work, travel and music.', 4499, 5999, 25, 'assets/images/products/headphones.jpg', 1),
(2, 1, 'Smart Watch Fit Pro', 'Heart-rate and sleep tracking, 7-day battery, water resistant. Pairs with Android and iPhone.', 6999, 8999, 18, 'assets/images/products/smartwatch.jpg', 1),
(3, 2, 'Running Shoes AirFlex', 'Lightweight breathable running shoes with a cushioned sole and strong grip. Perfect for daily runs and gym.', 7499, 9499, 30, 'assets/images/products/shoes.jpg', 1),
(4, 2, 'Leather Wallet Classic', 'Genuine leather bifold wallet with 8 card slots, 2 cash compartments and RFID protection.', 2499, 3299, 40, 'assets/images/products/wallet.jpg', 1),
(5, 3, 'Steel Water Bottle 1L', 'Double-wall insulated stainless steel bottle. Keeps drinks cold for 24 hours or hot for 12 hours.', 1499, 1999, 50, 'assets/images/products/bottle.jpg', 1),
(6, 3, 'LED Desk Lamp', 'Adjustable brightness eye-care LED desk lamp with touch control and a USB charging port.', 2999, 3999, 22, 'assets/images/products/lamp.jpg', 1),
(7, 2, 'Backpack TravelPro 25L', 'Water-resistant 25L backpack with a padded laptop sleeve, USB charging port and anti-theft pocket.', 4999, 6499, 15, 'assets/images/products/backpack.jpg', 1),
(8, 1, 'Bluetooth Speaker Boom Mini', 'Deep bass portable speaker with 12-hour playtime, splash proof body and built-in microphone.', 3499, 4499, 28, 'assets/images/products/speaker.jpg', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price);

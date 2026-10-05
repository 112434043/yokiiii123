CREATE DATABASE IF NOT EXISTS campus_bites;
USE campus_bites;

-- மாணவர்கள் அட்டவணை
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- கேண்டீன் நிலை (Open / Close)
CREATE TABLE IF NOT EXISTS canteen_status (
    id INT PRIMARY KEY,
    is_open TINYINT(1) DEFAULT 1
);
INSERT INTO canteen_status (id, is_open) VALUES (1, 1) ON DUPLICATE KEY UPDATE id=1;

-- மெனு அட்டவணை
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    is_available TINYINT(1) DEFAULT 1
);

INSERT INTO menu_items (name, price, category, is_available) VALUES
('Masala Dosa', 40, 'Breakfast', 1),
('Veg Biryani', 60, 'Lunch', 1),
('Chicken Fried Rice', 70, 'Lunch', 1),
('Idli (2 pcs)', 30, 'Breakfast', 1),
('Tea', 15, 'Snacks', 1),
('Samosa', 20, 'Snacks', 1);

-- ஆர்டர்கள் அட்டவணை
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(20) UNIQUE NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) NOT NULL,
    department VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    pickup_time VARCHAR(20) NOT NULL,
    total_amount INT NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Ready', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
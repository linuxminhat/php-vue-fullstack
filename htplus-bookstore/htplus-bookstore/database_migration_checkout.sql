-- ============================================
-- Migration: Add phone and shipping_address to orders table
-- Run this SQL in your database to add checkout functionality
-- ============================================

-- CÁCH 1: Nếu bảng orders ĐÃ TỒN TẠI, chạy lệnh ALTER TABLE này:
ALTER TABLE orders 
ADD COLUMN phone VARCHAR(20) NULL AFTER customer_id,
ADD COLUMN shipping_address TEXT NULL AFTER phone;

-- CÁCH 2: Nếu bạn muốn TẠO LẠI bảng orders từ đầu, chạy các lệnh sau:
-- (Lưu ý: Điều này sẽ XÓA tất cả dữ liệu orders hiện có!)

-- DROP TABLE IF EXISTS orders;

-- CREATE TABLE orders (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     customer_id INT NOT NULL,
--     phone VARCHAR(20) NULL,
--     shipping_address TEXT NULL,
--     created_by INT NULL,
--     status VARCHAR(50) NOT NULL DEFAULT 'pending',
--     total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
--     FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
-- );

-- ============================================
-- HƯỚNG DẪN CHẠY SQL:
-- ============================================
-- 1. Mở phpMyAdmin hoặc MySQL Workbench
-- 2. Chọn database của bạn
-- 3. Vào tab "SQL"
-- 4. Copy và paste CÁCH 1 (ALTER TABLE) vào
-- 5. Click "Go" hoặc "Execute"
-- ============================================


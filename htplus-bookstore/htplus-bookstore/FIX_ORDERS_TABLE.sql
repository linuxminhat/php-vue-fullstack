-- ============================================
-- CHẠY FILE NÀY ĐỂ SỬA LỖI "Unknown column 'phone'"
-- ============================================

-- Thêm cột phone và shipping_address vào bảng orders
ALTER TABLE orders 
ADD COLUMN phone VARCHAR(20) NULL AFTER customer_id;

ALTER TABLE orders 
ADD COLUMN shipping_address TEXT NULL AFTER phone;

-- Kiểm tra kết quả
SELECT 'Migration completed successfully! ✅' AS status;
DESCRIBE orders;


# 🔧 Cách Bật PHP Fileinfo Extension

## Lỗi: Class "finfo" not found

Lỗi này xảy ra vì PHP extension `fileinfo` chưa được bật.

---

## 🚀 Cách Sửa (Windows)

### Bước 1: Tìm file `php.ini`

1. Mở Command Prompt hoặc PowerShell
2. Chạy lệnh:
```bash
php --ini
```

Sẽ hiện ra đường dẫn file php.ini, ví dụ:
```
Loaded Configuration File: C:\xampp\php\php.ini
```

### Bước 2: Mở và Sửa `php.ini`

1. Mở file `php.ini` bằng Notepad hoặc editor khác
2. Tìm dòng (Ctrl + F):
```ini
;extension=fileinfo
```

3. Xóa dấu `;` ở đầu dòng để bật extension:
```ini
extension=fileinfo
```

### Bước 3: Restart PHP Server

#### Nếu dùng `php -S`:
- Tắt server hiện tại (Ctrl + C)
- Start lại: `php -S localhost:8080 -t public`

#### Nếu dùng XAMPP:
- Restart Apache trong XAMPP Control Panel

#### Nếu dùng WAMP:
- Restart WAMP

---

## ✅ Kiểm Tra

Sau khi restart, kiểm tra bằng cách chạy:

```bash
php -m | findstr fileinfo
```

Nếu thấy `fileinfo` trong danh sách là thành công!

Hoặc tạo file `test.php`:
```php
<?php
if (class_exists('finfo')) {
    echo "✅ Fileinfo extension is enabled!";
} else {
    echo "❌ Fileinfo extension is NOT enabled!";
}
```

---

## 🔄 Thay Thế (Nếu không thể bật fileinfo)

Nếu vẫn không bật được, có thể sửa code để không dùng finfo.
Xem file `FILEUPLOADER_WITHOUT_FINFO.php` để biết cách thay thế.

---

## 📝 Lưu Ý

- Fileinfo extension thường đã được bật sẵn trong PHP 7.x và 8.x
- Nếu dùng hosting, liên hệ provider để bật extension
- Extension này rất quan trọng cho việc xác định MIME type của file upload


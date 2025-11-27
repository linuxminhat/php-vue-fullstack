# HTPLUS Book Store - Hệ thống quản lý cửa hàng sách

## Giới thiệu

HTPLUS Book Store là một hệ thống quản lý và bán sách trực tuyến hoàn chỉnh với phân quyền người dùng (Admin, Staff, Customer). Hệ thống được xây dựng bằng:

- **Backend**: PHP 8.0+ với kiến trúc MVC
- **Frontend**: HTML, CSS, Tailwind CSS, JavaScript (AJAX)
- **Database**: MySQL

## Tính năng

### 1. Hệ thống xác thực (Authentication)
- Đăng nhập / Đăng ký
- Phân quyền người dùng: Admin, Staff, Customer
- Quản lý phiên làm việc

### 2. Trang công khai
- **Trang chủ**: Hiển thị sách nổi bật
- **Trang sản phẩm**: Xem tất cả sách, tìm kiếm, lọc theo SKU/Category

### 3. Dashboard Khách hàng (Customer)
- **Mua sắm**: 
  - Xem và tìm kiếm sản phẩm
  - Lọc theo danh mục
  - Thêm vào giỏ hàng
- **Giỏ hàng**:
  - Quản lý sản phẩm trong giỏ
  - Cập nhật số lượng
  - Đặt hàng
- **Đơn hàng**:
  - Xem danh sách đơn hàng
  - Theo dõi trạng thái
  - Xem chi tiết đơn hàng
- **Lịch sử**: Xem lịch sử mua hàng đã hoàn thành

### 4. Dashboard Nhân viên (Staff)
- **Quản lý sản phẩm**:
  - Xem danh sách sản phẩm
  - Thêm/Sửa/Xóa sản phẩm
  - Tìm kiếm sản phẩm
- **Quản lý đơn hàng**:
  - Xem tất cả đơn hàng
  - Cập nhật trạng thái đơn hàng
  - Xem chi tiết đơn hàng

### 5. Dashboard Admin
- **Thống kê tổng quan**:
  - Tổng sản phẩm
  - Tổng đơn hàng
  - Tổng danh mục
  - Doanh thu
- **Quản lý người dùng**:
  - Tạo tài khoản mới (Admin/Staff/Customer)
- **Quản lý danh mục**:
  - CRUD danh mục sản phẩm
- **Quản lý sản phẩm**:
  - CRUD sản phẩm
  - Theo dõi tồn kho
- **Quản lý đơn hàng**:
  - Xem tất cả đơn hàng
  - Cập nhật trạng thái (pending/completed/cancelled)
  - Xem chi tiết đơn hàng
- **Báo cáo & Thống kê**:
  - Thống kê theo trạng thái đơn hàng
  - Top sản phẩm bán chạy
  - Log hệ thống (demo)

## Cấu trúc thư mục

```
simple-store/
├── app/
│   ├── Controllers/          # Controllers xử lý logic
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   └── HomeController.php
│   ├── Core/                 # Core framework
│   │   ├── Auth.php
│   │   ├── BaseController.php
│   │   ├── BaseModel.php
│   │   ├── Config.php
│   │   ├── Database.php
│   │   └── Router.php
│   └── Models/               # Models tương tác database
│       ├── User.php
│       ├── Category.php
│       ├── Product.php
│       ├── Order.php
│       └── OrderItem.php
├── public/                   # Frontend files
│   ├── assets/
│   │   └── js/
│   │       ├── api.js       # API helper functions
│   │       └── auth.js      # Authentication manager
│   ├── index.html           # Trang chủ
│   ├── login.html           # Đăng nhập
│   ├── register.html        # Đăng ký
│   ├── products.html        # Danh sách sản phẩm
│   ├── customer-dashboard.html   # Dashboard khách hàng
│   ├── staff-dashboard.html      # Dashboard nhân viên
│   ├── admin-dashboard.html      # Dashboard admin
│   └── index.php            # Entry point backend
├── config/                  # Configuration files
├── vendor/                  # Composer dependencies
└── composer.json
```

## Cài đặt

### Yêu cầu
- PHP >= 8.0
- MySQL >= 5.7
- Composer

### Các bước cài đặt

1. **Clone hoặc copy project**

2. **Cài đặt dependencies**
```bash
composer install
```

3. **Tạo database**
```sql
CREATE DATABASE simple_store;
USE simple_store;

-- Tạo bảng users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    role ENUM('admin', 'staff', 'customer') DEFAULT 'customer',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng products
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    publisher VARCHAR(255),
    isbn VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Tạo bảng orders
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    staff_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

-- Tạo bảng order_items
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

4. **Tạo file .env** (hoặc config) với thông tin database
```
DB_HOST=localhost
DB_NAME=simple_store
DB_USER=root
DB_PASS=
```

5. **Tạo dữ liệu mẫu**
```sql
-- Tạo users demo
INSERT INTO users (email, password, full_name, role) VALUES
('admin@htplus.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin'),
('staff@htplus.com', '$2y$10$YourHashedPasswordHere', 'Staff User', 'staff'),
('customer@htplus.com', '$2y$10$YourHashedPasswordHere', 'Customer User', 'customer');

-- Tạo categories
INSERT INTO categories (name) VALUES
('Văn học'),
('Kinh tế'),
('Kỹ năng sống'),
('Công nghệ'),
('Thiếu nhi');

-- Tạo products mẫu
INSERT INTO products (category_id, sku, name, author, publisher, isbn, price, stock) VALUES
(1, 'VH001', 'Nhà Giả Kim', 'Paulo Coelho', 'NXB Hội Nhà Văn', '978-604-2-00000-1', 79000, 50),
(2, 'KT001', 'Sapiens', 'Yuval Noah Harari', 'NXB Thế Giới', '978-604-2-00000-2', 189000, 30),
(3, 'KN001', 'Đắc Nhân Tâm', 'Dale Carnegie', 'NXB Tổng hợp', '978-604-2-00000-3', 99000, 100);
```

6. **Chạy server PHP**
```bash
cd public
php -S localhost:8000
```

7. **Truy cập ứng dụng**
- Mở trình duyệt: `http://localhost:8000`

## Tài khoản demo

Để tạo tài khoản demo, sử dụng script `generate_hash.php`:

```bash
php generate_hash.php
```

**Tài khoản mặc định**:
- Admin: `admin@htplus.com` / `admin123`
- Staff: `staff@htplus.com` / `staff123`
- Customer: `customer@htplus.com` / `customer123`

## API Endpoints

### Authentication
- `POST /auth/login` - Đăng nhập
- `POST /auth/logout` - Đăng xuất
- `GET /auth/getAuthMe` - Lấy thông tin user hiện tại
- `POST /auth/createUser` - Tạo user mới

### Categories (Admin)
- `GET /admin/categories` - Danh sách categories
- `POST /admin/categories/create` - Tạo category
- `POST /admin/categories/update` - Cập nhật category
- `POST /admin/categories/delete` - Xóa category

### Products (Admin/Staff)
- `GET /admin/products` - Danh sách products
- `POST /admin/products/create` - Tạo product
- `POST /admin/products/update` - Cập nhật product
- `POST /admin/products/delete` - Xóa product

### Orders (Customer)
- `POST /orders` - Tạo đơn hàng
- `GET /orders/my` - Danh sách đơn hàng của tôi
- `GET /orders/my/detail?id=` - Chi tiết đơn hàng

### Orders (Admin/Staff)
- `POST /admin/orders/create` - Tạo đơn cho customer
- `GET /admin/orders` - Danh sách tất cả đơn hàng
- `GET /admin/orders/detail?id=` - Chi tiết đơn hàng
- `POST /admin/orders/update-status` - Cập nhật trạng thái

## Công nghệ sử dụng

### Backend
- PHP 8.0+
- PDO (MySQL)
- Session-based authentication
- MVC Architecture

### Frontend
- HTML5
- Tailwind CSS 3.x (CDN)
- Vanilla JavaScript
- AJAX (Fetch API)
- LocalStorage (giỏ hàng, auth)

## Tính năng nổi bật

1. **Single Page Application (SPA) style**: Sử dụng AJAX để load dữ liệu động
2. **Responsive Design**: Tương thích mọi thiết bị
3. **Real-time Updates**: Cập nhật dữ liệu không cần reload trang
4. **Shopping Cart**: Giỏ hàng lưu trên LocalStorage
5. **Role-based Access Control**: Phân quyền rõ ràng theo vai trò
6. **Beautiful UI**: Giao diện hiện đại với Tailwind CSS

## Lưu ý

- Hệ thống sử dụng session để quản lý authentication
- Giỏ hàng được lưu trên LocalStorage của browser
- Tất cả API đều trả về JSON format
- Frontend sử dụng AJAX để giao tiếp với backend

## Tác giả

HTPLUS Development Team

## License

MIT License


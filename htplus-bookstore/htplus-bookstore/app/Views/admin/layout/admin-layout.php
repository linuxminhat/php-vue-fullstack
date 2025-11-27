<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="flex min-h-screen bg-gray-100">

    <div class="w-84 bg-[#0F1E34] text-white flex flex-col shadow-lg">
        <div class="px-6 py-4 text-2xl font-bold border-b border-blue-900">
            HTPLUS - Book Store
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/admin/users"
               class="block px-4 py-3 rounded hover:bg-blue-600 transition">
                👤 Quản lý người dùng hệ thống
            </a>
            <a href="/admin/categories"
               class="block px-4 py-3 hover:bg-blue-800 rounded transition">
                🗂 Quản lý danh mục sản phẩm
            </a>
            <a href="/admin/products"
               class="block px-4 py-3 hover:bg-blue-800 rounded transition">
                🛍️ Quản lý sản phẩm
            </a>
            <a href="/admin/orders"
               class="block px-4 py-3 hover:bg-blue-800 rounded transition">
                📦 Quản lý đơn hàng
            </a>
            <a href="/admin/logs"
               class="block px-4 py-3 hover:bg-blue-800 rounded transition">
                📜 Nhật ký hệ thống
            </a>
        </nav>
    </div>
    <div class="flex-1 p-8">
        <?= $content ?>      
    </div>
</div>

</body>
</html>

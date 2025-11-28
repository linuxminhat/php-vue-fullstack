<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Staff Panel - HTPLUS Book Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="flex min-h-screen bg-gray-100">

    <!-- Sidebar for Staff (Limited Menu) -->
    <div class="w-84 bg-[#0F1E34] text-white flex flex-col shadow-lg">
        <div class="px-6 py-4 text-2xl font-bold border-b border-blue-900">
            HTPLUS - Staff Panel
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/staff/products"
               class="block px-4 py-3 rounded hover:bg-blue-600 transition">
                🛍️ Quản lý sản phẩm
            </a>
            <a href="/staff/orders"
               class="block px-4 py-3 hover:bg-blue-800 rounded transition">
                📦 Quản lý đơn hàng
            </a>
            <div class="mt-8 pt-8 border-t border-blue-800">
                <a href="/"
                   class="block px-4 py-3 hover:bg-blue-800 rounded transition">
                    🏠 Back to Home
                </a>
                <a href="/auth/logout"
                   class="block px-4 py-3 hover:bg-red-700 rounded transition text-red-300">
                    🚪 Logout
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content Area -->
    <div class="flex-1 p-8">
        <?= $content ?>      
    </div>
</div>

</body>
</html>


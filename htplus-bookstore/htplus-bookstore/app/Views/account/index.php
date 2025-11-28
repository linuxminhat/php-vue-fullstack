<?php use App\Core\View; ?>
<div class="w-full h-48 overflow-hidden">
    <img 
        src="<?= View::asset('app-image/background-book-store.jpg') ?>" 
        alt="Books banner" 
        class="w-full h-full object-cover"
    >
</div>

<div class="max-w-6xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">Account Page</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      
    <!-- Sidebar Navigation -->
    <aside class="md:col-span-1">
        <nav class="bg-gray-50 rounded-lg overflow-hidden">
            <a href="/account?tab=dashboard" 
           class="block px-6 py-4 <?= (!isset($_GET['tab']) || $_GET['tab'] === 'dashboard') ? 'bg-green-400 text-white font-semibold' : 'hover:bg-gray-100' ?>">
            DASHBOARD
            </a>
        
        <a href="/account?tab=orders" 
           class="block px-6 py-4 <?= (isset($_GET['tab']) && $_GET['tab'] === 'orders') ? 'bg-green-400 text-white font-semibold' : 'hover:bg-gray-100' ?>">
            ORDERS
        </a>
        
        <a href="/account?tab=details" 
           class="block px-6 py-4 <?= (isset($_GET['tab']) && $_GET['tab'] === 'details') ? 'bg-green-400 text-white font-semibold' : 'hover:bg-gray-100' ?>">
            ACCOUNT DETAILS
        </a>
        
        <a href="/auth/logout" 
           class="block px-6 py-4 hover:bg-gray-100 text-red-600">
            LOGOUT
        </a>
    </nav>
</aside>
        
<!-- Main Content Area -->
<main class="md:col-span-3">
    
    <?php 
    // Lấy tab hiện tại, mặc định là 'dashboard'
    $currentTab = $_GET['tab'] ?? 'dashboard';
    ?>
    
    <?php if ($currentTab === 'dashboard'): ?>
        <!-- DASHBOARD TAB -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Dashboard</h2>
            
            <p class="mb-4">
                Hello <strong><?= View::e($user->full_name ?? 'User') ?></strong> 
                (not <strong><?= View::e($user->full_name ?? 'User') ?></strong>? 
                <a href="/auth/logout" class="text-green-600 underline">Log out</a>)
            </p>
            
            <p class="text-gray-600">
                From your account dashboard you can view your 
                <a href="/account?tab=orders" class="text-green-600 underline">recent orders</a>, 
                and 
                <a href="/account?tab=details" class="text-green-600 underline">edit your password and account details</a>.
            </p>
        </div>
        
        <?php elseif ($currentTab === 'orders'): ?>
    <!-- ORDERS TAB -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-6">Orders</h2>
        
        <!-- Bảng đơn hàng -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-semibold">Order</th>
                        <th class="py-3 px-4 font-semibold">Date</th>
                        <th class="py-3 px-4 font-semibold">Phone</th>
                        <th class="py-3 px-4 font-semibold">Shipping Address</th>
                        <th class="py-3 px-4 font-semibold">Status</th>
                        <th class="py-3 px-4 font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                    <tr class="border-b border-gray-100">
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            <p class="text-lg mb-2">No orders yet.</p>
                            <a href="/products" class="text-green-600 underline">Browse products</a>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <!-- Order ID -->
                            <td class="py-4 px-4">
                                <a href="/orders/my" class="text-green-600 hover:underline font-medium">
                                    Đơn hàng #<?= str_pad((string)$order->id, 6, '0', STR_PAD_LEFT) ?>
                                </a>
                            </td>
                            
                            <!-- Date -->
                            <td class="py-4 px-4 text-gray-700">
                                <?= date('d/m/Y', strtotime($order->created_at)) ?>
                            </td>
                            
                            <!-- Phone -->
                            <td class="py-4 px-4 text-gray-700">
                                <?= View::e($order->phone ?? '-') ?>
                            </td>
                            
                            <!-- Shipping Address -->
                            <td class="py-4 px-4 text-gray-700 max-w-xs">
                                <div class="truncate" title="<?= View::e($order->shipping_address ?? '-') ?>">
                                    <?= View::e($order->shipping_address ?? '-') ?>
                                </div>
                            </td>
                            
                            <!-- Status -->
                            <td class="py-4 px-4">
                                <?php
                                $statusConfig = [
                                    'pending' => ['label' => 'Chờ xác nhận', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-300'],
                                    'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'bg-blue-100 text-blue-800 border-blue-300'],
                                    'shipping' => ['label' => 'Đang giao hàng', 'class' => 'bg-purple-100 text-purple-800 border-purple-300'],
                                    'shipped' => ['label' => 'Đang giao hàng', 'class' => 'bg-purple-100 text-purple-800 border-purple-300'],
                                    'delivered' => ['label' => 'Đã giao hàng', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-300'],
                                    'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-green-100 text-green-800 border-green-300'],
                                    'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-100 text-red-800 border-red-300'],
                                ];
                                $status = $statusConfig[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'bg-gray-100 text-gray-800 border-gray-300'];
                                ?>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold border <?= $status['class'] ?>">
                                    <?= $status['label'] ?>
                                </span>
                            </td>
                            
                            <!-- Total -->
                            <td class="py-4 px-4 font-semibold text-green-600">
                                <?= View::currency($order->total_amount) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($orders)): ?>
        <div class="mt-6 text-center">
            <a href="/orders/my" class="inline-block px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition">
                View All Orders Details
            </a>
        </div>
        <?php endif; ?>
    </div>
        
    <?php elseif ($currentTab === 'details'): ?>
    <!-- ACCOUNT DETAILS TAB -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-6">Account Details</h2>
        
        <!-- Thông báo thành công/lỗi -->
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                ✓ <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                ✗ <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Form 1: Cập nhật Full Name -->
        <form method="POST" action="/account/update-profile" class="mb-8 pb-8 border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Profile Information</h3>
            
            <div class="space-y-4">
                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="full_name" 
                        value="<?= View::e($user->full_name ?? '') ?>"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Email Address</label>
                    <input 
                        type="email" 
                        value="<?= View::e($user->email) ?>"
                        disabled
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 text-gray-500 cursor-not-allowed"
                    >
                    <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                </div>
            </div>
            
            <button 
                type="submit"
                class="mt-4 px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-md text-sm transition">
                Save Changes
            </button>
        </form>
        
        <!-- Form 2: Đổi Password -->
        <form method="POST" action="/account/change-password" class="mt-8">
            <h3 class="text-lg font-semibold mb-4">Change Password</h3>
            
            <div class="space-y-4">
                <!-- Current Password -->
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Current Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="current_password"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Enter current password"
                    >
                </div>
                
                <!-- New Password -->
                <div>
                    <label class="block text-sm font-medium mb-1">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="new_password"
                        required
                        minlength="6"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Minimum 6 characters"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="confirmed_password"
                        required
                        minlength="6"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Re-enter new password"
                    >
                </div>
            </div>
            
            <button 
                type="submit"
                class="mt-4 px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-md text-sm transition">
                Change Password
            </button>
        </form>
    </div>
        
    <?php else: ?>
        <!-- Default fallback -->
        <p>Tab không tồn tại</p>
    <?php endif; ?>
    
</main>
        
    </div>
</div>
<?php use App\Core\View; ?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4">
        
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Đơn Hàng Của Tôi</h1>
            <p class="text-gray-600 mt-2">Quản lý và theo dõi đơn hàng của bạn</p>
        </div>

        <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="mb-4">
                    <span class="text-6xl">📦</span>
                </div>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Chưa có đơn hàng nào</h2>
                <p class="text-gray-500 mb-6">Bạn chưa đặt hàng. Hãy khám phá và mua sắm ngay!</p>
                <a href="/products" class="inline-block bg-orange-600 hover:bg-orange-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                    Mua sắm ngay
                </a>
            </div>
        <?php else: ?>
            <!-- Orders List -->
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden">
                    
                    <!-- Order Header -->
                    <div class="bg-gradient-to-r from-orange-50 to-white border-b px-6 py-4">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Mã đơn hàng</p>
                                    <p class="font-semibold text-gray-800">#<?= str_pad($order->id, 6, '0', STR_PAD_LEFT) ?></p>
                                </div>
                                <div class="h-10 w-px bg-gray-300"></div>
                                <div>
                                    <p class="text-sm text-gray-500">Ngày đặt</p>
                                    <p class="font-medium text-gray-800"><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></p>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div>
                                <?php
                                $statusConfig = [
                                    'pending' => ['label' => 'Chờ xác nhận', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-300'],
                                    'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'bg-blue-100 text-blue-800 border-blue-300'],
                                    'shipping' => ['label' => 'Đang giao', 'class' => 'bg-purple-100 text-purple-800 border-purple-300'],
                                    'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-green-100 text-green-800 border-green-300'],
                                    'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-100 text-red-800 border-red-300'],
                                ];
                                $status = $statusConfig[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'bg-gray-100 text-gray-800 border-gray-300'];
                                ?>
                                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold border <?= $status['class'] ?>">
                                    <?= $status['label'] ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Body -->
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Shipping Info -->
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <span class="mr-2">📍</span>
                                    Thông tin giao hàng
                                </h3>
                                <div class="space-y-2 text-sm">
                                    <?php if ($order->phone): ?>
                                    <div class="flex items-start">
                                        <span class="text-gray-500 w-24 flex-shrink-0">Số điện thoại:</span>
                                        <span class="text-gray-800 font-medium"><?= View::e($order->phone) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($order->shipping_address): ?>
                                    <div class="flex items-start">
                                        <span class="text-gray-500 w-24 flex-shrink-0">Địa chỉ:</span>
                                        <span class="text-gray-800"><?= View::e($order->shipping_address) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <span class="mr-2">💰</span>
                                    Tổng đơn hàng
                                </h3>
                                <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Tổng tiền:</span>
                                        <span class="text-2xl font-bold text-orange-600">
                                            <?= View::currency($order->total_amount) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="text-sm text-gray-500">
                            <?php if ($order->status === 'pending'): ?>
                                <span class="flex items-center">
                                    <span class="mr-2">⏳</span>
                                    Đơn hàng đang chờ xác nhận
                                </span>
                            <?php elseif ($order->status === 'shipping'): ?>
                                <span class="flex items-center">
                                    <span class="mr-2">🚚</span>
                                    Đơn hàng đang được giao đến bạn
                                </span>
                            <?php elseif ($order->status === 'completed'): ?>
                                <span class="flex items-center text-green-600">
                                    <span class="mr-2">✓</span>
                                    Đã giao hàng thành công
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="/orders/detail/<?= $order->id ?>" 
                               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-sm font-medium">
                                Xem chi tiết
                            </a>
                            
                            <?php if ($order->status === 'completed'): ?>
                            <button class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition text-sm font-medium">
                                Mua lại
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination or Load More -->
            <?php if (count($orders) >= 10): ?>
            <div class="mt-6 text-center">
                <button class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Xem thêm đơn hàng
                </button>
            </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>


<?php use App\Core\View; ?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/orders/my" class="inline-flex items-center text-gray-600 hover:text-gray-800 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại danh sách đơn hàng
            </a>
        </div>

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Đơn hàng #<?= str_pad((string)$order->id, 6, '0', STR_PAD_LEFT) ?>
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">
                        Đặt ngày: <?= date('d/m/Y H:i', strtotime($order->created_at)) ?>
                    </p>
                </div>
                
                <!-- Status Badge -->
                <div>
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
                    <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold border <?= $status['class'] ?>">
                        <?= $status['label'] ?>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shipping Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Thông tin giao hàng
                    </h3>
                    <div class="space-y-2 text-sm">
                        <?php if ($order->phone): ?>
                        <div>
                            <span class="text-gray-500">Số điện thoại:</span>
                            <span class="ml-2 text-gray-800 font-medium"><?= View::e($order->phone) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order->shipping_address): ?>
                        <div>
                            <span class="text-gray-500">Địa chỉ:</span>
                            <p class="mt-1 text-gray-800"><?= View::e($order->shipping_address) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Tổng tiền đơn hàng
                    </h3>
                    <div class="text-center py-2">
                        <p class="text-3xl font-bold text-orange-600">
                            <?= View::currency($order->total_amount) ?>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">(Đã bao gồm VAT)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Sản phẩm trong đơn hàng
            </h2>

            <?php if (empty($items)): ?>
                <p class="text-gray-500 text-center py-8">Không có sản phẩm nào trong đơn hàng này.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($items as $item): ?>
                    <div class="flex items-start gap-4 pb-4 border-b border-gray-200 last:border-0">
                        <!-- Product Image (if available) -->
                        <div class="w-20 h-24 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>

                        <!-- Product Info -->
                        <div class="flex-grow">
                            <h3 class="font-semibold text-gray-800">
                                Sản phẩm ID: <?= $item->product_id ?>
                            </h3>
                            <div class="mt-2 space-y-1 text-sm">
                                <div class="flex items-center text-gray-600">
                                    <span>Số lượng:</span>
                                    <span class="ml-2 font-semibold text-gray-800">x<?= $item->quantity ?></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <span>Đơn giá:</span>
                                    <span class="ml-2 font-semibold text-gray-800">
                                        <?= View::currency($item->price_at_purchase) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Line Total -->
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm text-gray-500 mb-1">Thành tiền</p>
                            <p class="text-lg font-bold text-orange-600">
                                <?= View::currency($item->line_total) ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Total Summary -->
                <div class="mt-6 pt-6 border-t border-gray-300">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-700">Tổng cộng:</span>
                        <span class="text-2xl font-bold text-orange-600">
                            <?= View::currency($order->total_amount) ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Timeline (Optional) -->
        <?php if ($order->status !== 'cancelled'): ?>
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Trạng thái đơn hàng</h2>
            
            <div class="relative">
                <!-- Timeline -->
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            ✓
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800">Đơn hàng đã đặt</p>
                            <p class="text-sm text-gray-600"><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></p>
                        </div>
                    </div>

                    <?php if (in_array($order->status, ['confirmed', 'shipping', 'shipped', 'delivered', 'completed'])): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            ✓
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800">Đã xác nhận</p>
                            <p class="text-sm text-gray-600">Đơn hàng đã được xác nhận</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (in_array($order->status, ['shipping', 'shipped', 'delivered', 'completed'])): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            ✓
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800">Đang giao hàng</p>
                            <p class="text-sm text-gray-600">Đơn hàng đang trên đường giao đến bạn</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (in_array($order->status, ['delivered', 'completed'])): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            ✓
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800">Đã giao hàng</p>
                            <p class="text-sm text-gray-600">Đơn hàng đã được giao đến bạn</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($order->status === 'completed'): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                            ✓
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800">Hoàn thành</p>
                            <p class="text-sm text-gray-600">Đơn hàng đã hoàn tất</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="mt-6 flex gap-3 justify-center">
            <a href="/orders/my" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium">
                Quay lại danh sách
            </a>
            <?php if ($order->status === 'completed'): ?>
            <button class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium">
                Mua lại đơn hàng
            </button>
            <?php endif; ?>
        </div>

    </div>
</div>


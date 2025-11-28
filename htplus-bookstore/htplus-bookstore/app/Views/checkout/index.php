<?php use App\Core\View; ?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4">
        
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
            <h1 class="text-2xl font-bold text-orange-600">
                <span class="mr-2">📦</span> Checkout
            </h1>
        </div>

        <form id="checkout-form" method="POST">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Shipping Info + Products -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Shipping Address Section -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <span class="text-xl mr-2">📍</span>
                            <h2 class="text-lg font-semibold text-red-600">Địa Chỉ Nhận Hàng</h2>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- User Info Display -->
                            <div class="flex items-center text-gray-700">
                                <span class="font-semibold mr-2"><?= View::e($user->full_name ?? 'Khách hàng') ?></span>
                                <span class="text-gray-400">|</span>
                                <span class="ml-2"><?= View::e($user->email) ?></span>
                            </div>

                            <!-- Phone Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Số điện thoại <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    id="phone"
                                    placeholder="Nhập số điện thoại" 
                                    required
                                    pattern="[0-9]{10,11}"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                />
                                <p class="text-xs text-gray-500 mt-1">Vui lòng nhập 10-11 chữ số</p>
                            </div>

                            <!-- Address Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Địa chỉ nhận hàng <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="shipping_address" 
                                    id="shipping_address"
                                    rows="3"
                                    placeholder="Nhập địa chỉ đầy đủ: Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition resize-none"
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-1">Ví dụ: 123 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</p>
                            </div>
                        </div>
                    </div>

                    <!-- Products Section -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4 pb-3 border-b">
                            Sản Phẩm
                        </h2>

                        <!-- Product List -->
                        <div class="space-y-4">
                            <?php foreach ($items as $item): ?>
                            <div class="flex gap-4 pb-4 border-b last:border-b-0">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <img src="<?= View::e($item['image']) ?>" 
                                         alt="<?= View::e($item['name']) ?>"
                                         class="w-20 h-24 object-cover rounded border">
                                </div>

                                <!-- Product Info -->
                                <div class="flex-grow">
                                    <h3 class="font-medium text-gray-800 mb-1 line-clamp-2">
                                        <?= View::e($item['name']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-2">
                                        Số lượng: x<?= $item['quantity'] ?>
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-400 line-through">
                                            <?= View::currency($item['price'] * 1.2) ?>
                                        </span>
                                        <span class="text-orange-600 font-semibold">
                                            <?= View::currency($item['price']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <span class="text-xl mr-2">💳</span>
                            <h2 class="text-lg font-semibold">Phương Thức Thanh Toán</h2>
                        </div>
                        
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-orange-500 rounded-lg cursor-pointer bg-orange-50">
                                <input type="radio" name="payment_method" value="cod" checked class="w-5 h-5 text-orange-600">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-800">Thanh toán khi nhận hàng (COD)</p>
                                    <p class="text-sm text-gray-500">Thanh toán bằng tiền mặt khi nhận hàng</p>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-not-allowed opacity-50">
                                <input type="radio" name="payment_method" value="banking" disabled class="w-5 h-5">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-800">Chuyển khoản ngân hàng</p>
                                    <p class="text-sm text-gray-500">Tính năng đang phát triển</p>
                                </div>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Order Summary (Sticky) -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                        <h2 class="text-lg font-semibold mb-4 pb-3 border-b">
                            Tổng Đơn Hàng
                        </h2>

                        <div class="space-y-3 mb-4">
                            <!-- Subtotal -->
                            <div class="flex justify-between text-gray-600">
                                <span>Tạm tính (<?= count($items) ?> sản phẩm):</span>
                                <span><?= View::currency($total) ?></span>
                            </div>

                            <!-- Shipping -->
                            <div class="flex justify-between text-gray-600">
                                <span>Phí vận chuyển:</span>
                                <span class="text-green-600">Miễn phí</span>
                            </div>

                            <!-- Discount -->
                            <div class="flex justify-between text-gray-600">
                                <span>Giảm giá:</span>
                                <span>-<?= View::currency(0) ?></span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200 mb-6">
                            <span class="text-lg font-semibold">Tổng cộng:</span>
                            <span class="text-2xl font-bold text-orange-600">
                                <?= View::currency($total) ?>
                            </span>
                        </div>

                        <!-- Checkout Button -->
                        <button 
                            type="submit"
                            id="place-order-btn"
                            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-4 rounded-lg transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0">
                            Đặt Hàng
                        </button>

                        <!-- Note -->
                        <p class="text-xs text-gray-500 text-center mt-4">
                            Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo 
                            <a href="/terms" class="text-orange-600 hover:underline">Điều khoản</a> 
                            của HTPLUS Book Store
                        </p>

                        <!-- Security Badge -->
                        <div class="mt-6 pt-4 border-t text-center">
                            <div class="flex items-center justify-center text-gray-500 text-sm">
                                <span class="mr-2">🔒</span>
                                <span>Thanh toán an toàn & bảo mật</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-8 max-w-md w-full text-center transform transition-all">
        <div class="mb-4">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                <span class="text-4xl">✓</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Đặt hàng thành công!</h3>
        <p class="text-gray-600 mb-6">
            Cảm ơn bạn đã mua hàng tại HTPLUS Book Store. 
            Chúng tôi sẽ liên hệ với bạn sớm nhất!
        </p>
        <div class="flex gap-3">
            <a href="/orders/my" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 rounded-lg transition">
                Xem đơn hàng
            </a>
            <a href="/products" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 rounded-lg transition">
                Tiếp tục mua
            </a>
        </div>
    </div>
</div>

<script>
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('shipping_address').value.trim();
    const submitBtn = document.getElementById('place-order-btn');
    
    // Validation
    if (!phone || !address) {
        alert('Vui lòng điền đầy đủ thông tin giao hàng!');
        return;
    }

    if (!/^[0-9]{10,11}$/.test(phone)) {
        alert('Số điện thoại không hợp lệ. Vui lòng nhập 10-11 chữ số!');
        return;
    }

    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Đang xử lý...';

    // Submit order
    fetch('/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            phone: phone,
            shipping_address: address
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Đặt Hàng';
            return;
        }

        // Show success modal
        document.getElementById('success-modal').classList.remove('hidden');
        
        // Update cart count
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            cartCountEl.innerText = '0';
        }
        
        // Redirect after 3 seconds
        setTimeout(() => {
            window.location.href = '/orders/my';
        }, 3000);
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi kết nối, vui lòng thử lại!');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Đặt Hàng';
    });
});
</script>


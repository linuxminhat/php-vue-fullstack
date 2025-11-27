<?php use App\Core\View; ?>

<div class="max-w-6xl mx-auto px-4 py-16 grid grid-cols-1 md:grid-cols-2 gap-10">

    <!-- PRODUCT IMAGE -->
    <div>
        <img src="<?= View::e($product->image) ?>"
             alt="<?= View::e($product->name) ?>"
             class="w-full object-cover rounded shadow">
    </div>

    <!-- PRODUCT INFO -->
    <div>
        <h1 class="text-3xl font-semibold mb-2"><?= View::e($product->name) ?></h1>

        <?php if ($product->author): ?>
        <p class="text-gray-600 mb-4">by <?= View::e($product->author) ?></p>
        <?php endif; ?>

        <p class="font-medium text-2xl text-green-600 mb-4">
            <?= View::currency($product->price) ?>
        </p>

        <p class="text-gray-700 leading-relaxed mb-6">
            <?= nl2br(View::e($product->description ?? "No description available.")) ?>
        </p>

 
        <div class="flex items-center gap-4 mt-6">

 
            <input id="qty"
                   type="number"
                   min="1"
                   value="1"
                   class="w-20 border rounded px-3 py-2 text-center shadow">

     
            <button onclick="addToCartDetail()"
                    data-product-id="<?= $product->id ?>"
                    data-product-price="<?= $product->price ?>"
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded shadow">
                ✔ Add To Cart
            </button>

 
            <button onclick="orderNow()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded shadow">
                ⚡ Order
            </button>

        </div>
    </div>
</div>

<script>
function addToCartDetail() {
    const qty = parseInt(document.getElementById('qty').value) || 1;
    const productId = <?= $product->id ?>;
    const productPrice = <?= json_encode($product->price) ?>;
    
    fetch('/cart/add', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            product_id: productId,
            quantity: qty,
            price: parseFloat(productPrice)
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Có lỗi xảy ra');
            return;
        }
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl && data.cart_count) {
            cartCountEl.innerText = data.cart_count;
        }
        alert('Đã thêm vào giỏ hàng!');
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi kết nối, vui lòng thử lại!');
    });
}

function orderNow() {
    const qty = document.getElementById('qty').value || 1;
    const productId = <?= $product->id ?>;
    window.location.href = `/checkout?product=${productId}&qty=${qty}`;
}
</script>

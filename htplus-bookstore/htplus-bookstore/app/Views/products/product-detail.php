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

<!-- You might also like section -->
<?php if (!empty($related_products)): ?>
<section class="bg-gray-50 py-16">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-bold mb-8">You might also like</h2>
        
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($related_products as $relatedProduct): ?>
                <article class="relative group">
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-xl hover:-translate-y-2 transition p-3">
                        <!-- Hardcover badge -->
                        <div class="relative mb-3">
                            <div class="absolute -top-3 left-0 bg-green-400 text-white text-[11px] px-3 py-1 rounded-sm z-10">
                                HARDCOVER
                            </div>
                            
                            <!-- Wishlist button -->
                            <button class="absolute top-2 right-2 w-8 h-8 rounded-full border bg-white flex items-center justify-center text-green-500 text-lg hover:bg-green-50 transition z-10">
                                ♡
                            </button>

                            <!-- Book image -->
                            <a href="/product/<?= $relatedProduct->id ?>">
                                <div class="bg-gray-50 border flex items-center justify-center aspect-[3/4] overflow-hidden">
                                    <?php if (!empty($relatedProduct->image)): ?>
                                        <img src="<?= View::e($relatedProduct->image) ?>"
                                             alt="<?= View::e($relatedProduct->name) ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition">
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">No image</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>

                        <!-- Book info -->
                        <a href="/product/<?= $relatedProduct->id ?>">
                            <h3 class="font-semibold text-sm mb-1 hover:text-green-600 transition line-clamp-2">
                                <?= View::e($relatedProduct->name) ?>
                            </h3>
                        </a>

                        <?php if (!empty($relatedProduct->author)): ?>
                            <p class="text-xs text-gray-500 mb-2">
                                By <?= View::e($relatedProduct->author) ?>
                            </p>
                        <?php endif; ?>

                        <p class="text-sm font-bold text-gray-800 mb-3">
                            <?= View::currency($relatedProduct->price) ?>
                        </p>

                        <!-- Add to cart button -->
                        <button
                            onclick="addToCart(<?= $relatedProduct->id ?>)"
                            data-product-id="<?= $relatedProduct->id ?>"
                            data-product-price="<?= $relatedProduct->price ?>"
                            class="w-full bg-green-400 hover:bg-green-500 text-white text-xs font-semibold py-2 flex items-center justify-center gap-2 rounded transition">
                            <span>🛒</span><span>Add To Cart</span>
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

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

// Add to cart for related products
function addToCart(productId) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            product_id: productId,
            quantity: 1,
            price: parseFloat(event.target.closest('button').dataset.productPrice)
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
</script>

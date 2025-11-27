<?php use App\Core\View; ?>

<div class="max-w-6xl mx-auto px-4 py-12">

    <h1 class="text-3xl font-bold mb-6">Your Cart</h1>

    <?php if (empty($items)): ?>
        <p class="text-gray-600">Your cart is empty.</p>
        <a href="/products" class="text-green-600 underline">Continue shopping</a>
    <?php else: ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- LIST ITEMS -->
            <div class="md:col-span-2 space-y-4">

                <?php foreach ($items as $item): ?>
                <div class="flex bg-white shadow rounded p-4">

                    <img src="<?= View::e($item['image']) ?>"
                         class="w-28 h-32 object-cover rounded mr-4">

                    <div class="flex-grow">
                        <h2 class="text-lg font-semibold"><?= View::e($item['name']) ?></h2>

                        <p class="text-green-600 font-medium">
                            <?= View::currency($item['price']) ?>
                        </p>

                        <div class="flex items-center mt-3">

                            <!-- Quantity -->
                            <input type="number" min="1"
                                   value="<?= $item['quantity'] ?>"
                                   class="w-20 border px-2 py-1 rounded text-center"
                                   onchange="updateQty(<?= $item['cart_item_id'] ?>, this.value)">

                            <!-- Remove -->
                            <button onclick="removeItem(<?= $item['cart_item_id'] ?>)"
                                    class="ml-4 text-red-600 hover:underline">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>

            <!-- TOTAL -->
            <div class="bg-white shadow rounded p-6 h-fit sticky top-6">
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

                <p class="flex justify-between text-lg mb-4">
                    <span>Total:</span>
                    <span class="font-bold text-green-600">
                        <?= View::currency($total) ?>
                    </span>
                </p>

                <a href="/checkout"
                   class="block bg-blue-600 text-white text-center py-3 rounded hover:bg-blue-700">
                    Proceed to Checkout
                </a>
            </div>

        </div>

    <?php endif; ?>

</div>

<script>
function updateQty(cartItemId, qty) {
    fetch('/cart/update-qty', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart_item_id: cartItemId, quantity: qty })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) alert(data.message);
        else location.reload();
    });
}

function removeItem(id) {
    fetch('/cart/remove-item', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart_item_id: id })
    })
    .then(res => res.json())
    .then(data => location.reload());
}
</script>

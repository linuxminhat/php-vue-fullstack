<?php use App\Core\View; ?>
<?php use App\Core\Auth; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? View::e($title) : 'HTPLUS Bookstore' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="/favicon.ico">    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= View::asset('css/app.css') ?>">
</head>

<body class="bg-gray-50 text-gray-900">

    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <?= $content ?>

    <script>
    // Global cart functions
    function addToCart(productId, quantity = null) {
        const qtyInput = document.getElementById('qty');
        const qty = quantity || (qtyInput ? parseInt(qtyInput.value) : 1) || 1;
        
        const productPrice = document.querySelector(`[data-product-id="${productId}"]`)?.dataset?.productPrice;
        if (!productPrice) {
            alert('Không tìm thấy thông tin sản phẩm');
            return;
        }
        
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
    </script>
</body>
</html>

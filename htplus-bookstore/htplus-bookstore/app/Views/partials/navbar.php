<?php

use App\Core\View;
use App\Core\Auth;
use App\Services\CategoryService;

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!function_exists('nav_active')) {
    function nav_active(string $path): string {
        global $currentPath;
        return $currentPath === $path
            ? 'text-green-600 font-semibold'
            : 'text-gray-800 hover:text-green-600';
    }
}

// Get categories using Service if not passed from controller
if (!isset($categories)) {
    $categoryService = new CategoryService();
    $categories = $categoryService->getAllCategories();
}

?>

<header class="w-full bg-[#f6faf9] shadow-sm">
    <div class="max-w-6xl mx-auto flex items-center justify-between py-4 px-4">
        
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img
                src="<?= View::asset('app-image/htplus-book-store-logo.png') ?>"
                alt="HTPLUS Book Store"
                class="h-10 md:h-12 lg:h-14 w-auto"
            >
            <div class="leading-tight">
                <div class="text-lg font-semibold tracking-wide">HTPLUS Book Store</div>
                <div class="text-xs text-gray-500">in Da Nang</div>
            </div>
        </div>

        <!-- Menu -->
        <nav class="hidden md:flex items-center space-x-8 text-sm">

            <a href="/about" class="<?= nav_active('/about') ?>">About Us</a>
            <a href="/#our-locations" class="hover:text-green-600">Our Locations</a>

            <!-- All Books -->
            <a href="/products" class="<?= nav_active('/products') ?>">All Books</a>

            <?php if (Auth::isLoggedIn()): ?>
                <a href="/account" class="<?= nav_active('/account') ?>">My Account</a>
            <?php else: ?>
                <a href="/auth/login" class="<?= nav_active('/auth/login') ?>">My Account</a>
            <?php endif; ?>

            <?php if (Auth::isAdmin()): ?>
                <a href="/admin" class="px-4 py-2 hover:text-blue-600">Admin Page</a>
            <?php endif; ?>

        </nav>

        <div class="flex items-center space-x-4 text-green-500">
                <a href="/cart" class="relative cursor-pointer hover:scale-110 transition">
        <svg xmlns="http://www.w3.org/2000/svg" 
         viewBox="0 0 24 24" 
         fill="currentColor" 
         class="w-8 h-8 text-gray-700 hover:text-green-600">
        <path d="M2.25 3a.75.75 0 0 0 0 1.5h1.5l2.28 9.11a3 3 0 0 0 2.91 2.39h9.29a3 3 0 0 0 2.91-2.39l1.03-4.91a.75.75 0 0 0-.73-.9H6.34l-.47-2H21a.75.75 0 0 0 0-1.5H4.5l-.28-1.11A1.5 1.5 0 0 0 2.25 3z"/>
        <path d="M8.25 21a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm8.25 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
        </svg>

        <span id="cart-count"
          class="absolute -top-1 -right-2 text-[10px] bg-green-600 text-white rounded-full px-1">
        <?= $_SESSION['cart_count'] ?? 0 ?>
        </span>
    </a>
            <button class="relative text-red-500 hover:text-red-600 transition cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke-width="1.8"
         stroke="currentColor"
         class="w-7 h-7 md:w-8 md:h-8">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733C11.285 4.876 9.623 3.75 7.688 3.75 5.099 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
    </svg>

    <span class="absolute -top-1 -right-2 text-[10px] bg-green-600 text-white rounded-full px-1">
        0
    </span>
</button>

        </div>
    </div>
</header>

<script>
function toggleBooksMenu() {
    const box = document.getElementById("books-menu");

    if (box.classList.contains("hidden")) {
        box.classList.remove("hidden");
        setTimeout(() => box.classList.remove("opacity-0"), 10);
    } else {
        box.classList.add("opacity-0");
        setTimeout(() => box.classList.add("hidden"), 200);
    }
}

document.addEventListener("click", function(e) {
    const menu = document.getElementById("books-menu");
    const btn = e.target.closest("button");

    if (!menu.contains(e.target) && (!btn || !btn.textContent.includes("All Books"))) {
        menu.classList.add("opacity-0");
        setTimeout(() => menu.classList.add("hidden"), 200);
    }
});
</script>

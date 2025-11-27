<?php use App\Core\View; ?>

<section class="py-12 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-3xl font-semibold mb-6">All Books</h1>

        <p class="text-sm text-gray-500 mb-6">
            Tổng cộng: <?= (int)($total_products ?? 0) ?> cuốn sách
        </p>
        <?php if (empty($products)): ?>
            <p>Chưa có sách.</p>
        <?php else: ?>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <?php foreach ($products as $product): ?>
                    <article class="border rounded-md p-3 shadow-sm bg-white">
                        <div class="mb-3 flex items-center justify-center bg-gray-50 aspect-[3/4] overflow-hidden">
                            <?php if (!empty($product->image ?? null)): ?>
                                <img src="<?= View::e($product->image) ?>"
                                     alt="<?= View::e($product->name) ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-xs text-gray-400">No image</span>
                            <?php endif; ?>
                        </div>
                        <h2 class="font-semibold text-sm mb-1">
                            <?= View::e($product->name) ?>
                        </h2>
                        <?php if (!empty($product->author)): ?>
                            <p class="text-xs text-gray-500 mb-1">
                                By <?= View::e($product->author) ?>
                            </p>
                        <?php endif; ?>
                        <p class="text-sm font-medium mb-3">
                            <?= View::currency($product->price ?? 0) ?>
                        </p>
                        <button class="w-full bg-green-400 hover:bg-green-500 text-white text-xs py-2 rounded">
                            Add to Cart
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (($total_pages ?? 1) > 1): ?>
                <nav class="mt-8 flex justify-center gap-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php
                        $isActive = ($i === ($page ?? 1));
                        $basePath = strtok($_SERVER['REQUEST_URI'], '?');
                        $url      = $basePath . '?page=' . $i;
                        ?>
                        <a href="<?= View::e($url) ?>"
                           class="px-3 py-1 border rounded text-sm
                               <?= $isActive
                                   ? 'bg-green-500 text-white border-green-500'
                                   : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

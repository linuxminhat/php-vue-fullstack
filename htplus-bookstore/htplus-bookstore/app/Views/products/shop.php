<?php use App\Core\View; ?>
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-4xl font-semibold text-center mb-10">Cửa hàng</h1>
        <div class="grid grid-cols-1 md:grid-cols-[260px,1fr] gap-10">
            <!-- Sidebar filter -->
            <aside class="space-y-6 text-sm">
                <form method="get" class="space-y-4">
                    <!-- Search -->
                    <div>
                        <label class="block mb-1 font-semibold">Tìm kiếm</label>
                        <input
                            type="text"
                            name="q"
                            value="<?= View::e($q ?? '') ?>"
                            placeholder="Tìm kiếm sách và tác giả"
                            class="w-full border rounded px-3 py-2 text-sm"
                        />
                    </div>

                    <!-- Category filter -->
                    <div>
                        <p class="mb-1 font-semibold">Thể loại</p>
                        <div class="space-y-1 max-h-64 overflow-auto pr-2">
                            <label class="flex items-center gap-2">
                                <input
                                    type="radio"
                                    name="category_id"
                                    value=""
                                    <?= empty($current_cat_id) ? 'checked' : '' ?>
                                />
                                <span>Tất cả</span>
                            </label>
                            <?php foreach ($categories as $cat): ?>
                                <label class="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        name="category_id"
                                        value="<?= (int)$cat->id ?>"
                                        <?= ($current_cat_id ?? 0) === (int)$cat->id ? 'checked' : '' ?>
                                    />
                                    <span><?= View::e($cat->name) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-green-500 text-white py-2 rounded text-sm font-semibold">
                        Áp dụng bộ lọc
                    </button>
                </form>
            </aside>

            <!-- Content / list -->
            <div>
                <!-- search bar + sort bar -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <p class="text-sm text-gray-500">
                        Hiển thị <?= count($products) ?> trong số <?= (int)($total_products ?? 0) ?> sách
                    </p>

                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-600">Sắp xếp:</span>
                        <form method="get" class="flex items-center gap-2" data-turbo-frame="_top">
                            <!-- giữ lại các param khác khi đổi sort -->
                            <input type="hidden" name="q" value="<?= View::e($q ?? '') ?>">
                            <input type="hidden" name="category_id" value="<?= (int)($current_cat_id ?? 0) ?>">

                            <select name="sort" class="border rounded px-2 py-1 text-sm"
                                    onchange="this.form.requestSubmit()">
                                <option value="title_az"     <?= ($current_sort ?? '') === 'title_az' ? 'selected' : '' ?>>Tên A–Z</option>
                                <option value="title_za"     <?= ($current_sort ?? '') === 'title_za' ? 'selected' : '' ?>>Tên Z–A</option>
                                <option value="price_low_high"  <?= ($current_sort ?? '') === 'price_low_high' ? 'selected' : '' ?>>Giá: Thấp → Cao</option>
                                <option value="price_high_low"  <?= ($current_sort ?? '') === 'price_high_low' ? 'selected' : '' ?>>Giá: Cao → Thấp</option>
                                <option value="newest"          <?= ($current_sort ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            </select>
                        </form>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <p class="text-gray-500">Không tìm thấy sách nào phù hợp.</p>
                <?php else: ?>
                    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($products as $product): ?>
                            <article class="relative group">
                                <div class="bg-white rounded-lg shadow-sm hover:shadow-xl hover:-translate-y-2 transition p-3">
                                    <div class="relative mb-3">
                                        <div class="absolute -top-3 left-0 bg-green-400 text-white text-[11px] px-3 py-1 rounded-sm">
                                            BÌA CỨNG
                                        </div>
                                        <button class="absolute top-2 right-2 w-8 h-8 rounded-full border bg-white flex items-center justify-center text-green-500 text-lg">
                                            ♡
                                        </button>

                                        <a href="/product/<?= $product->id ?>">
                                    <div class="bg-gray-50 border flex items-center justify-center aspect-[3/4] overflow-hidden">
                                     <?php if (!empty($product->image ?? null)): ?>
                                   <img src="<?= View::e($product->image) ?>"
                                     alt="<?= View::e($product->name) ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition">
                                 <?php else: ?>
                                 <span class="text-gray-400 text-xs">Chưa có ảnh</span>
                                 <?php endif; ?>
                                 </div>
                                    </a>

                                    </div>

                                    <h3 class="font-semibold text-sm mb-1">
                                        <?= View::e($product->name) ?>
                                    </h3>

                                    <?php if (!empty($product->author)): ?>
                                        <p class="text-xs text-gray-500 mb-1">
                                            Tác giả: <?= View::e($product->author) ?>
                                        </p>
                                    <?php endif; ?>

                                    <p class="text-sm font-medium mb-3">
                                        <?= View::currency($product->price ?? 0) ?>
                                    </p>

                                    <button
                                        onclick="addToCart(<?= $product->id ?>)"
                                        data-product-id="<?= $product->id ?>"
                                        data-product-price="<?= $product->price ?>"
                                        class="w-full bg-green-400 hover:bg-green-500 text-white text-xs font-semibold py-2 flex items-center justify-center gap-2 rounded">
                                        <span>🛒</span><span>Thêm vào giỏ</span>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (($total_pages ?? 1) > 1): ?>
                        <nav class="mt-10 flex justify-center gap-2 text-sm">
                            <?php
                            $basePath = strtok($_SERVER['REQUEST_URI'], '?');
                            for ($i = 1; $i <= $total_pages; $i++):
                                $isActive = ($i === ($page ?? 1));
                                $params   = [
                                    'page'        => $i,
                                    'q'           => $q ?? '',
                                    'category_id' => $current_cat_id ?? '',
                                    'sort'        => $current_sort ?? '',
                                ];
                                $url = $basePath . '?' . http_build_query($params);
                            ?>
                                <a href="<?= View::e($url) ?>"
                                   class="px-3 py-1 border rounded
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
        </div>
    </div>
</section>

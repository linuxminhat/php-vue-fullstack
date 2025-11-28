<?php use App\Core\View; ?>
<section
    class="relative h-[560px] w-full overflow-hidden">
    <div
        class="absolute inset-0 bg-cover bg-center"
       style="background-image: url('<?= View::asset('app-image/background-book-store.jpg') ?>');">
    </div>

    <div class="absolute inset-0 bg-black/45"></div>
    <div class="relative max-w-6xl mx-auto h-full flex items-end px-4 pb-16">
        <div class="text-white max-w-xl">
            <p class="tracking-[0.25em] text-xs uppercase mb-2">
                Đọc nhiều, sống tốt
            </p>
            <h1 class="text-4xl md:text-5xl font-semibold mb-4">
                HTPLUS Book Store 
            </h1>
            <div class="flex flex-wrap gap-4 text-xs font-semibold">
                <a href="#whats-good"
                   class="inline-block border border-white px-4 py-2 hover:bg-white hover:text-black transition">
                    KHÁM PHÁ CỬA HÀNG
                </a>
                <a href="#submit-review"
                   class="inline-block border border-white px-4 py-2 hover:bg-white hover:text-black transition">
                    ĐÁNH GIÁ SÁCH
                </a>
            </div>
        </div>
    </div>
</section>
<section id="whats-good" class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-semibold text-center mb-10">
            Chọn cuốn sách yêu thích của bạn
        </h2>
        <?php if (empty($products)): ?>
            <p class="text-center text-gray-500">Chưa có sách nào.</p>
        <?php else: ?>
    <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($products as $product): ?>
            <article class="relative group">
                <div
                    class="bg-white rounded-lg shadow-sm
                           transition-transform transition-shadow duration-200
                           hover:-translate-y-2 hover:shadow-xl">

                    <div class="relative">
                        <div class="absolute -top-3 left-0 bg-green-400 text-white text-[11px] font-semibold px-3 py-1 rounded-sm tracking-wide">
                            BÌA CỨNG
                        </div>
                        <button
                            class="absolute top-2 right-2 w-8 h-8 rounded-full border border-green-300 bg-white flex items-center justify-center text-green-500 text-lg">
                            ♡
                        </button>
                        <a href="/product/<?= $product->id ?>">
                        <div class="mb-3 flex items-center justify-center bg-gray-50 aspect-[3/4] overflow-hidden">
                        <?php if (!empty($product->image ?? null)): ?>
                        <img src="<?= View::e($product->image) ?>"
                        alt="<?= View::e($product->name) ?>"
                        class="w-full h-full object-cover">
                        <?php else: ?>
                        <span class="text-xs text-gray-400">Chưa có ảnh</span>
                        <?php endif; ?>
                        </div>
                        </a>


                    </div>

                    <!-- Thông tin sách -->
                    <div class="px-2 pb-3">
                        <h3 class="font-semibold text-sm mb-1">
                            <?= View::e($product->name) ?>
                        </h3>

                        <?php $author = property_exists($product, 'author') ? $product->author : null; ?>
                        <?php if ($author): ?>
                            <p class="text-xs text-gray-500 mb-1">
                                Tác giả: <?= View::e($author) ?>
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
                        <span>🛒</span>
                        <span>Thêm vào giỏ</span>
                        </button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
    ?>
     
    <?php if (($total_pages ?? 1) > 1): ?>
        <nav class="mt-10 flex justify-center gap-2">
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

</section>

<!-- OUR LOCATIONS -->
<section id="our-locations" class="py-16 bg-[#f9fafb]">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-semibold text-center mb-10">
            Văn phòng & Cơ sở phát triển
        </h2>

        <div class="grid gap-10 md:grid-cols-2 text-sm md:text-base">
            <article class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">Văn phòng kinh doanh</h3>

                <div class="space-y-2">
                    <p>
                        <span class="font-semibold">Tên công ty:</span>
                        <span class="ml-2">HT Plus JSC</span>
                    </p>
                    <p>
                        <span class="font-semibold">Thành lập:</span>
                        <span class="ml-2">04/2022</span>
                    </p>
                    <p>
                        <span class="font-semibold">Địa chỉ:</span>
                        <span class="ml-2">
                            No 402 Tokyo Fuji Buildings 2-3-1 Iidabashi,<br>
                            Chiyoda Ward, Tokyo
                        </span>
                    </p>
                    <p>
                        <span class="font-semibold">Vốn điều lệ:</span>
                        <span class="ml-2">5 tỷ đồng</span>
                    </p>

                    <p class="font-semibold mt-4">Lĩnh vực kinh doanh</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Dịch vụ phát triển offshore (Hệ thống web, ứng dụng, Blockchain, AI)</li>
                        <li>Dịch vụ phát triển nhân lực (Tiếng Nhật thương mại & khóa đào tạo CNTT)</li>
                        <li>Cung cấp giải pháp (Solution)</li>
                    </ul>
                </div>

                <div class="mt-5 aspect-video w-full">
    <iframe
        class="w-full h-full rounded-md"
        style="border:0;"
        loading="lazy"
        allowfullscreen=""
        referrerpolicy="no-referrer-when-downgrade"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3240.0695822429348!2d139.74528217634358!3d35.69990527894696!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x60188c41654910f1%3A0x841a0b43b636edb2!2zSWlkYWJhc2hpLCBDaGl5b2RhLCBUw7RrecO0IDEwMi0wMDcyLCBOaOG6rXQgQuG6o24!5e0!3m2!1svi!2s!4v1764052753300!5m2!1svi!2s">
    </iframe>
</div>
            </article>
            <article class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">Cơ sở phát triển</h3>

                <div class="space-y-2">
                    <p>
                        <span class="font-semibold">Tên công ty:</span>
                        <span class="ml-2">CTCP Phần mềm HT Plus</span>
                    </p>
                    <p>
                        <span class="font-semibold">Địa chỉ:</span>
                        <span class="ml-2">
                            Tòa nhà 2/9, 168–170 Xô Viết Nghệ Tĩnh,<br>
                            phường Hòa Cường, Thành phố Đà Nẵng, Việt Nam
                        </span>
                    </p>
                    <p>
                        <span class="font-semibold">Vốn điều lệ:</span>
                        <span class="ml-2">1,5 tỷ đồng Việt Nam</span>
                    </p>

                    <p class="font-semibold mt-4">Lĩnh vực kinh doanh</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Dịch vụ phát triển quốc tế (Web, ứng dụng, Blockchain, AI)</li>
                        <li>Dịch vụ phát triển nhân lực (Tiếng Nhật thương mại, đào tạo bán hàng CNTT)</li>
                        <li>Cung cấp giải pháp (Solution)</li>
                    </ul>
                </div>

                <!-- Google Map Đà Nẵng -->
                <div class="mt-5 aspect-video w-full">
                <iframe
                class="w-full h-full rounded-md"
                style="border:0;"
                loading="lazy"
                allowfullscreen=""
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d30677.43676962533!2d108.22615039082542!3d16.030186128515165!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1zVMOyYSBuaMOgIDIvOSwgMTY44oCTMTcwIFjDtCBWaeG6v3QgTmdo4buHIFTEqW5oLCDEkMOgIE7hurVuZw!5e0!3m2!1svi!2s!4v1764052673416!5m2!1svi!2s">
                </iframe>
</div>

            </article>

        </div>
    </div>
</section>

<section id="submit-review" class="py-16 bg-[#f6faf9]">
    <div class="max-w-3xl mx-auto px-4 text-center text-sm text-gray-600">
        <h3 class="text-xl font-semibold mb-4">Đánh giá sách</h3>
        <p>Phần này sau bạn có thể làm form đánh giá sách.</p>
    </div>
</section>


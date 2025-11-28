<?php use App\Core\View; ?>

<section class="relative w-full min-h-[60vh] flex items-center overflow-hidden">
    <div
        class="absolute inset-0 bg-cover bg-center scale-105 filter blur-sm"
        style="background-image: url('<?= View::asset('app-image/background-about-us.jpg') ?>');">
    </div>

    <div class="absolute inset-0 bg-black/70"></div>


    <div class="relative max-w-4xl mx-auto px-4 py-16 text-slate-100">
        <h1 class="text-3xl md:text-4xl font-semibold mb-6 text-slate-50">Về HTPLUS Book Store</h1>

        <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">
            HTPLUS Book Store là dự án demo nội bộ của <strong>HT Plus</strong>, một công ty công nghệ 
            chuyên xây dựng các giải pháp phần mềm hiện đại cho doanh nghiệp và giáo dục.
            Dự án này được thiết kế như một hiệu sách trực tuyến nhỏ gọn để thực hành phát triển web thực tế.
        </p>

       <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">
            Mục tiêu chính của ứng dụng này là khám phá cách xây dựng một codebase sạch sẽ, dễ bảo trì sử dụng
            <strong>PHP thuần túy</strong> với <strong>kiến trúc MVC tùy chỉnh</strong>, kết hợp với
            <strong>HTML, CSS (TailwindCSS)</strong>, và <strong>AJAX</strong> để tương tác mượt mà
            mà không cần tải lại trang.
        </p>

       <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">
            Phía sau hệ thống, chúng tôi sử dụng <strong>MySQL</strong> để lưu trữ dữ liệu, một cơ sở dữ liệu
            có cấu trúc cho người dùng, danh mục, sản phẩm và đơn hàng, cùng với module xác thực và phân quyền đơn giản.
            Dự án được cố tình giữ nhỏ gọn nhưng thực tế để có thể mở rộng sau này với các tính năng như
            lịch sử đơn hàng, đánh giá, danh sách yêu thích và công cụ quản trị.
        </p>

      <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">
            Đối với các thành viên của HT Plus, hiệu sách này đóng vai trò là sân chơi thực hành để:
        </p>

       <ul class="list-disc list-inside mb-4 text-sm md:text-base space-y-1 text-slate-200">
            <li>Thực hành code PHP sạch với tách biệt các concerns (models, controllers, views).</li>
            <li>Làm việc với các REST-like endpoints và JSON responses sử dụng AJAX.</li>
            <li>Cải thiện kỹ năng front-end với TailwindCSS và responsive layouts.</li>
            <li>Hiểu về luồng e-commerce cơ bản: sản phẩm, giỏ hàng và đơn hàng.</li>
            <li>Chuẩn bị cho các tích hợp tương lai với APIs hoặc các microservices khác.</li>
        </ul>

       <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">
            HTPLUS Book Store không chỉ là một demo — đây là một dự án học tập phản ánh cách chúng tôi tiếp cận
            phát triển phần mềm tại HT Plus: đơn giản, rõ ràng và sẵn sàng phát triển.
        </p>

        <a href="/"
           class="inline-block px-4 py-2 border border-white text-sm font-semibold hover:bg-white hover:text-black transition">
            ← Về lại trang chủ 
        </a>
    </div>
</section>

<?php use App\Core\View; ?>

<section class="relative w-full min-h-[60vh] flex items-center overflow-hidden">
    <div
        class="absolute inset-0 bg-cover bg-center scale-105 filter blur-sm"
        style="background-image: url('<?= View::asset('app-image/background-about-us.jpg') ?>');">
    </div>

    <div class="absolute inset-0 bg-black/70"></div>


    <div class="relative max-w-4xl mx-auto px-4 py-16 text-slate-100">
        <h1 class="text-3xl md:text-4xl font-semibold mb-6 text-slate-50">About HTPLUS Book Store</h1>

        <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">


            HTPLUS Book Store is an internal demo project of <strong>HT Plus</strong>, a technology company that
            focuses on building modern software solutions for businesses and education.  
            This project is designed as a lightweight online bookstore to practice real-world web development.
        </p>

       <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">


            The main goal of this application is to explore how to build a clean, maintainable codebase using
            <strong>pure PHP</strong> with a custom <strong>MVC architecture</strong>, combined with
            <strong>HTML, CSS (TailwindCSS)</strong>, and <strong>AJAX</strong> for smooth user interaction
            without full page reloads.
        </p>

       <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">


            Behind the scenes, the system uses <strong>MySQL</strong> for data storage, a structured database
            for users, categories, products, and orders, and a simple authentication and authorization module.
            The project is intentionally kept small but realistic so that it can be extended later with features
            like order history, reviews, wishlists, and admin tools.
        </p>

      <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">

            For HT Plus team members, this bookstore serves as a practical playground to:
        </p>

       <ul class="list-disc list-inside mb-4 text-sm md:text-base space-y-1 text-slate-200">

            <li>Practice clean PHP coding with separation of concerns (models, controllers, views).</li>
            <li>Work with REST-like endpoints and JSON responses using AJAX.</li>
            <li>Improve front-end skills with TailwindCSS and responsive layouts.</li>
            <li>Understand basic e-commerce flows: products, carts, and orders.</li>
            <li>Prepare for future integrations with APIs or other microservices.</li>
        </ul>

       <p class="mb-4 text-sm md:text-base font-normal leading-relaxed text-slate-200">

            HTPLUS Book Store is not just a demo — it is a learning project that reflects how we approach
            software development at HT Plus: simple, clear, and ready to grow.
        </p>

        <a href="/"
           class="inline-block px-4 py-2 border border-white text-sm font-semibold hover:bg-white hover:text-black transition">
            ← Back to Home
        </a>
    </div>
</section>

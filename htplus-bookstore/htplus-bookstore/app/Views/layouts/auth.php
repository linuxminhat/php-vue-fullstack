<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>
        <?= isset($title) ? \App\Core\View::e($title) . ' | HTPLUS Bookstore' : 'HTPLUS Bookstore' ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= \App\Core\View::asset('css/app.css') ?>">
</head>
<body class="bg-gray-50 text-gray-900">

    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <main class="relative min-h-[calc(100vh-64px)]">
        <div
            class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('<?= \App\Core\View::asset('app-image/background-login.jpg') ?>');">
        </div>

        <div class="absolute inset-0 bg-white/35"></div>

        <div class="relative max-w-6xl mx-auto flex justify-end items-center h-full px-4 py-12">
            <div class="bg-white/85 backdrop-blur shadow-xl rounded-xl w-full max-w-xl p-8">
                <?= $content ?>
            </div>
        </div>
    </main>

    <script src="<?= \App\Core\View::asset('js/auth.js') ?>"></script>
</body>
</html>

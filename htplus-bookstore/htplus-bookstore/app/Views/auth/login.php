<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-semibold text-gray-800">Login</h1>

    <div class="flex bg-gray-100 rounded-full p-1 space-x-1 text-sm">
        <a href="/auth/register"
           class="px-4 py-1 rounded-full hover:bg-white">
            Sign Up
        </a>
        <a href="/auth/login"
           class="px-4 py-1 rounded-full bg-white text-green-600 font-semibold">
            Login
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded">
        <?= \App\Core\View::e($error) ?>
    </div>
<?php endif; ?>

<form method="post" action="/auth/login" class="space-y-4">
    <div>
        <label class="block text-sm font-medium mb-1">
            Email address <span class="text-red-500">*</span>
        </label>
        <input
            type="email"
            name="email"
            required
            value="<?= \App\Core\View::e($email ?? $_POST['email'] ?? '') ?>"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="you@example.com"
        >
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Password <span class="text-red-500">*</span>
        </label>
        <input
            type="password"
            name="password"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="••••••••"
        >
    </div>

    <div class="flex items-center justify-between text-xs text-gray-500">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="remember" class="rounded border-gray-300">
            <span>Remember me</span>
        </label>
        <a href="#" class="hover:text-green-600">Forgot password?</a>
    </div>

    <button
        type="submit"
        class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-md text-sm">
        Login
    </button>
</form>

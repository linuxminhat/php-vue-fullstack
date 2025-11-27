<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-semibold text-gray-800">Register</h1>

    <div class="flex bg-gray-100 rounded-full p-1 space-x-1 text-sm">
        <a href="/auth/register"
           class="px-4 py-1 rounded-full bg-white text-green-600 font-semibold">
            Sign Up
        </a>
        <a href="/auth/login"
           class="px-4 py-1 rounded-full hover:bg-white">
            Login
        </a>
    </div>
</div>

<form id="register-form" class="space-y-4">
    <div id="register-error"
         class="hidden bg-red-50 text-red-700 text-sm px-3 py-2 rounded">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Full name</label>
        <input name="full_name" type="text"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Email address <span class="text-red-500">*</span>
        </label>
        <input name="email" type="email" required
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Password <span class="text-red-500">*</span>
        </label>
        <input name="password" type="password" required
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
    </div>

    <button type="submit"
            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-md text-sm">
        Register
    </button>
</form>

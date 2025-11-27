<?php ob_start(); ?>

<h1 class="text-2xl font-bold mb-4">Edit User #<?= $user->id ?></h1>

<form method="POST" action="/admin/users/update">

    <input type="hidden" name="id" value="<?= $user->id ?>">

    <label>Full Name:</label>
    <input class="border p-2 w-full mb-3"
           name="full_name"
           value="<?= htmlspecialchars($user->full_name) ?>">

    <label>Role:</label>
    <select name="role" class="border p-2 w-full mb-3">
        <option value="Admin" <?= $user->role === 'Admin' ? 'selected' : '' ?>>Admin</option>
        <option value="Customer" <?= $user->role === 'Customer' ? 'selected' : '' ?>>Customer</option>
    </select>

    <label>New Password (optional):</label>
    <input type="password" class="border p-2 w-full mb-3" name="password">

    <label>Active Status:</label>
    <select name="is_active" class="border p-2 w-full mb-3">
        <option value="1" <?= $user->is_active ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= !$user->is_active ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>

</form>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layout/admin-layout.php";
?>

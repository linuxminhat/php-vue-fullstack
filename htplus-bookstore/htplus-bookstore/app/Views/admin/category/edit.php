<?php ob_start(); ?>

<h1 class="text-2xl font-bold mb-4">Edit category #<?= $category->id ?></h1>

<form method="POST" action="/admin/categories/update">

    <input type="hidden" name="id" value="<?= $category->id ?>">
    <label class="block mb-1 font-semibold">Name:</label>
    <input class="border p-2 w-full mb-4"
           name="name"
           required
           value="<?= htmlspecialchars($category->name) ?>">

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Update
    </button>

</form>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layout/admin-layout.php";
?>

<?php ob_start(); ?>

    <h1 class="text-3xl font-bold mb-6">Quản lý người dùng hệ thống</h1>

<button id="openCreateModal"
    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
    + New Client
</button>

    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Create User</h2>

        <form id="createForm" method="POST" action="/admin/users/create">

            <label class="block mb-2">Email</label>
            <input type="email" name="email" required
                class="w-full border px-3 py-2 rounded mb-3">

            <label class="block mb-2">Password</label>
            <input type="password" name="password" required
                class="w-full border px-3 py-2 rounded mb-3">

            <label class="block mb-2">Full Name</label>
            <input type="text" name="full_name" required
                class="w-full border px-3 py-2 rounded mb-3">

            <label class="block mb-2">Role</label>
            <select name="role" class="w-full border px-3 py-2 rounded mb-3">
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="customer">Customer</option>
            </select>

            <label class="block mb-2">Active Status</label>
            <select name="is_active" class="w-full border px-3 py-2 rounded mb-4">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>

            <div class="flex justify-end space-x-2">
                <button type="button" id="closeCreateModal"
                        class="px-4 py-2 bg-gray-400 rounded text-white">Cancel</button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 rounded text-white">Create</button>
            </div>
        </form>
    </div>
</div>

    <div class="bg-white shadow mt-6 rounded-lg overflow-hidden">

        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 border-b">
            <tr class="text-left">
                <th class="py-3 px-4">ID</th>
                <th class="py-3 px-4">Email</th>
                <th class="py-3 px-4">Password (hashed)</th>
                <th class="py-3 px-4">Full Name</th>
                <th class="py-3 px-4">Role</th>
                <th class="py-3 px-4">Action</th>
            </tr>
            </thead>

            <tbody>

            <?php foreach ($users as $u): ?>
                <tr class="border-b hover:bg-gray-50">

                    <td class="py-3 px-4"><?= $u->id ?></td>

                    <td class="py-3 px-4"><?= $u->email ?></td>

                    <td class="py-3 px-4 text-gray-500 text-sm">
                        <?= $u->password ?>
                    </td>

                    <td class="py-3 px-4"><?= $u->full_name ?></td>

                    <td class="py-3 px-4 capitalize">
                        <?= $u->role ?>
                    </td>

                    <td class="py-3 px-4 space-x-2">

                        <button 
                            class="px-3 py-1 bg-green-500 text-white rounded openEditModal"
                            data-id="<?= $u->id ?>"
                            data-name="<?= $u->full_name ?>"
                            data-role="<?= $u->role ?>"
                            data-active="<?= $u->is_active ?>">Edit
                        </button>
                        <a class="px-3 py-1 bg-red-500 text-white rounded"
                        href="/admin/users/delete?id=<?= $u->id ?>">Delete</a>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
    <div class="mt-4 flex justify-center space-x-2">
        <?php if ($page > 1): ?>
            <a href="/admin/users?page=<?= $page - 1 ?>" 
            class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="/admin/users?page=<?= $i ?>"
            class="px-3 py-2 rounded 
                <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
            <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="/admin/users?page=<?= $page + 1 ?>" 
            class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Next</a>
        <?php endif; ?>

    </div>
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Edit User</h2>
            <form id="editForm" method="POST" action="/admin/users/update">
                <input type="hidden" name="id" id="edit_id">
                <label class="block mb-2">Full Name</label>
                <input type="text" name="full_name" id="edit_fullname"
                    class="w-full border px-3 py-2 rounded mb-3">
                <label class="block mb-2">Role</label>
                <select name="role" id="edit_role"
                        class="w-full border px-3 py-2 rounded mb-3">
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="customer">Customer</option>
                </select>
                <label class="block mb-2">New Password (optional)</label>
                <input type="password" name="password" 
                    class="w-full border px-3 py-2 rounded mb-3">
                <label class="block mb-2">Active Status</label>
                <select name="is_active" id="edit_active"
                        class="w-full border px-3 py-2 rounded mb-4">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeModal" 
                            class="px-4 py-2 bg-gray-400 rounded text-white">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 rounded text-white">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
<script>
document.querySelectorAll(".openEditModal").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("edit_id").value = btn.dataset.id;
        document.getElementById("edit_fullname").value = btn.dataset.name;
        document.getElementById("edit_role").value = btn.dataset.role;
        document.getElementById("edit_active").value = btn.dataset.active;

        document.getElementById("editModal").classList.remove("hidden");
    });
});

document.getElementById("closeModal").addEventListener("click", () => {
    document.getElementById("editModal").classList.add("hidden");
});
</script>
<script>
document.getElementById("openCreateModal").addEventListener("click", () => {
    document.getElementById("createModal").classList.remove("hidden");
});

document.getElementById("closeCreateModal").addEventListener("click", () => {
    document.getElementById("createModal").classList.add("hidden");
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layout/admin-layout.php";
?>

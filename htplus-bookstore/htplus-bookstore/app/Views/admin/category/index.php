<?php ob_start(); ?>

<h1 class="text-3xl font-bold mb-6">Quản lý danh mục</h1>

<!-- Nút mở modal tạo mới -->
<button id="openCreateModal"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
    + Thêm danh mục mới
</button>

<!-- Modal Create -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Tạo danh mục mới</h2>

        <form id="createForm">
            <label class="block mb-2">Tên danh mục</label>
            <input type="text" name="name" required
                   class="w-full border px-3 py-2 rounded mb-3">

            <div class="flex justify-end space-x-2">
                <button type="button" id="closeCreateModal"
                        class="px-4 py-2 bg-gray-400 rounded text-white">Hủy</button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 rounded text-white">Tạo mới</button>
            </div>
        </form>
    </div>
</div>

<!-- TABLE -->
<div class="bg-white shadow mt-6 rounded-lg overflow-hidden">

    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b">
        <tr class="text-left">
            <th class="py-3 px-4">ID</th>
            <th class="py-3 px-4">Tên danh mục</th>
            <th class="py-3 px-4">Ngày tạo</th>
            <th class="py-3 px-4">Hành động</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach ($categories as $c): ?>
            <tr class="border-b hover:bg-gray-50">

                <td class="py-3 px-4"><?= $c->id ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($c->name) ?></td>

                <td class="py-3 px-4 text-gray-500 text-sm">
                    <?= $c->created_at ?>
                </td>

                <td class="py-3 px-4 space-x-2">
                    <!-- Edit -->
                    <button 
                        class="px-3 py-1 bg-green-500 text-white rounded openEditModal"
                        data-id="<?= $c->id ?>"
                        data-name="<?= htmlspecialchars($c->name) ?>">
                        Sửa
                    </button>

                    <!-- Delete -->
                    <button 
                        class="px-3 py-1 bg-red-500 text-white rounded deleteBtn"
                        data-id="<?= $c->id ?>">
                        Xóa
                    </button>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<!-- PAGINATION -->
<div class="mt-4 flex justify-center space-x-2">
    <?php if ($page > 1): ?>
        <a href="/admin/categories?page=<?= $page - 1 ?>"
           class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Trước</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="/admin/categories?page=<?= $i ?>"
           class="px-3 py-2 rounded 
            <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="/admin/categories?page=<?= $page + 1 ?>"
           class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Tiếp</a>
    <?php endif; ?>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white w-1/3 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Chỉnh sửa danh mục</h2>

        <form id="editForm">

            <input type="hidden" name="id" id="edit_id">

            <label class="block mb-2">Tên danh mục</label>
            <input type="text" name="name" id="edit_name" required
                   class="w-full border px-3 py-2 rounded mb-3">

            <div class="flex justify-end space-x-2">
                <button type="button" id="closeModal"
                        class="px-4 py-2 bg-gray-400 rounded text-white">Hủy</button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 rounded text-white">Cập nhật</button>
            </div>

        </form>
    </div>
</div>

<!-- EDIT Modal JS -->
<script>
document.querySelectorAll(".openEditModal").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("edit_id").value = btn.dataset.id;
        document.getElementById("edit_name").value = btn.dataset.name;
        document.getElementById("editModal").classList.remove("hidden");
    });
});

document.getElementById("closeModal").addEventListener("click", () => {
    document.getElementById("editModal").classList.add("hidden");
});
</script>

<!-- CREATE Modal JS -->
<script>
document.getElementById("openCreateModal").addEventListener("click", () => {
    document.getElementById("createModal").classList.remove("hidden");
});

document.getElementById("closeCreateModal").addEventListener("click", () => {
    document.getElementById("createModal").classList.add("hidden");
});
</script>

<!-- FETCH API FOR CRUD -->
<script>
// ================= CREATE =================
document.getElementById("createForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    let name = e.target.name.value;

    const res = await fetch("/admin/categories/create", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ name })
    });

    const data = await res.json();
    alert(data.message ?? "Created!");
    location.reload();
});

// ================= EDIT =================
document.getElementById("editForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    let id = document.getElementById("edit_id").value;
    let name = document.getElementById("edit_name").value;

    const res = await fetch("/admin/categories/update", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ id, name })
    });

    const data = await res.json();
    alert(data.message ?? "Updated!");
    location.reload();
});

// ================= DELETE =================
document.querySelectorAll(".deleteBtn").forEach(btn => {
    btn.addEventListener("click", async () => {
        if (!confirm("Delete this category?")) return;

        const id = btn.dataset.id;

        const res = await fetch("/admin/categories/delete", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({ id })
        });

        const data = await res.json();
        alert(data.message ?? "Deleted!");
        location.reload();
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layout/admin-layout.php";
?>

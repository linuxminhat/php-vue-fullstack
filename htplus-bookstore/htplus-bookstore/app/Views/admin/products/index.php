<?php ob_start(); ?>

<h1 class="text-3xl font-bold mb-6">Quản lý sản phẩm</h1>

<button id="openCreateModal"
    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
    + New Product
</button>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 m-4">
        <h2 class="text-xl font-bold mb-4">Create Product</h2>

        <form id="createForm" onsubmit="submitCreateForm(event)">
            <div class="grid grid-cols-2 gap-4">
                <!-- Name -->
                <div class="col-span-2">
                    <label class="block mb-2 font-semibold">Product Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- SKU -->
                <div>
                    <label class="block mb-2 font-semibold">SKU <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block mb-2 font-semibold">Category</label>
                    <select name="category_id" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Author -->
                <div>
                    <label class="block mb-2 font-semibold">Author <span class="text-red-500">*</span></label>
                    <input type="text" name="author" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Publisher -->
                <div>
                    <label class="block mb-2 font-semibold">Publisher <span class="text-red-500">*</span></label>
                    <input type="text" name="publisher" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- ISBN -->
                <div>
                    <label class="block mb-2 font-semibold">ISBN <span class="text-red-500">*</span></label>
                    <input type="text" name="isbn" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Price -->
                <div>
                    <label class="block mb-2 font-semibold">Price <span class="text-red-500">*</span></label>
                    <input type="number" name="price" step="0.01" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Stock -->
                <div>
                    <label class="block mb-2 font-semibold">Stock <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Image -->
                <div class="col-span-2">
                    <label class="block mb-2 font-semibold">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*"
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label class="block mb-2 font-semibold">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" id="closeCreateModal"
                        class="px-4 py-2 bg-gray-400 rounded text-white hover:bg-gray-500">Cancel</button>
                <button type="submit" id="createBtn"
                        class="px-4 py-2 bg-blue-600 rounded text-white hover:bg-blue-700">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="bg-white shadow mt-6 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 border-b">
            <tr class="text-left">
                <th class="py-3 px-4">ID</th>
                <th class="py-3 px-4">Image</th>
                <th class="py-3 px-4">Name</th>
                <th class="py-3 px-4">Author</th>
                <th class="py-3 px-4">SKU</th>
                <th class="py-3 px-4">Price</th>
                <th class="py-3 px-4">Stock</th>
                <th class="py-3 px-4">Action</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($products as $p): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4"><?= $p->id ?></td>

                    <td class="py-3 px-4">
                        <?php if (!empty($p->image)): ?>
                            <img src="<?= htmlspecialchars($p->image) ?>" 
                                 alt="<?= htmlspecialchars($p->name) ?>"
                                 class="w-12 h-16 object-cover rounded">
                        <?php else: ?>
                            <div class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-xs text-gray-500">No image</span>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td class="py-3 px-4 font-medium"><?= htmlspecialchars($p->name) ?></td>

                    <td class="py-3 px-4 text-gray-600"><?= htmlspecialchars($p->author ?? '-') ?></td>

                    <td class="py-3 px-4 text-sm text-gray-500"><?= htmlspecialchars($p->sku) ?></td>

                    <td class="py-3 px-4 font-semibold text-green-600">
                        <?= number_format($p->price, 0, ',', '.') ?>đ
                    </td>

                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded text-sm <?= $p->stock > 10 ? 'bg-green-100 text-green-800' : ($p->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                            <?= $p->stock ?>
                        </span>
                    </td>

                    <td class="py-3 px-4 space-x-2">
                        <button 
                            class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 openEditModal"
                            data-id="<?= $p->id ?>"
                            data-name="<?= htmlspecialchars($p->name) ?>"
                            data-sku="<?= htmlspecialchars($p->sku) ?>"
                            data-author="<?= htmlspecialchars($p->author ?? '') ?>"
                            data-publisher="<?= htmlspecialchars($p->publisher ?? '') ?>"
                            data-isbn="<?= htmlspecialchars($p->isbn ?? '') ?>"
                            data-category="<?= $p->category_id ?? '' ?>"
                            data-price="<?= $p->price ?>"
                            data-stock="<?= $p->stock ?>"
                            data-description="<?= htmlspecialchars($p->description ?? '') ?>"
                            data-image="<?= htmlspecialchars($p->image ?? '') ?>">
                            Edit
                        </button>
                        <button 
                            onclick="deleteProduct(<?= $p->id ?>)"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2">
    <?php if ($page > 1): ?>
        <a href="/admin/products?page=<?= $page - 1 ?>" 
        class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="/admin/products?page=<?= $i ?>"
        class="px-3 py-2 rounded 
            <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
        <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="/admin/products?page=<?= $page + 1 ?>" 
        class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Next</a>
    <?php endif; ?>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 m-4">
        <h2 class="text-xl font-bold mb-4">Edit Product</h2>
        <form id="editForm" onsubmit="submitEditForm(event)">
            <input type="hidden" name="id" id="edit_id">

            <div class="grid grid-cols-2 gap-4">
                <!-- Name -->
                <div class="col-span-2">
                    <label class="block mb-2 font-semibold">Product Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- SKU -->
                <div>
                    <label class="block mb-2 font-semibold">SKU <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" id="edit_sku" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block mb-2 font-semibold">Category</label>
                    <select name="category_id" id="edit_category" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Author -->
                <div>
                    <label class="block mb-2 font-semibold">Author <span class="text-red-500">*</span></label>
                    <input type="text" name="author" id="edit_author" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Publisher -->
                <div>
                    <label class="block mb-2 font-semibold">Publisher <span class="text-red-500">*</span></label>
                    <input type="text" name="publisher" id="edit_publisher" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- ISBN -->
                <div>
                    <label class="block mb-2 font-semibold">ISBN <span class="text-red-500">*</span></label>
                    <input type="text" name="isbn" id="edit_isbn" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Price -->
                <div>
                    <label class="block mb-2 font-semibold">Price <span class="text-red-500">*</span></label>
                    <input type="number" name="price" id="edit_price" step="0.01" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Stock -->
                <div>
                    <label class="block mb-2 font-semibold">Stock <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" id="edit_stock" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Current Image -->
                <div class="col-span-2">
                    <label class="block mb-2 font-semibold">Current Image</label>
                    <div id="current_image_preview"></div>
                    <input type="hidden" name="current_image" id="edit_current_image">
                </div>

                <!-- New Image -->
                <div class="col-span-2">
                    <label class="block mb-2 font-semibold">Change Image (optional)</label>
                    <input type="file" name="cover_image" accept="image/*"
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label class="block mb-2 font-semibold">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="edit_description" rows="4" required
                        class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" id="closeEditModal" 
                        class="px-4 py-2 bg-gray-400 rounded text-white hover:bg-gray-500">
                    Cancel
                </button>
                <button type="submit" id="updateBtn"
                        class="px-4 py-2 bg-blue-600 rounded text-white hover:bg-blue-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md text-center">
        <div class="mb-4">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
        <p id="successMessage" class="text-gray-600 mb-6"></p>
        <button onclick="closeSuccessModal()" 
                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            OK
        </button>
    </div>
</div>

<script>
// Submit Create Form via AJAX
function submitCreateForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('createForm');
    const formData = new FormData(form);
    const createBtn = document.getElementById('createBtn');
    
    // Disable button
    createBtn.disabled = true;
    createBtn.textContent = 'Creating...';
    
    // Submit via AJAX
    fetch('/admin/products/create', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        createBtn.disabled = false;
        createBtn.textContent = 'Create';
        
        if (data.success) {
            // Close create modal
            document.getElementById('createModal').classList.add('hidden');
            
            // Show success modal
            document.getElementById('successMessage').textContent = 'Product created successfully!';
            document.getElementById('successModal').classList.remove('hidden');
            
            // Reset form
            form.reset();
            
            // Reload after 2 seconds
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            alert(data.message || 'Failed to create product');
        }
    })
    .catch(err => {
        console.error(err);
        createBtn.disabled = false;
        createBtn.textContent = 'Create';
        alert('An error occurred. Please try again.');
    });
}

// Submit Edit Form via AJAX
function submitEditForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('editForm');
    const formData = new FormData();
    const updateBtn = document.getElementById('updateBtn');
    
    // Get all form data
    formData.append('id', document.getElementById('edit_id').value);
    formData.append('name', document.getElementById('edit_name').value);
    formData.append('sku', document.getElementById('edit_sku').value);
    formData.append('author', document.getElementById('edit_author').value);
    formData.append('publisher', document.getElementById('edit_publisher').value);
    formData.append('isbn', document.getElementById('edit_isbn').value);
    formData.append('category_id', document.getElementById('edit_category').value);
    formData.append('price', document.getElementById('edit_price').value);
    formData.append('stock', document.getElementById('edit_stock').value);
    formData.append('description', document.getElementById('edit_description').value);
    
    // Add image if selected
    const fileInput = form.querySelector('input[type="file"]');
    if (fileInput.files.length > 0) {
        formData.append('cover_image', fileInput.files[0]);
    }
    
    // Disable button
    updateBtn.disabled = true;
    updateBtn.textContent = 'Updating...';
    
    // Submit via AJAX
    fetch('/admin/products/update', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        updateBtn.disabled = false;
        updateBtn.textContent = 'Update';
        
        if (data.success) {
            // Close edit modal
            document.getElementById('editModal').classList.add('hidden');
            
            // Show success modal
            document.getElementById('successMessage').textContent = data.message || 'Product updated successfully!';
            document.getElementById('successModal').classList.remove('hidden');
            
            // Reload after 2 seconds
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            alert(data.message || 'Failed to update product');
        }
    })
    .catch(err => {
        console.error(err);
        updateBtn.disabled = false;
        updateBtn.textContent = 'Update';
        alert('An error occurred. Please try again.');
    });
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    location.reload();
}

// Open Create Modal
document.getElementById("openCreateModal").addEventListener("click", () => {
    document.getElementById("createModal").classList.remove("hidden");
});

document.getElementById("closeCreateModal").addEventListener("click", () => {
    document.getElementById("createModal").classList.add("hidden");
});

// Open Edit Modal
document.querySelectorAll(".openEditModal").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("edit_id").value = btn.dataset.id;
        document.getElementById("edit_name").value = btn.dataset.name;
        document.getElementById("edit_sku").value = btn.dataset.sku;
        document.getElementById("edit_author").value = btn.dataset.author;
        document.getElementById("edit_publisher").value = btn.dataset.publisher;
        document.getElementById("edit_isbn").value = btn.dataset.isbn;
        document.getElementById("edit_category").value = btn.dataset.category;
        document.getElementById("edit_price").value = btn.dataset.price;
        document.getElementById("edit_stock").value = btn.dataset.stock;
        document.getElementById("edit_description").value = btn.dataset.description;
        document.getElementById("edit_current_image").value = btn.dataset.image;

        // Show current image
        const previewDiv = document.getElementById("current_image_preview");
        if (btn.dataset.image) {
            previewDiv.innerHTML = `<img src="${btn.dataset.image}" class="w-24 h-32 object-cover rounded">`;
        } else {
            previewDiv.innerHTML = '<span class="text-gray-500 text-sm">No current image</span>';
        }

        document.getElementById("editModal").classList.remove("hidden");
    });
});

document.getElementById("closeEditModal").addEventListener("click", () => {
    document.getElementById("editModal").classList.add("hidden");
});

// Delete Product
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch('/admin/products/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Product deleted successfully!');
                location.reload();
            } else {
                alert(data.message || 'Failed to delete product');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred');
        });
    }
}

// Close modals when clicking outside
window.addEventListener('click', (e) => {
    if (e.target === document.getElementById('createModal')) {
        document.getElementById('createModal').classList.add('hidden');
    }
    if (e.target === document.getElementById('editModal')) {
        document.getElementById('editModal').classList.add('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layout/admin-layout.php";
?>


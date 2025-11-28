<?php ob_start(); ?>

<h1 class="text-3xl font-bold mb-6">Quản lý đơn hàng</h1>

<!-- Orders Table -->
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 border-b">
            <tr class="text-left">
                <th class="py-3 px-4">Order ID</th>
                <th class="py-3 px-4">Customer</th>
                <th class="py-3 px-4">Phone</th>
                <th class="py-3 px-4">Shipping Address</th>
                <th class="py-3 px-4">Total Amount</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Created Date</th>
                <th class="py-3 px-4">Action</th>
            </tr>
            </thead>

            <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="8" class="py-8 text-center text-gray-500">
                        No orders found.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr class="border-b hover:bg-gray-50">
                    <!-- Order ID -->
                    <td class="py-3 px-4 font-semibold">
                        #<?= str_pad($order->id, 6, '0', STR_PAD_LEFT) ?>
                    </td>

                    <!-- Customer Name -->
                    <td class="py-3 px-4">
                        <?= htmlspecialchars($order->customer_name ?? 'N/A') ?>
                        <br>
                        <span class="text-xs text-gray-500">ID: <?= $order->customer_id ?></span>
                    </td>

                    <!-- Phone -->
                    <td class="py-3 px-4 text-sm">
                        <?= htmlspecialchars($order->phone ?? '-') ?>
                    </td>

                    <!-- Shipping Address -->
                    <td class="py-3 px-4 text-sm max-w-xs">
                        <div class="truncate" title="<?= htmlspecialchars($order->shipping_address ?? '-') ?>">
                            <?= htmlspecialchars($order->shipping_address ?? '-') ?>
                        </div>
                    </td>

                    <!-- Total Amount -->
                    <td class="py-3 px-4 font-semibold text-green-600">
                        <?= number_format($order->total_amount, 0, ',', '.') ?>đ
                    </td>

                    <!-- Status -->
                    <td class="py-3 px-4">
                        <?php
                        $statusConfig = [
                            'pending' => ['label' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-300'],
                            'confirmed' => ['label' => 'Confirmed', 'class' => 'bg-blue-100 text-blue-800 border-blue-300'],
                            'shipped' => ['label' => 'Shipped', 'class' => 'bg-purple-100 text-purple-800 border-purple-300'],
                            'delivered' => ['label' => 'Delivered', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-300'],
                            'completed' => ['label' => 'Completed', 'class' => 'bg-green-100 text-green-800 border-green-300'],
                            'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-red-100 text-red-800 border-red-300'],
                        ];
                        $status = $statusConfig[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'bg-gray-100 text-gray-800 border-gray-300'];
                        ?>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold border <?= $status['class'] ?>">
                            <?= $status['label'] ?>
                        </span>
                    </td>

                    <!-- Created Date -->
                    <td class="py-3 px-4 text-sm text-gray-600">
                        <?= date('d/m/Y H:i', strtotime($order->created_at)) ?>
                    </td>

                    <!-- Action -->
                    <td class="py-3 px-4">
                        <button 
                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 openUpdateModal"
                            data-id="<?= $order->id ?>"
                            data-customer="<?= htmlspecialchars($order->customer_name ?? 'N/A') ?>"
                            data-phone="<?= htmlspecialchars($order->phone ?? '') ?>"
                            data-address="<?= htmlspecialchars($order->shipping_address ?? '') ?>"
                            data-total="<?= number_format($order->total_amount, 0, ',', '.') ?>"
                            data-status="<?= $order->status ?>">
                            Update Status
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if (($totalPages ?? 1) > 1): ?>
<div class="mt-4 flex justify-center space-x-2">
    <?php if ($page > 1): ?>
        <a href="/admin/orders?page=<?= $page - 1 ?>" 
        class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="/admin/orders?page=<?= $i ?>"
        class="px-3 py-2 rounded 
            <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
        <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="/admin/orders?page=<?= $page + 1 ?>" 
        class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Next</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Update Status Modal -->
<div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 m-4">
        <h2 class="text-xl font-bold mb-4">Update Order Status</h2>
        
        <form id="updateForm" onsubmit="submitUpdateStatus(event)">
            <input type="hidden" name="id" id="update_id">

            <!-- Order Info (Read-only) -->
            <div class="bg-gray-50 p-4 rounded mb-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-semibold" id="display_order_id"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer:</span>
                    <span class="font-semibold" id="display_customer"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phone:</span>
                    <span id="display_phone"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-semibold text-green-600" id="display_total"></span>
                </div>
                <div>
                    <span class="text-gray-600">Address:</span>
                    <p class="text-sm mt-1" id="display_address"></p>
                </div>
            </div>

            <!-- Status Selection -->
            <div class="mb-4">
                <label class="block mb-2 font-semibold">Order Status <span class="text-red-500">*</span></label>
                <select name="status" id="update_status" required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="pending">🟡 Pending - Chờ xác nhận</option>
                    <option value="confirmed">🔵 Confirmed - Đã xác nhận</option>
                    <option value="shipped">🟣 Shipped - Đang giao hàng</option>
                    <option value="delivered">🟤 Delivered - Đã giao hàng</option>
                    <option value="completed">🟢 Completed - Hoàn thành</option>
                    <option value="cancelled">🔴 Cancelled - Đã hủy</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Select the new status for this order</p>
            </div>

            <!-- Note -->
            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
                <p class="text-sm text-yellow-800">
                    ⚠️ <strong>Note:</strong> Only order status can be changed. All other information is locked.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2">
                <button type="button" id="closeUpdateModal" 
                        class="px-4 py-2 bg-gray-400 rounded text-white hover:bg-gray-500">
                    Cancel
                </button>
                <button type="submit" id="updateBtn"
                        class="px-4 py-2 bg-blue-600 rounded text-white hover:bg-blue-700">
                    Update Status
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
        <p id="successMessage" class="text-gray-600 mb-6">Order status updated successfully!</p>
        <button onclick="closeSuccessModal()" 
                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            OK
        </button>
    </div>
</div>

<script>
// Open Update Modal
document.querySelectorAll('.openUpdateModal').forEach(btn => {
    btn.addEventListener('click', () => {
        const orderId = btn.dataset.id;
        
        document.getElementById('update_id').value = orderId;
        document.getElementById('display_order_id').textContent = '#' + String(orderId).padStart(6, '0');
        document.getElementById('display_customer').textContent = btn.dataset.customer;
        document.getElementById('display_phone').textContent = btn.dataset.phone || '-';
        document.getElementById('display_address').textContent = btn.dataset.address || '-';
        document.getElementById('display_total').textContent = btn.dataset.total + 'đ';
        document.getElementById('update_status').value = btn.dataset.status;
        
        document.getElementById('updateModal').classList.remove('hidden');
    });
});

document.getElementById('closeUpdateModal').addEventListener('click', () => {
    document.getElementById('updateModal').classList.add('hidden');
});

// Submit Update Status
function submitUpdateStatus(event) {
    event.preventDefault();
    
    const orderId = document.getElementById('update_id').value;
    const status = document.getElementById('update_status').value;
    const updateBtn = document.getElementById('updateBtn');
    
    // Disable button
    updateBtn.disabled = true;
    updateBtn.textContent = 'Updating...';
    
    // Submit via AJAX
    fetch('/admin/orders/update-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: orderId,
            status: status
        })
    })
    .then(res => res.json())
    .then(data => {
        updateBtn.disabled = false;
        updateBtn.textContent = 'Update Status';
        
        if (data.success) {
            // Close update modal
            document.getElementById('updateModal').classList.add('hidden');
            
            // Show success modal
            document.getElementById('successMessage').textContent = data.message || 'Order status updated successfully!';
            document.getElementById('successModal').classList.remove('hidden');
            
            // Reload after 2 seconds
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            alert(data.message || 'Failed to update order status');
        }
    })
    .catch(err => {
        console.error(err);
        updateBtn.disabled = false;
        updateBtn.textContent = 'Update Status';
        alert('An error occurred. Please try again.');
    });
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    location.reload();
}

// Close modals when clicking outside
window.addEventListener('click', (e) => {
    if (e.target === document.getElementById('updateModal')) {
        document.getElementById('updateModal').classList.add('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layout/admin-layout.php";
?>


<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\BaseModel;
use PDO;
use PDOException;
use Exception;

class Order extends BaseModel { 
    public int $id;
    public int $customer_id;
    public ?string $phone;
    public ?string $shipping_address;
    public ?int $created_by;    
    public string $status;
    public float $total_amount;
    public string $created_at;
    public ?string $updated_at;
    
    // For admin display
    public ?string $customer_name = null;
    
    private static function mapRow(array $row): self { 
        $order = new self();
        $order->id = (int)$row["id"];
        $order->customer_id = (int)$row["customer_id"];
        $order->phone = $row["phone"] ?? null;
        $order->shipping_address = $row["shipping_address"] ?? null;
        $order->created_by = $row["created_by"] !== null ? (int) $row["created_by"] : null;
        $order->status = $row["status"];
        $order->total_amount = (float) $row["total_amount"];
        $order->created_at = $row["created_at"];
        $order->updated_at = $row["updated_at"] !== null ? $row["updated_at"] : null;
        return $order;
    }

    public function findById(int $id): ?self{
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? self::mapRow($row) : null;
    }

    public function findByCustomer(int $id, int $customer_id) : ?self {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id AND customer_id = :customer_id");
        $stmt->execute(["id"=> $id, "customer_id"=>$customer_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row?self::mapRow($row):null;
    }

    public function listByCustomer(int $customer_id):array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE customer_id = :customer_id ORDER BY id DESC");
        $stmt->execute(["customer_id"=>$customer_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => self::mapRow($row), $rows);
    }

    public function listAll():array {
        $stmt = $this->db->query("SELECT * FROM orders ORDER BY id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => self::mapRow($row), $rows);
    }

    public function updateStatus(int $id, string $status) : int {
        $stmt = $this->db->prepare("UPDATE orders SET status = :status WHERE id=:id");
        $stmt->execute(["id" => $id, "status" => $status]);
        return $stmt->rowCount();
    }
    public function createOrder(int $customer_id, ?int $created_by, array $items, ?string $phone = null, ?string $shipping_address = null): int{
    if (empty($items)) {
        throw new Exception("Order must contain at least one item");
    }
    try {
        $this->db->beginTransaction();

        $totalAmount     = 0.0;
        $orderItemsData  = [];  
        foreach ($items as $item) {
            $productID = (int)($item['product_id'] ?? 0);
            $quantity  = (int)($item['quantity'] ?? 0);

            if ($productID <= 0 || $quantity <= 0) {
                throw new Exception("Invalid product ID or quantity");
            }

            $stmt = $this->db->prepare(
                "SELECT id, price, stock 
                 FROM products 
                 WHERE id = :id 
                 FOR UPDATE"
            );
            $stmt->execute(['id' => $productID]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product with ID $productID not found");
            }

            if ((int)$product['stock'] < $quantity) {
                throw new Exception("Insufficient stock for product ID $productID");
            }

            $price     = (float)$product['price'];
            $lineTotal = $price * $quantity;
            $totalAmount += $lineTotal;

            $orderItemsData[] = [
                'product_id'        => $productID,
                'quantity'          => $quantity,
                'price_at_purchase' => $price,
                'line_total'        => $lineTotal,
            ];
            $update = $this->db->prepare(
                "UPDATE products 
                 SET stock = stock - :quantity 
                 WHERE id = :id"
            );
            $update->execute([
                'quantity' => $quantity,
                'id'       => $productID,
            ]);
        }

        $insertOrder = $this->db->prepare(
            "INSERT INTO orders (customer_id, phone, shipping_address, created_by, status, total_amount)
             VALUES (:customer_id, :phone, :shipping_address, :created_by, :status, :total_amount)"
        );
        $insertOrder->execute([
            'customer_id'      => $customer_id,
            'phone'            => $phone,
            'shipping_address' => $shipping_address,
            'created_by'       => $created_by,
            'status'           => 'pending',
            'total_amount'     => $totalAmount,
        ]);

        $orderID = (int)$this->db->lastInsertId();

        $insertItem = $this->db->prepare(
            "INSERT INTO order_items 
             (order_id, product_id, quantity, price_at_purchase, line_total)
             VALUES (:order_id, :product_id, :quantity, :price_at_purchase, :line_total)"
        );

        foreach ($orderItemsData as $row) {
            $insertItem->execute([
                'order_id'          => $orderID,
                'product_id'        => $row['product_id'],
                'quantity'          => $row['quantity'],
                'price_at_purchase' => $row['price_at_purchase'],
                'line_total'        => $row['line_total'],
            ]);
        }

        $this->db->commit();
        return $orderID;

    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

}
?>
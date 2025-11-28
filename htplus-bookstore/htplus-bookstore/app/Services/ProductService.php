<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\Product;
use RuntimeException;
class ProductService
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function getAllProducts(): array
    {
        return $this->repository->findAll();
    }

    public function getProductById(int $id): ?Product
    {
        return $this->repository->findById($id);
    }

    public function createProduct(array $data): int
    {
        $this->validateProductData($data);
        $existing = $this->repository->findByName($data['name']);
        if ($existing !== null) {
            throw new RuntimeException("Product name already exists");
        }

        return $this->repository->create($data);
    }

    public function updateProduct(int $id, array $data): bool
    {
        // Check if product exists
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new RuntimeException("Product not found");
        }

        // Validate data
        $this->validateProductData($data);

        $rowsAffected = $this->repository->update($id, $data);
        return $rowsAffected > 0;
    }
    public function deleteProduct(int $id): bool
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new RuntimeException("Product not found");
        }

        $rowsAffected = $this->repository->delete($id);
        return $rowsAffected > 0;
    }
    public function getShopProducts(array $filters, string $sort, int $page, int $perPage): array
    {
        if ($page < 1) {
            $page = 1;
        }

        $totalProducts = $this->repository->countFiltered($filters);
        $totalPages = (int)max(1, ceil($totalProducts / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $products = $this->repository->getFiltered($filters, $sort, $perPage, $offset);

        return [
            'products' => $products,
            'pagination' => [
                'page' => $page,
                'total_pages' => $totalPages,
                'total_products' => $totalProducts,
            ]
        ];
    }
    public function getRelatedProducts(int $categoryId, int $excludeProductId, int $limit = 4): array
    {
        return $this->repository->getRelatedByCategory($categoryId, $excludeProductId, $limit);
    }

    private function validateProductData(array $data): void
    {
        $requiredFields = ['name', 'sku', 'author', 'publisher', 'isbn', 'description'];
        
        foreach ($requiredFields as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                throw new RuntimeException("Field '$field' is required");
            }
        }

        $price = (float)($data['price'] ?? 0);
        $stock = (int)($data['stock'] ?? 0);

        if ($price <= 0) {
            throw new RuntimeException("Price must be greater than 0");
        }

        if ($stock < 0) {
            throw new RuntimeException("Stock cannot be negative");
        }
    }
}


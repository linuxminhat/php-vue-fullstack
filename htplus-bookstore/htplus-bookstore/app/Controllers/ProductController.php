<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Product;
use App\Core\FileUploader;
use App\Models\Category;

class ProductController extends BaseController{

public function index(): void{
    
    $productModel = new Product();
    $products = $productModel->listAllProduct();
        
    $categoryModel = new \App\Models\Category();
    $categories = $categoryModel->listAllCategory();
        
    \App\Core\View::render('products.index', [
        'title' => 'Danh sách sản phẩm',
        'products' => $products,
        'categories' => $categories
    ]);
}

public function listAllProduct(): void{
    $model = new Product();
    $products = $model->listAllProduct();
    $this->json([
        "success" => true,
        "data" => $products,
    ]);
}

public function createNewProduct(): void{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isJson = stripos($contentType, 'application/json') !== false;

    if ($isJson) {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true) ?? [];
    } else {
        $data = $_POST;
    }

    $data['name']      = trim($data['name']      ?? '');
    $data['sku']       = trim($data['sku']       ?? '');
    $data['author']    = trim($data['author']    ?? '');
    $data['publisher'] = trim($data['publisher'] ?? '');
    $data['isbn']      = trim($data['isbn']      ?? '');
    $data['price']     = (float)($data['price'] ?? 0);
    $data['stock']     = (int)($data['stock'] ?? 0);
    $data['description'] = trim($data['description'] ?? '');

    if (
        $data['name'] === "" ||
        $data['sku'] === "" ||
        $data['author'] === "" ||
        $data['publisher'] === "" ||
        $data['isbn'] === "" ||
        $data['description'] === '' 
    ) {
        $this->json([
            "success" => false,
            "message" => "Need to fill in all fields",
        ], 422);
        return;
    }

    if ($data["price"] <= 0 || $data["stock"] < 0) {
        $this->json([
            "success" => false,
            "message" => "Price and Stock must be greater than 0",
        ], 422);
        return;
    }

    //Upload image
    $imageUrl = null;
    if (!$isJson && isset($_FILES['cover_image'])) {
        try {
            $imageUrl = FileUploader::uploadBookImage($_FILES['cover_image']);
        } catch (\RuntimeException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
            return;
        }
    }
    $data['image'] = $imageUrl;    

    $model = new Product();
    $existing = $model->findByName($data["name"]);
    if ($existing !== null) {
        $this->json([
            "success" => false,
            "message" => "Product name already exist",
        ], 409);
        return;
    }

    $id = $model->create($data);
    $product = $model->findById($id);

    $this->json([
        "success" => true,
        "data" => [
            "id"    => $product->id,
            "name"  => $product->name,
            "image" => $product->image,
        ],
    ], 201);
}

public function updateProduct(): void
{
    $contentType = $_SERVER["CONTENT_TYPE"] ?? "";
    $isJson = stripos($contentType, "application/json") !== false;

    if ($isJson) {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true) ?? [];
    } else {
        $data = $_POST;
    }

    $id            = (int)($data['id'] ?? 0);
    $data["name"]  = trim($data["name"] ?? "");
    $data["sku"]   = trim($data["sku"] ?? "");
    $data["isbn"]  = trim($data["isbn"] ?? "");
    $data["price"] = (float)($data["price"] ?? 0);
    $data["stock"] = (int)($data["stock"] ?? 0);
    $data["description"] = trim($data["description"] ?? "");

    if ($id <= 0) {
        $this->json([
            "success" => false,
            "message" => "Invalid ID",
        ], 422);
        return;
    }

    if ($data["name"] === "" || $data["sku"] === "" || $data["description "]) {
        $this->json([
            "success" => false,
            "message" => "Name Or SKU Or Description is required",
        ], 422);
        return;
    }

    if ($data["price"] <= 0 || $data["stock"] < 0) {
        $this->json([
            "success" => false,
            "message" => "Price and Stock need to greater than 0",
        ], 422);
        return;
    }

    $model = new Product();
    $product = $model->findById($id);
    if (!$product) {
        $this->json([
            "success" => false,
            "message" => "Not Found A Product",
        ], 404);
        return;
    }

 
    if (!$isJson && isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        try {
            $imageUrl = FileUploader::uploadBookImage($_FILES['cover_image']);
            $data['image'] = $imageUrl;    
        } catch (\RuntimeException $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
            return;
        }
    }

    $model->update($id, $data);
    $product = $model->findById($id);

    $this->json([
        "success" => true,
        "message" => "Update product successfully",
        "data"    => $product,
    ]);
}

public function deleteProduct(): void {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        $data = $_POST;
    }
    $id = (int)($data["id"] ?? 0);
    if ($id <= 0) {
        $this->json([
            "success" => "false",
            "message" => "Invalid ID",
        ], 422);
    }
    $model = new Product();
    $product = $model->findById($id);
    if (!$product) {
        $this->json([
            "success" => "false",
            "message" => "Not found a Product",
        ], 404);
    }
    $model->delete($id);
    $this->json([
        "success" => "true",
        "message" => "Delete Successfully",
    ]);
}

public function shop(): void
    {
        $productModel  = new Product();
        $categoryModel = new Category();

        $q = trim($_GET['q'] ?? '');

        $categoryIdRaw = $_GET['category_id'] ?? '';
        $category_id = $categoryIdRaw === '' ? null : (int)$categoryIdRaw;

        $sort = $_GET['sort'] ?? 'title_az';

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $perPage = 9;  

        $filters = [
            'q'          => $q,
            'category_id'=> $category_id,
        ];

        $totalProducts = $productModel->countFiltered($filters);
        $totalPages    = (int)max(1, ceil($totalProducts / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;

        $products   = $productModel->getFiltered($filters, $sort, $perPage, $offset);
        $categories = $categoryModel->listAllCategory(); 
        
        \App\Core\View::render('products.shop', [
            'title'          => 'Shop - HTPLUS Book Store',
            'products'       => $products,
            'categories'     => $categories,
            'q'              => $q,
            'current_cat_id' => $category_id,
            'current_sort'   => $sort,
            'page'           => $page,
            'total_pages'    => $totalPages,
            'total_products' => $totalProducts,
        ], 'main');
    }
    public function detail($id){
        $id = (int) $id;
        $productModel = new Product();
        $product = $productModel->findById($id);
        if(!$product) {
            http_response_code(404);
            echo "Product Not Found";
            return;
        }
        
        // Get related products from the same category
        $relatedProducts = [];
        if ($product->category_id) {
            $relatedProducts = $productModel->getRelatedByCategory(
                $product->category_id, 
                $product->id, 
                4  // Limit to 4 related products
            );
        }
        
        \App\Core\View::render('products.product-detail',[
            'title' => $product->name,
            'product' => $product,
            'description' => $product->description,
            'related_products' => $relatedProducts,
        ], 'main');
    }
    
}

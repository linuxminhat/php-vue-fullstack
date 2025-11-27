<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Category;
use App\Core\View;

class CategoryController extends BaseController {
    
    public function listAllCategory(): void{
    $model = new Category();
    $categories = $model->listAllCategory();
    View::render('admin.category.index', [
    'categories' => $categories,
    'page' => $page ?? 1,
    'totalPages' => $totalPages ?? 1
    ], 'main');

    }
    public function createNewCategory(): void {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $this->json([
                "success" => false,
                "message" => "Category name is required",
            ], 422);
        }

        $model = new Category();
        $existing = $model->findByName($name);

        if ($existing !== null) {
            $this->json([
                "success" => false,
                "message" => "Category name already exist",
            ], 400);
        } else {
            $id = $model->create($name);
            $category = $model->findByID($id);
            $this->json([
                "success" => true,
                "data" => [
                    "id" => $category->id,
                    "name" => $category->name,
                ],
            ]);
        }
    }
    
    public function updateCategory(): void
    {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }
        $id = (int)($data['id'] ?? 0);
        $name = trim($data["name"] ?? "");
        if ($id < 0 || $name === '') {
            $this->json([
                "success" => false,
                "message" => "Invalid id or name",
            ], 422);
        }
        $model = new Category();
        $category = $model->findById($id);
        if (!$category) {
            $this->json([
                "success" => "false",
                "message" => "Category not found",
            ], 404);
        } else {
            $model->update($id, $name);
            $category = $model->findById($id);
            $this->json([
                "success" => "true",
                "message" => "Update Category Successfully",
                "data" => $category,
            ]);
        }
    }
    
    public function deleteCategory(): void
    {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $data = $_POST;
        }
        $id = (int)($data['id'] ?? 0);
        if ($id <= 0) {
            $this->json([
                "success" => false,
                "message" => "Invalid ID",
            ], 422);
        }
        $model = new Category();
        $category = $model->findById($id);
        if (!$category) {
            $this->json([
                "success" => false,
                "message" => "Not Found A Categroy",
            ], 404);
        }
        $model->delete($id);
        $this->json([
            "success" => true,
            "message" => "Delete Category Successfully",
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Services\ProductService;

class HomeController extends BaseController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function index(): void
    {
        $perPage = 8;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $filters = [];
        $sort = 'title_az';

        $result = $this->productService->getShopProducts($filters, $sort, $page, $perPage);

        View::render('home.index', [
            'title' => 'HTPLUS Book Store - Trang chủ',
            'products' => $result['products'],
            'page' => $result['pagination']['page'],
            'total_pages' => $result['pagination']['total_pages'],
            'total_products' => $result['pagination']['total_products'],
        ], 'main');
    }
}

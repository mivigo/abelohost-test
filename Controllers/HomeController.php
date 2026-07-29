<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends Controller
{
    /**
     * Display the blog home page.
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $categories = Category::getActiveCategories();
        
        $categoriesData = [];
        foreach ($categories as $category) {
            $categoriesData[] = [
                'model' => $category,
                'posts' => $category->getLatestPosts(3)
            ];
        }

        return $this->render('home.tpl', [
            'title' => 'Главная страница блога',
            'categoriesData' => $categoriesData
        ]);
    }
}

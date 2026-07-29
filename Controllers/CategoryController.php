<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryController extends Controller
{
    /**
     * Display a specific category with its paginated and sorted posts.
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int)$request->getAttribute('id');
        $category = Category::find($id);

        if (!$category) {
            return $this->html("<h1>404 Категория не найдена</h1>", 404);
        }

        $queryParams = $request->getQueryParams();
        $sortBy = $queryParams['sort'] ?? 'date';
        $sortOrder = $queryParams['order'] ?? 'desc';
        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        if ($page < 1) $page = 1;

        $limit = 6;
        $totalPosts = $category->getPostsCount();
        $totalPages = (int)ceil($totalPosts / $limit);
        if ($totalPages < 1) $totalPages = 1;
        if ($page > $totalPages) $page = $totalPages;

        $posts = $category->getPagedPosts($page, $limit, $sortBy, $sortOrder);

        return $this->render('category.tpl', [
            'title' => "Категория: {$category->name}",
            'category' => $category,
            'posts' => $posts,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalPosts' => $totalPosts
        ]);
    }
}

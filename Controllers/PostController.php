<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Post;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostController extends Controller
{
    /**
     * Display a specific post, increment its views, and fetch similar posts.
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int)$request->getAttribute('id');
        $post = Post::find($id);

        if (!$post) {
            return $this->html("<h1>404 Статья не найдена</h1>", 404);
        }

        // Increment views count
        $post->views = (int)$post->views + 1;
        $post->save();

        $categories = $post->categories();
        $similarPosts = $post->getSimilarPosts(3);

        return $this->render('post.tpl', [
            'title' => $post->name,
            'post' => $post,
            'categories' => $categories,
            'similarPosts' => $similarPosts
        ]);
    }
}

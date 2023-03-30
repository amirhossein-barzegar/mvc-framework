<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Post;
use App\Models\User;

class UserController extends BaseController
{
    public function getPosts(): void
    {
        new Post();
        dd(Post::findById(1, 'users'));
    }
    
    public function getApi()
    {
        new Post;
        $posts = Post::all();
        echo json_encode([
            "posts" => $posts
        ]);
    }
    
    public function postApi($request)
    {
        $request->rules = [
            'title' => 'required|min:5',
            'description' => 'required'
        ];
    
        $request->validate($request);
        
        $status = Post::create($request->request);
        
        if ($status) {
            $this->response([
                'status' => $status,
                'message' => 'Post created successfully'
            ], 200);
        } else {
            $this->response([
                'status' => 404,
                'error' => 'some error'
            ], 404);
        }
    }
    
    public function deletePost($id)
    {
        new Post;
        $post = Post::findById($id);
        $status = $post->delete();
        $this->response([
            'status' => $status,
            'message' => 'Post deleted successfully!',
            'post' => $post
        ]);
    }
    
    public function editPost($request, $id)
    {
        new Post;
        $post = Post::findById($id);
        $status = $post->edit($request->request);
        
        $this->response([
            'status' => $status,
            'message'=> 'Post editted successfully!'
        ]);
    }
}
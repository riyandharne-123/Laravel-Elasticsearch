<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elasticsearch;

//requests
use App\Http\Requests\PostRequest;

//models
use App\Models\Post;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $page = !empty($request->query('page')) ? $request->query('page') : 1;
        $per_page = !empty($request->query('per_page')) ? $request->query('per_page') : 3;

        $params = [
            'sort' => [
                'created_at' => [
                    'order' => 'desc'
                ]
            ],
            'size' => $per_page,
            'from' => ($page - 1) * $per_page
        ];

        if (!empty($search)) {
            $params['query'] = [
                'match' => [
                    'title' => $search
                ],
                'match' => [
                    'body' => $search
                ]
            ];
        }

        $posts = ElasticSearch::search([
            'index' => 'posts_index',
            'body' => $params
        ]);

        $total = $posts['hits']['total']['value'];
        $posts = collect($posts['hits']['hits'])->pluck('_source');

        return response()->json([
            'total' => $total,
            'current_page' => intval($page),
            'last_page' => ceil(floatval($total / $per_page)),
            'posts' => $posts
        ], 200);
    }

    public function store(PostRequest $request)
    {
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body
        ]);

        ElasticSearch::index([
            'index' => 'posts_index',
            'id' => $post->id,
            'body' => [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at
            ]
        ]);

        return response()->json($post, 200);
    }

    public function show($id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response()->json([
                'error' => 'Post not found.'
            ], 401);
        }

        return response()->json($post, 200);
    }

    public function update(PostRequest $request, $id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response()->json([
                'error' => 'Post not found.'
            ], 401);
        }

        $post->update([
            'title' => $request->title,
            'body' => $request->body
        ]);

        ElasticSearch::index([
            'index' => 'posts_index',
            'id' => $post->id,
            'body' => [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at
            ]
        ]);

        return response()->json($post, 200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response()->json([
                'error' => 'Post not found.'
            ], 401);
        }

        ElasticSearch::delete([
            'index' => 'posts_index',
            'id' => $post->id,
        ]);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted.'
        ], 200);
    }
}

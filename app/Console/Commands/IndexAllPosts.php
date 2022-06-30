<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elasticsearch;

//models
use App\Models\Post;

class IndexAllPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all posts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $posts = Post::orderBy('created_at', 'desc')
        ->get();

        foreach($posts as $post) {
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
        }

        echo 'Post indexing complete' . PHP_EOL;
    }
}

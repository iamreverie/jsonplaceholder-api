<?php

namespace App\Console\Commands;

use App\Models\JpAlbum;
use App\Models\JpComment;
use App\Models\JpPhoto;
use App\Models\JpPost;
use App\Models\JpTodo;
use App\Models\JpUser;
use App\Services\JsonPlaceholderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchJsonPlaceholderData extends Command
{
    protected $signature = 'app:fetch-jsonplaceholder
                            {--fresh : Truncate existing data before inserting}';

    protected $description = 'Fetch all data from JSONPlaceholder and persist it to the database.';

    public function __construct(private readonly JsonPlaceholderService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->warn('Truncating existing data...');
            $this->truncateAll();
        }

        $this->info('Fetching data from JSONPlaceholder...');
        $data = $this->service->fetchAll();

        DB::transaction(function () use ($data) {
            $this->persistUsers($data['users'] ?? []);
            $this->persistPosts($data['posts'] ?? []);
            $this->persistComments($data['comments'] ?? []);
            $this->persistAlbums($data['albums'] ?? []);
            $this->persistPhotos($data['photos'] ?? []);
            $this->persistTodos($data['todos'] ?? []);
        });

        $this->info('All data has been successfully stored.');

        return Command::SUCCESS;
    }

    private function persistUsers(array $users): void
    {
        $this->info('Saving users...');

        $payload = array_map(fn($u) => [
            'id'         => $u['id'],
            'name'       => $u['name'],
            'username'   => $u['username'],
            'email'      => $u['email'],
            'phone'      => $u['phone'] ?? null,
            'website'    => $u['website'] ?? null,
            'address'    => json_encode($u['address'] ?? []),
            'company'    => json_encode($u['company'] ?? []),
            'created_at' => now(),
            'updated_at' => now(),
        ], $users);

        JpUser::upsert($payload, ['id'], ['name', 'username', 'email', 'phone', 'website', 'address', 'company', 'updated_at']);

        $this->line('  -> Users done: ' . count($users) . ' records.');
    }

    private function persistPosts(array $posts): void
    {
        $this->info('Saving posts...');

        $payload = array_map(fn($p) => [
            'id'         => $p['id'],
            'user_id'    => $p['userId'],
            'title'      => $p['title'],
            'body'       => $p['body'],
            'created_at' => now(),
            'updated_at' => now(),
        ], $posts);

        JpPost::upsert($payload, ['id'], ['user_id', 'title', 'body', 'updated_at']);

        $this->line('  -> Posts done: ' . count($posts) . ' records.');
    }

    private function persistComments(array $comments): void
    {
        $this->info('Saving comments...');

        $payload = array_map(fn($c) => [
            'id'         => $c['id'],
            'post_id'    => $c['postId'],
            'name'       => $c['name'],
            'email'      => $c['email'],
            'body'       => $c['body'],
            'created_at' => now(),
            'updated_at' => now(),
        ], $comments);

        JpComment::upsert($payload, ['id'], ['post_id', 'name', 'email', 'body', 'updated_at']);

        $this->line('  -> Comments done: ' . count($comments) . ' records.');
    }

    private function persistAlbums(array $albums): void
    {
        $this->info('Saving albums...');

        $payload = array_map(fn($a) => [
            'id'         => $a['id'],
            'user_id'    => $a['userId'],
            'title'      => $a['title'],
            'created_at' => now(),
            'updated_at' => now(),
        ], $albums);

        JpAlbum::upsert($payload, ['id'], ['user_id', 'title', 'updated_at']);

        $this->line('  -> Albums done: ' . count($albums) . ' records.');
    }

    private function persistPhotos(array $photos): void
    {
        $this->info('Saving photos...');

        foreach (array_chunk($photos, 500) as $chunk) {
            $payload = array_map(fn($p) => [
                'id'            => $p['id'],
                'album_id'      => $p['albumId'],
                'title'         => $p['title'],
                'url'           => $p['url'],
                'thumbnail_url' => $p['thumbnailUrl'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ], $chunk);

            JpPhoto::upsert($payload, ['id'], ['album_id', 'title', 'url', 'thumbnail_url', 'updated_at']);
        }

        $this->line('  -> Photos done: ' . count($photos) . ' records.');
    }

    private function persistTodos(array $todos): void
    {
        $this->info('Saving todos...');

        $payload = array_map(fn($t) => [
            'id'         => $t['id'],
            'user_id'    => $t['userId'],
            'title'      => $t['title'],
            'completed'  => $t['completed'],
            'created_at' => now(),
            'updated_at' => now(),
        ], $todos);

        JpTodo::upsert($payload, ['id'], ['user_id', 'title', 'completed', 'updated_at']);

        $this->line('  -> Todos done: ' . count($todos) . ' records.');
    }

    private function truncateAll(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        JpComment::truncate();
        JpPhoto::truncate();
        JpPost::truncate();
        JpAlbum::truncate();
        JpTodo::truncate();
        JpUser::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Tables truncated.');
    }
}

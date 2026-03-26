<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JsonPlaceholderService
{
    private const BASE_URL = 'https://jsonplaceholder.typicode.com';

    private const ENDPOINTS = [
        'users',
        'posts',
        'comments',
        'albums',
        'photos',
        'todos',
    ];

    public function fetchAll(): array
    {
        $responses = Http::pool(function ($pool) {
            return array_map(
                fn($endpoint) => $pool->as($endpoint)->timeout(30)->get(self::BASE_URL . '/' . $endpoint),
                self::ENDPOINTS
            );
        });

        $data = [];

        foreach (self::ENDPOINTS as $endpoint) {
            if ($responses[$endpoint]->successful()) {
                $data[$endpoint] = $responses[$endpoint]->json();
                Log::info("Fetched {$endpoint}: " . count($data[$endpoint]) . ' records.');
            } else {
                Log::error("Failed to fetch {$endpoint}. Status: " . $responses[$endpoint]->status());
                $data[$endpoint] = [];
            }
        }

        return $data;
    }
}

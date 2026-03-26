<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JpPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JpPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $posts = JpPost::paginate($this->getPerPage($request));
        return response()->json($posts);
    }

    public function show(int $id): JsonResponse
    {
        $post = JpPost::findOrFail($id);
        return response()->json($user);
    }
}
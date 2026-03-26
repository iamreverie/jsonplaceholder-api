<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JpTodo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JpTodoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $todos = JpTodo::paginate($this->getPerPage($request));
        return response()->json($todos);
    }

    public function show(int $id): JsonResponse
    {
        $todo = JpTodo::findOrFail($id);
        return response()->json($todo);
    }
}
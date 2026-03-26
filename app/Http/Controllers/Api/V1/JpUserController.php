<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JpUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JpUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = JpUser::paginate($this->getPerPage($request));
        return response()->json($users);
    }

    public function show(int $id): JsonResponse
    {
        $user = JpUser::findOrFail($id);
        return response()->json($user);
    }
}
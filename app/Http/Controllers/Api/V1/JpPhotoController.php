<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JpPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JpPhotoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $photos = JpPhoto::paginate($this->getPerPage($request));
        return response()->json($photos);
    }

    public function show(int $id): JsonResponse
    {
        $photo = JpPhoto::findOrFail($id);
        return response()->json($photo);
    }
}
<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JpAlbum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JpAlbumController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $albums = JpAlbum::paginate($this->getPerPage($request));
        return response()->json($albums);
    }

    public function show(int $id): JsonResponse
    {
        $album = JpAlbum::findOrFail($id);
        return response()->json($album);
    }
}
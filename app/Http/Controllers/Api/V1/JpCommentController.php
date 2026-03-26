<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JpComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JpCommentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $comments = JpComment::paginate($this->getPerPage($request));
        return response()->json($comments);
    }

    public function show(int $id): JsonResponse
    {
        $comment = JpComment::findOrFail($id);
        return response()->json($comment);
    }
}
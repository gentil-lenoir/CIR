<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CommentController extends Controller
{
    public function store(Request $request): CommentResource|JsonResponse
    {
        try {
            $validated = $request->validate([
                'issue_id' => ['required', 'exists:issues,id'],
                'comment' => ['required', 'string', 'max:1000'],
            ]);

            $comment = Comment::create([
                'issue_id' => $validated['issue_id'],
                'user_id' => $request->user()->id,
                'comment' => $validated['comment'],
            ]);

            $comment->load('user');

            return new CommentResource($comment);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to add comment.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function index(int $id): JsonResponse
    {
        try {
            $issue = Issue::findOrFail($id);
            $comments = $issue->comments()->with('user')->latest()->paginate(20);

            return response()->json([
                'message' => 'Comments fetched successfully.',
                'data' => CommentResource::collection($comments),
                'meta' => [
                    'total' => $comments->total(),
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to fetch comments.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}

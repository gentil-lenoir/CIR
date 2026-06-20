<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Upvote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpvoteController extends Controller
{
    public function store(Request $request, int $id): JsonResponse
    {
        try {
            $issue = Issue::findOrFail($id);

            $upvote = Upvote::where('issue_id', $issue->id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($upvote) {
                return response()->json([
                    'message' => 'You have already upvoted this issue.',
                    'data' => [
                        'upvotes_count' => $issue->upvotes_count,
                    ],
                ]);
            }

            DB::transaction(function () use ($issue, $request) {
                Upvote::create([
                    'issue_id' => $issue->id,
                    'user_id' => $request->user()->id,
                ]);

                $issue->increment('upvotes_count');

            });

            return response()->json([
                'message' => 'Issue upvoted successfully.',
                'data' => [
                    'upvotes_count' => $issue->fresh()->upvotes_count,
                ],
            ], 201);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to upvote issue.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}

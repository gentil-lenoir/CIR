<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\IssueAssignment;
use App\Models\IssueStatusHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class AssignmentController extends Controller
{
    public function assignIssue(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'issue_id' => ['required', 'exists:issues,id'],
                'worker_id' => ['required', 'exists:users,id'],
                'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
                'deadline_at' => ['nullable', 'date'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $worker = User::findOrFail($validated['worker_id']);
            if ($worker->role !== User::ROLE_WORKER) {
                return response()->json([
                    'message' => 'Assigned user must have worker role.',
                ], 422);
            }

            $issue = Issue::findOrFail($validated['issue_id']);

            DB::transaction(function () use ($validated, $request, $issue) {
                IssueAssignment::updateOrCreate(
                    ['issue_id' => $validated['issue_id']],
                    [
                        'worker_id' => $validated['worker_id'],
                        'assigned_by' => $request->user()->id,
                        'status' => 'assigned',
                        'deadline_at' => $validated['deadline_at'] ?? null,
                        'notes' => $validated['notes'] ?? null,
                    ]
                );

                $oldStatus = $issue->status;
                $issue->update([
                    'status' => 'assigned',
                    'priority' => $validated['priority'],
                    'escalated_to' => null,
                    'escalated_at' => null,
                ]);

                IssueStatusHistory::create([
                    'issue_id' => $issue->id,
                    'changed_by' => $request->user()->id,
                    'from_status' => $oldStatus,
                    'to_status' => 'assigned',
                    'notes' => $validated['notes'] ?? 'Issue assigned to worker.',
                ]);

            });

            return response()->json([
                'message' => 'Issue assigned successfully.',
            ], 201);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to assign issue.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}

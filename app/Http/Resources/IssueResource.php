<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'status' => $this->status,
            'priority' => $this->priority,
            'location' => [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
                'address' => $this->address,
            ],
            'reported_at' => optional($this->reported_at)->toISOString(),
            'upvotes_count' => $this->upvotes_count,
            'comments_count' => $this->whenCounted('comments'),
            'reporter' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'role' => $this->user?->role,
            ],
            'feedback' => $this->citizen_feedback,
            'feedback_submitted_at' => optional($this->feedback_submitted_at)->toISOString(),
            'escalation' => $this->status === 'escalated' ? [
                'escalated_at' => optional($this->escalated_at)->toISOString(),
                'system_manager' => [
                    'id' => $this->escalatedTo?->id,
                    'name' => $this->escalatedTo?->name,
                ],
            ] : null,
            'images' => $this->images->map(fn ($image) => [
                'id' => $image->id,
                'url' => $image->image_url,
                'path' => $image->image_path,
            ])->values(),
            'assignment' => $this->assignment ? [
                'id' => $this->assignment->id,
                'worker' => [
                    'id' => $this->assignment->worker?->id,
                    'name' => $this->assignment->worker?->name,
                ],
                'assigned_by' => [
                    'id' => $this->assignment->assignedBy?->id,
                    'name' => $this->assignment->assignedBy?->name,
                ],
                'status' => $this->assignment->status,
                'assigned_at' => optional($this->assignment->assigned_at)->toISOString(),
                'deadline_at' => optional($this->assignment->deadline_at)->toISOString(),
                'notes' => $this->assignment->notes,
                'proof_url' => $this->assignment->proof_url,
            ] : ($this->worker ? [
                'id' => null,
                'worker' => [
                    'id' => $this->worker?->id,
                    'name' => $this->worker?->name,
                ],
                'assigned_by' => null,
                'status' => $this->status,
                'assigned_at' => null,
                'deadline_at' => optional($this->deadline)->toISOString(),
                'notes' => null,
                'proof_url' => null,
            ] : null),
            'worker' => $this->worker ? [
                'id' => $this->worker->id,
                'name' => $this->worker->name,
                'email' => $this->worker->email,
                'department' => $this->worker->department?->name,
            ] : null,
            'deadline' => optional($this->deadline)->toDateString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

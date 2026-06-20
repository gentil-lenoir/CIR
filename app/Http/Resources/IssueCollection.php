<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IssueCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => IssueResource::collection($this->collection),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total' => method_exists($this->resource, 'total') ? $this->resource->total() : $this->collection->count(),
            ],
        ];
    }
}

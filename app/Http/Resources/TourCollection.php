<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TourCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => TourResource::collection($this->collection),
            'meta' => [
                'total' => $this->total(),
                'page' => $this->currentPage(),
                'limit' => $this->perPage(),
            ]
        ];
    }
}

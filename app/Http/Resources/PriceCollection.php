<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PriceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => PriceResource::collection($this->collection),
            'meta' => [
                'total' => $this->total(),
                'page'  => $this->currentPage(),
                'limit' => $this->perPage(),
            ]
        ];
    }
}

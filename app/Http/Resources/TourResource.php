<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this['id'],
            'title'       => $this['title'],
            'description' => $this['description'],
            //Todo: fix the country field to dont show on show.
            'country'     => $this['country'] ?? null,
            'city'        => $this['city']
        ];
    }
}

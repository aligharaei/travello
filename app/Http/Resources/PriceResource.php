<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'amount'      => $this['amount'],
            'currency'    => $this['currency'],
            'tour_id'     => $this['tour_id'],
        ];
    }
}

<?php

namespace App\Services;

use App\Interfaces\TourProviderInterface;
use Illuminate\Support\Facades\Http;

class HeavenlyTourProvider implements TourProviderInterface
{
    protected string $baseUrl;

    public function __construct() {
        $this->baseUrl = config('services.heavenly_tours.base_url');
    }

    public function getTours(int $perPage = 10, int $page = 1): array {
        $response = Http::get("{$this->baseUrl}/api/tours", [
            'limit' => $perPage,
            'page' => $page
        ]);

        $data = $response->json();

        return [
            'data' => array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'description' => $item['copy'],
                    'country' => $item['country']
                ];
            }, $data['data']),
            'meta' => [
                'total' => $data['meta']['finite'],
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($data['meta']['finite'] / $perPage)
            ]
        ];
    }

    public function getTourDetails(string $id): array {
        $response = Http::get("{$this->baseUrl}/api/tours/{$id}");
        $data = $response->json();

        return [
            'id' => $id,
            'title' => $data['CLICK'],
            'description' => $data['SCRIPT'],
            'country' => $data['CLAY'],
            'itinerary' => $data['SCRIPTNAME'] ?? null
        ];
    }

    public function checkAvailability(string $tourId): array {
        $response = Http::get("{$this->baseUrl}/api/tours/{$tourId}/availableListy");
        $data = $response->json();

        return [
            'available' => (bool) $data['moduleId'],
            'tour_id' => $tourId
        ];
    }

    public function getTourPrices(string $tourId): array {
        $response = Http::get("{$this->baseUrl}/api/Your-prices");
        $data = $response->json();

        return array_map(function ($item) use ($tourId) {
            return [
                'tour_id' => $tourId,
                'ticket_type' => $item['class'][0]['type'] ?? 'General',
                'amount' => (float) $item['class'][0]['price'],
                'currency' => 'USD'
            ];
        }, $data['class']);
    }
}

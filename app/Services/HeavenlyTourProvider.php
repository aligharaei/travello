<?php

namespace App\Services;

use App\Interfaces\TourProviderInterface;
use Illuminate\Support\Facades\Http;

class HeavenlyTourProvider implements TourProviderInterface
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.heavenly_tours.base_url');
    }

    public function getTours(int $perPage = 10, int $page = 1): array
    {
        $response = Http::withoutVerifying()->get("{$this->baseUrl}/api/tours", [
            'limit' => $perPage,
            'page'  => $page
        ]);

        $data = $response->json();

        return [
            'data' => collect($data['data'])->map(fn($tour) => $this->normalizeTourListItem($tour))->all(),
            'meta' => $data['meta'] ?? [],
        ];
    }

    protected function normalizeTourListItem(array $data): array
    {
        return [
            'id'          => $data['id'],
            'title'       => $data['title'],
            'description' => $data['excerpt'] ?? null,
            'country'     => $data['country'] ?? null,
            'city'        => $data['city'] ?? null,
        ];
    }

    public function getTourDetails(string $id): array
    {
        $response = Http::withoutVerifying()->get("{$this->baseUrl}/api/tours/{$id}");
        $data = $response->json();
        return $this->normalizeTourDetails($data);
    }

    protected function normalizeTourDetails(array $data): array
    {
        return [
            'id'          => $data['id'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'city'        => $data['city'],
            'categories'  => $data['categories'] ?? [],
            'photos'      => $data['photos'] ?? [],
        ];
    }

    public function checkAvailability(string $tourId): array
    {
        // TODO: Implement checkAvailability() method.
    }

    public function getTourPrices(string $tourId): array
    {
        // TODO: Implement getTourPrices() method.
    }
}

<?php

namespace App\Services;

use App\Interfaces\TourProviderInterface;
use Exception;
use Illuminate\Support\Facades\Cache;
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
        $cacheKey = "heavenly_tours_list_{$perPage}_{$page}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($perPage, $page) {
            try {
                $response = Http::retry(5, 100)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/api/tours", [
                        'limit' => $perPage,
                        'page'  => $page
                    ]);

                if (!$response->successful()) {
                    throw new Exception("Failed to fetch tours list");
                }

                $data = $response->json();
                return [
                    'data' => collect($data['data'])->map(fn($tour) => $this->normalizeTourListItem($tour))->all(),
                    'meta' => $data['meta'] ?? [],
                ];
            } catch (Exception) {
                throw new Exception("Failed to fetch tours list");
            }
        });
    }

    public function getTourDetails(string $id): array
    {
        $cacheKey = "heavenly_tour_details_{$id}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($id) {
            try {
                $response = Http::retry(5, 100)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/api/tours/{$id}");

                if (!$response->successful()) {
                    throw new Exception("Failed to fetch tour details. id: {$id}");
                }

                return $this->normalizeTourDetails($response->json());

            } catch (Exception) {
                throw new Exception("Failed to fetch tour details. id: {$id}");
            }
        });
    }

    public function checkAvailability(string $tourId): array
    {
        $cacheKey = "heavenly_availability_{$tourId}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($tourId) {
            try {
                $response = Http::retry(5, 100)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/api/tours/{$tourId}/availability");

                if (!$response->successful()) {
                    throw new Exception("Failed to check availability for tour {$tourId}");
                }

                return $this->normalizeAvailability($response->json(), $tourId);

            } catch (Exception) {
                throw new Exception("Failed to check availability for tour {$tourId}");
            }
        });
    }

    public function getTourPrices(int $perPage = 10, int $page = 1): array
    {
        $cacheKey = "heavenly_prices_{$perPage}_{$page}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($perPage, $page) {
            try {
                $response = Http::retry(5, 100)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/api/tour-prices", [
                        'limit' => $perPage,
                        'page'  => $page
                    ]);

                if (!$response->successful()) {
                    throw new Exception("Failed to fetch tour prices");
                }

                $data = $response->json();
                return collect($data['data'] ?? [])->map(fn($item) => $this->normalizeTourPrice($item))->all();

            } catch (Exception) {
                throw new Exception("Failed to fetch tour prices");
            }
        });
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

    protected function normalizeAvailability(array $data, string $tourId): array
    {
        $isAvailable = (bool)($data['available'] ?? false);
        return [
            'tour_id'   => $tourId,
            'available' => $isAvailable,
            'status'    => $isAvailable ? __('tour.available') : __('tour.unavailable'),
        ];
    }

    protected function normalizeTourPrice($item): array
    {
        $rawPrice = $item['price'];
        $symbol = mb_substr(trim($rawPrice), 0, 1);

        $currencyMap = [
            '$'   => __('currency.usd'),
            '€'   => __('currency.eur'),
            'AED' => __('currency.aed'),
        ];

        $currency = $currencyMap[$symbol] ?? 'UNKNOWN';
        $amount = (int)trim(mb_substr($rawPrice, 1));

        return [
            'tour_id'  => $item['tourId'],
            'amount'   => $amount,
            'currency' => $currency,
        ];
    }
}

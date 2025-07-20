<?php

namespace App\Services;

use App\Interfaces\TourProviderInterface;
use Exception;
use Illuminate\Http\Client\ConnectionException;
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
        try {
            $response = Http::retry(1, 100)
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
        try {

            $response = Http::retry(5, 100)
                ->withoutVerifying()
                ->get("{$this->baseUrl}/api/tours/{$id}");

            if (!$response->successful()) {
                throw new Exception("Failed to fetch tour details. id: {$id} ");
            }

            $data = $response->json();
            return $this->normalizeTourDetails($data);

        } catch (Exception) {
            throw new Exception("Failed to fetch tour details. id: {$id} ");
        }

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
        try {
            $response = Http::retry(5, 100)->withoutVerifying()->get("{$this->baseUrl}/api/tours/{$tourId}/availability");

            if (!$response->successful()) {
                throw new Exception("Failed to check the tour {$tourId} , availability.");
                //Todo: add logs
                //Todo: add cache mechanism
            }

            $data = $response->json();

            return $this->normalizeAvailability($data, $tourId);
        } catch (Exception) {
            throw new Exception("Failed to check the tour {$tourId} , availability.");
        }

    }

    protected function normalizeAvailability(array $data, string $tourId): array
    {
        return [
            'tour_id'   => $tourId,
            'available' => (bool)($data['available'] ?? false),
        ];
    }

    /**
     * @throws ConnectionException|Exception
     */
    public function getTourPrices(int $perPage = 10, int $page = 1): array
    {
        try {
            $response = Http::retry(5, 100)->withoutVerifying()->get("{$this->baseUrl}/api/tour-prices", [
                'limit' => $perPage,
                'page'  => $page
            ]);

            if (!$response->successful()) {
                throw new Exception("Failed to fetch tour prices");
            }

            $data = $response->json();
            return collect($data['data'] ?? [])->map(function ($item) {
                return $this->normalizeTourPrice($item);
            })->all();
        } catch (Exception) {
            throw new Exception("Failed to fetch tour prices");
        }
    }

    protected function normalizeTourPrice($item): array
    {
        $rawPrice = $item['price'];

        // Trim and extract the first character as the symbol
        $symbol = mb_substr(trim($rawPrice), 0, 1);

        $currencyMap = [
            '$' => 'USD',
            '€' => 'EUR',
            '£' => 'GBP',
            '¥' => 'JPY',
            // Add more as needed
        ];

        $currency = $currencyMap[$symbol] ?? 'UNKNOWN';

        // Remove the symbol from the price
        $numeric = trim(mb_substr($rawPrice, 1));
        $amount = (int)$numeric;

        return [
            'tour_id'  => $item['tourId'],
            'amount'   => $amount,
            'currency' => $currency,
        ];
    }
}

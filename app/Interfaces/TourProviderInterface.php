<?php

namespace App\Interfaces;

interface TourProviderInterface
{
    public function getTours(int $perPage = 10, int $page = 1): array;

    public function getTourDetails(string $id): array;

    public function checkAvailability(string $tourId): array;

    public function getTourPrices(string $tourId): array;
}

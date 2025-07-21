<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailabilityResource;
use App\Http\Resources\PriceCollection;
use App\Http\Resources\TourCollection;
use App\Http\Resources\TourResource;
use App\Interfaces\TourProviderInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TourController extends Controller
{
    public function __construct(
        protected TourProviderInterface $tourProvider
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('limit', 10);
            $page = $request->input('page', 1);

            $tours = $this->tourProvider->getTours($perPage, $page);
            $paginator = $this->createPaginator($tours, $request);

            return success('', new TourCollection($paginator));
        } catch (Exception) {
            return failed(__('message.tour.error.unavailable'));
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $tour = $this->tourProvider->getTourDetails($id);
            return success('', new TourResource($tour));

        } catch (Exception) {
            return failed(__('message.tour.detail.error.fetch_failed'));
        }
    }

    public function availability(string $id): JsonResponse
    {

        try {
            $availability = $this->tourProvider->checkAvailability($id);
            return success('', new AvailabilityResource($availability));
        } catch (Exception) {
            return failed(__('message.tour.availability.error.fetch_failed'));
        }
    }

    public function prices(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('limit', 10);
            $page = $request->input('page', 1);

            $prices = $this->tourProvider->getTourPrices($perPage, $page);
            $paginator = $this->createPaginator($prices, $request);

            return success('', new PriceCollection($paginator));
        } catch (Exception $e) {
            return failed(__('message.tour.price.error.fetch_failed'));
        }
    }

    private function createPaginator(array $data, Request $request): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            collect($data['data']),
            $data['meta']['total'],
            $data['meta']['limit'],
            $data['meta']['page'],
            [
                'path'     => $request->url(),
                'pageName' => 'page',
            ]
        );
    }
}

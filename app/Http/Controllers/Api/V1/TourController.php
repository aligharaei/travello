<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailabilityResource;
use App\Http\Resources\PriceCollection;
use App\Http\Resources\TourCollection;
use App\Http\Resources\TourResource;
use App\Interfaces\TourProviderInterface;
use Exception;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function __construct(
        protected TourProviderInterface $tourProvider
    )
    {
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('limit', 10);
            $page = $request->input('page', 1);

            $data = $this->tourProvider->getTours($perPage, $page);
            //Todo fix the pagination and add it in request apidog
            return success('', new TourCollection(collect($data['data'])));
        } catch (Exception) {
            //Todo: translate and localization implementation
            return failed('Service temporarily unavailable');
        }
    }

    public function show(string $id)
    {
        try {
            $tour = $this->tourProvider->getTourDetails($id);
            return success('', new TourResource($tour));

        } catch (Exception) {
            return failed('Unable to fetch tour details');
        }
    }

    public function availability(string $id)
    {
        try {
            $availability = $this->tourProvider->checkAvailability($id);
            return success('', new AvailabilityResource($availability));
        } catch (Exception) {
            return failed('Unable to check availability');
        }
    }

    public function prices(Request $request)
    {
        try {
            $perPage = $request->input('limit', 10);
            $page = $request->input('page', 1);

            $prices = $this->tourProvider->getTourPrices($perPage, $page);
            return success('', new PriceCollection($prices));
        } catch (Exception) {
            return failed('Unable to fetch prices');
        }
    }
}

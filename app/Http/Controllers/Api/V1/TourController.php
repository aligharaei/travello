<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailabilityResource;
use App\Http\Resources\TourCollection;
use App\Http\Resources\TourResource;
use App\Interfaces\TourProviderInterface;
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
        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $data = $this->tourProvider->getTours($perPage, $page);
        return new TourCollection(collect($data['data']));
    }

    public function show(string $id)
    {
        $tour = $this->tourProvider->getTourDetails($id);
        return new TourResource($tour);
    }

    public function availability(string $id)
    {
        $availability = $this->tourProvider->checkAvailability($id);
        return new AvailabilityResource($availability);
    }
}

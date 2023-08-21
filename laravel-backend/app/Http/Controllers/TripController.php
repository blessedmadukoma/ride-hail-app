<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    // store stores the trip information
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required|date',
        ]);

        $trip = $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));

        return $trip;
    }

    public function getTrip(Request $request, Trip $trip)
    {
        // if trip is associated with authenticated user, return the trip
        if ($trip->user->id === $request->user()->id) {
            return $trip;
        }

        if ($trip->driver_id === $request->user()->driver) {
            if ($trip->driver->id === $request->user()->driver->id) {
                return $trip;
            }
        }


        return response()->json(['message' => 'Cannot find this trip.'], 404);
    }
}

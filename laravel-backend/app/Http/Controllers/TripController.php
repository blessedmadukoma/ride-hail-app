<?php

namespace App\Http\Controllers;

use App\Events\TripAccepted;
use App\Events\TripCreated;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Events\TripStarted;
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
            'destination_name' => 'required',
        ]);

        $trip = $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));

        TripCreated::dispatch($trip, $request->user());

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

    public function accept(Request $request, Trip $trip)
    {
        // driver accepts a trip
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->driver->id,
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        TripAccepted::dispatch($trip, $trip->user);

        return $trip;
    }

    public function start(Request $request, Trip $trip)
    {

        // driver has started a trip
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        TripStarted::dispatch($trip, $request->user());

        return $trip;
    }

    public function end(Request $request, Trip $trip)
    {
        // driver has ended a trip
        $trip->update([
            'is_complete' => true
        ]);

        $trip->load('driver.user');

        TripEnded::dispatch($trip, $request->user());

        return $trip;
    }

    public function location(Request $request, Trip $trip)
    {
        // update the diver's current location
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        TripLocationUpdated::dispatch($trip, $request->user());

        return $trip;
    }
}

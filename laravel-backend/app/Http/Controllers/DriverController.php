<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function getDriver(Request $request)
    {
        // return user and associated driver model
        $user = $request->user();
        $user->load('driver'); // load: returns a user with an associated driver object/model

        return $user;
    }

    public function updateDriver(Request $request)
    {
        // update user and associated driver model
        $request->validate([
            'year' => 'required|numeric|between:2010,2024',
            'make' => 'required',
            'model' => 'required',
            'color' => 'required|alpha',
            'license_plate' => 'required',
            'name' => 'required'
        ]);

        $user = $request->user();

        // update user's namae
        $user->update($request->only('name'));

        // create or update a driver associated with this user
        $user->driver()->updateOrCreate($request->only([
            'year',
            'make',
            'model',
            'color',
            'license_plate'
        ]));

        $user->load('driver');

        return $user;
    }
}

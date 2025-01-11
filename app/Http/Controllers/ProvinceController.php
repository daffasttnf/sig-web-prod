<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::all();

         \Log::info('Provinces Data:', $provinces->toArray()); // Tambahkan log


        $features = $provinces->map(function ($province) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'name' => $province->name,
                    'population' => $province->population,
                ],
                'geometry' => [
                    'type' => $province->type_polygon,
                    'coordinates' => json_decode($province->polygon),
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}

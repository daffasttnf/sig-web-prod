<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::all();

        \Log::info('Provinces Data:', $provinces->toArray()); // Debug log untuk data

        // Map data untuk GeoJSON
        $features = $provinces->map(function ($province) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'name' => $province->name,
                    'population' => $province->population,
                    'main_river' => $province->river, // Sungai utama
                    'water_quality' => $province->water_quality, // Kualitas air
                    'ika' => $province->ika, // IKA (Water Index)
                    'soil_type' => $province->soil_type, // Tipe tanah
                    'soil_characteristics' => $province->soil_characteristics, // Karakteristik tanah
                    'rainfall' => $province->rainfall, // Curah hujan (mm)
                    'rainfall_category' => $province->rainfall_category, // Kategori curah hujan
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

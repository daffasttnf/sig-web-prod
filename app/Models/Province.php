<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';

    protected $fillable = [
        'name',
        'alt_name',
        'latitude',
        'longitude',
        'population',
        'type_polygon',
        'polygon',
        'river',
        'water_quality',
        'ika',
        'soil_type',
        'soil_characteristics',
        'rainfall',
        'rainfall_category',
    ];
    
}
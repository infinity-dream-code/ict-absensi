<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'location_name',
        'latitude',
        'longitude',
        'radius',
    ];

    protected $casts = [
        'check_in_start' => 'string',
        'check_in_end' => 'string',
        'check_out_start' => 'string',
        'check_out_end' => 'string',
    ];

    public static function getSettings()
    {
        return static::first() ?? static::create([
            'check_in_start' => '08:00:00',
            'check_in_end' => '09:00:00',
            'check_out_start' => '17:00:00',
            'check_out_end' => '18:00:00',
            'radius' => 100,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryStop extends Model
{
    protected $table = 'service_itinerary_stops';

    protected $fillable = [
        'transport_service_id',
        'name',
        'visit_time',
        'address',
        'google_maps_url',
        'latitude',
        'longitude',
        'sort_order',
        'status',
        'added_by_driver',
        'note',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'added_by_driver' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    public function service()
    {
        return $this->belongsTo(
            TransportService::class,
            'transport_service_id'
        );
    }

    public function events()
    {
        return $this->hasMany(
            SpkEvent::class,
            'itinerary_stop_id'
        );
    }
}

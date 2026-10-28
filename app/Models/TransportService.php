<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportService extends Model
{
    protected $fillable = [
        'service_code',
        'customer_name',
        'customer_phone',
        'service_type_id',
        'service_date',
        'service_end_date',
        'start_time',
        'end_time',
        'pickup_location',
        'notes',
        'driver_id',
        'vehicle_id',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'service_end_date' => 'date',
        ];
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function stops()
    {
        return $this->hasMany(
            ItineraryStop::class,
            'transport_service_id',
            'id'
        )->orderBy('sort_order');
    }

    public function spk()
    {
        return $this->hasOne(Spk::class);
    }
}

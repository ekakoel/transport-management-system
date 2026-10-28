<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'reservation_number', 'user_id', 'service_type_id', 'customer_name',
        'customer_phone', 'guest_count', 'service_date', 'service_end_date', 'start_time',
        'end_time', 'pickup_location', 'itinerary', 'notes', 'status',
        'admin_notes', 'transport_service_id', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'service_end_date' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function requester() { return $this->belongsTo(User::class, 'user_id'); }
    public function serviceType() { return $this->belongsTo(ServiceType::class); }
    public function transportService() { return $this->belongsTo(TransportService::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}

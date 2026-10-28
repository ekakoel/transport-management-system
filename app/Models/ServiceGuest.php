<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceGuest extends Model
{
    protected $fillable = ['transport_service_id', 'name', 'gender', 'age_category', 'phone'];

    public function service()
    {
        return $this->belongsTo(TransportService::class, 'transport_service_id');
    }
}

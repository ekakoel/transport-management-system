<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationGuest extends Model
{
    protected $fillable = ['reservation_id', 'name', 'gender', 'age_category', 'phone'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}

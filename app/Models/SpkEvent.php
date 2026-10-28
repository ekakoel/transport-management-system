<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SpkEvent extends Model {
 public $timestamps=false;
 protected $fillable=['spk_id','transport_service_id','itinerary_stop_id','event_type','latitude','longitude','accuracy_meters','note','actor_type','actor_id','created_at','metadata'];
 protected function casts(): array { return ['created_at'=>'datetime','metadata'=>'array']; }
 public function stop(){return $this->belongsTo(ItineraryStop::class,'itinerary_stop_id');}
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Spk extends Model {
 protected $fillable=['transport_service_id','spk_number','token_hash','published_at','revoked_at','emergency_name','emergency_phone','emergency_whatsapp'];
 protected function casts(): array { return ['published_at'=>'datetime','revoked_at'=>'datetime']; }
 public function service(){return $this->belongsTo(TransportService::class,'transport_service_id');}
 public function events(){return $this->hasMany(SpkEvent::class);}
 public function isTokenValid(string $token): bool { return $this->revoked_at === null && hash_equals($this->token_hash, hash('sha256',$token)); }
}
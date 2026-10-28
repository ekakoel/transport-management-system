<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Driver extends Model {
 protected $fillable=['name','phone','license_number','is_active','notes'];
 protected function casts(): array { return ['is_active'=>'boolean']; }
 public function services(){return $this->hasMany(TransportService::class);}
}
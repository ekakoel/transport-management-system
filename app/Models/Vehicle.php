<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Vehicle extends Model {
 protected $fillable=['name','ownership_type','capacity','status','notes'];
 public function services(){return $this->hasMany(TransportService::class);}
}
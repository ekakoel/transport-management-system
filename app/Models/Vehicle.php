<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Vehicle extends Model {
 protected $fillable=['name','plate_number','capacity','status','notes'];
 public function services(){return $this->hasMany(TransportService::class);}
}
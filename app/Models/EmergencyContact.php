<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmergencyContact extends Model {
 protected $fillable=['name','phone','whatsapp','is_default','is_active'];
 protected function casts(): array { return ['is_default'=>'boolean','is_active'=>'boolean']; }

 public static function activeDefault(): ?self
 {
  return static::query()
   ->where('is_active', true)
   ->where('is_default', true)
   ->orderBy('id')
   ->first();
 }
}
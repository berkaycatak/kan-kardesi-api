<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BloodRequest extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'required_blood_type_id', 'units_needed', 'hospital_name', 'city', 'district', 'address', 'latitude', 'longitude', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requiredBloodType()
    {
        return $this->belongsTo(BloodType::class, 'required_blood_type_id');
    }
}

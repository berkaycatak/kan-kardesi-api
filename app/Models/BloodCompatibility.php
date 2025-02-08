<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BloodCompatibility extends Model
{
    use HasFactory;
    protected $fillable = ['donor_blood_type_id', 'recipient_blood_type_id'];

    public function donorBloodType()
    {
        return $this->belongsTo(BloodType::class, 'donor_blood_type_id');
    }

    public function recipientBloodType()
    {
        return $this->belongsTo(BloodType::class, 'recipient_blood_type_id');
    }
}

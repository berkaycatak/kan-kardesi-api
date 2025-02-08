<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BloodType extends Model
{
    use HasFactory;
    protected $fillable = ['type'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

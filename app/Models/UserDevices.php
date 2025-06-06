<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevices extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

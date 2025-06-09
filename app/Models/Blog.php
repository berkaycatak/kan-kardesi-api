<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    public function user()
    {
        return $this->hasOne(User::class,  'id', 'created_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function User()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}

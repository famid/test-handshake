<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarazIntegration extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'shop_id',
        'user_code',
        'app_key',
        'secret_key',
        'access_token',
        'refresh_token',
        'user_info'
    ];
}

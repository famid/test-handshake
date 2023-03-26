<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'package_id',
        'user_id',
        'user_type',
        'remain_product',
        'remain_warehouse',
        'remain_daraz_sync',
        'subscription_start',
        'subscription_end',
        'status',
    ];

    public function User()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function Package()
    {
        return $this->hasOne(Package::class,'id','package_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $fillable = [
        'package_name',
        'package_price',
        'product_limit',
        'warehouse_limit',
        'daraz_sync_limit',
        'package_image',
        'package_duration',
        'status',
        'package_type',
        'additional_packages'
    ];
}

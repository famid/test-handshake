<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';
    protected $guarded = [];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $inventory_translations = $this->inventory_translations->where('lang', $lang)->first();
        return $inventory_translations != null ? $inventory_translations->$field : $this->$field;
    }

    public function inventory_translations()
    {
        return $this->hasMany(InventoryTranslation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function product_stock()
    {
        return $this->belongsTo(ProductStock::class, 'product_stock_id', 'id');
    }

    public function product_owner()
    {
        return $this->belongsTo(User::class, 'product_owner_id', 'id');
    }

    public function warehouse_owner()
    {
        return $this->belongsTo(User::class, 'warehouse_owner_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Location;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';
    protected $guarded = [];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $warehouse_translations = $this->warehouse_translations->where('lang', $lang)->first();
        return $warehouse_translations != null ? $warehouse_translations->$field : $this->$field;
    }

    public function warehouse_translations()
    {
        return $this->hasMany(WarehouseTranslation::class);
    }

    public function vendor() {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'warehouse_id', 'id');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'warehouse_id', 'id');
    }

    public function shelves()
    {
        return $this->hasMany(Shelf::class, 'warehouse_id', 'id');
    }

    public function cells()
    {
        return $this->hasMany(Cell::class, 'warehouse_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    /**
     * Check if warehouse has any locations associated with it.
     *
     * @return bool
     */
    public function hasLocations(): bool
    {
        return $this->locations()->exists();
    }
}

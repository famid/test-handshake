<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    protected $guarded = [];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $area_translations = $this->area_translations->where('lang', $lang)->first();
        return $area_translations != null ? $area_translations->$field : $this->$field;
    }

    public function area_translations()
    {
        return $this->hasMany(AreaTranslation::class);
    }

    /**
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function shelves()
    {
        return $this->hasMany(Shelf::class, 'area_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    /**
     * Check if area has any shelves associated with it.
     *
     * @return bool
     */
    public function hasShelves(): bool
    {
        return $this->shelves()->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    protected $guarded = [];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $location_translations = $this->location_translations->where('lang', $lang)->first();
        return $location_translations != null ? $location_translations->$field : $this->$field;
    }

    public function location_translations()
    {
        return $this->hasMany(LocationTranslation::class);
    }

    /**
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'location_id', 'id');
    }

    /**
     * Check if location has any areas associated with it.
     *
     * @return bool
     */
    public function hasAreas(): bool
    {
        return $this->areas()->exists();
    }
}

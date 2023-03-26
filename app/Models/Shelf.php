<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shelf extends Model
{
    use HasFactory;

    protected $table = 'shelves';
    protected $guarded = [];

    public function getTranslation($field = '', $lang = FALSE)
    {
        $lang = $lang == FALSE ? App::getLocale() : $lang;
        $shelf_translations = $this->shelf_translations->where('lang', $lang)->first();
        return $shelf_translations != NULL ? $shelf_translations->$field : $this->$field;
    }

    public function shelf_translations()
    {
        return $this->hasMany(ShelfTranslation::class);
    }

    /**
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    public function cells()
    {
        return $this->hasMany(Cell::class, 'shelf_id', 'id');
    }

    /**
     * Check if shelf has any cells associated with it.
     *
     * @return bool
     */
    public function hasCells(): bool
    {
        return $this->cells()->exists();
    }
}

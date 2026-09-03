<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'serial_number',
        'asset_tag',
        'category_id',
        'brand_id',
        'status',
        'date_added',
        'removed_at',
    ];

    protected $casts = [
        'date_added'  => 'date',
        'removed_at'  => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}

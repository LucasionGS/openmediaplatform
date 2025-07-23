<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all active categories
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)
                   ->orderBy('sort_order')
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Get all categories for both images and videos (shared)
     */
    public static function getSharedCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return self::getActive();
    }

    /**
     * For backward compatibility - same as getSharedCategories
     */
    public static function getImageCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return self::getSharedCategories();
    }

    /**
     * For backward compatibility - same as getSharedCategories
     */
    public static function getVideoCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return self::getSharedCategories();
    }

    /**
     * Get images in this category
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'category', 'slug');
    }

    /**
     * Get videos in this category
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'category', 'slug');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    // 🔹 Mass assignable fields
    protected $fillable = [
        'asset_name',
        'asset_type',
        'serial_number',
        'purchase_date',
        'status',
        'condition',
        'notes'
    ];

    /**
     * 🔹 Get all assignments for this asset
     * Returns a hasMany relationship to AssetAssignment
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id');
    }

    /**
     * 🔹 Get latest assignment record (current active assignment)
     * Useful to check who currently holds the asset
     */
    public function latestAssignment()
    {
        return $this->hasOne(AssetAssignment::class, 'asset_id')
                    ->latest('assigned_date');
    }

    /**
     * 🔹 Helper method to get current holder user
     * Returns the user of the latest assignment or null if not assigned
     */
    public function getCurrentHolderAttribute()
    {
        return $this->latestAssignment?->user;
    }
}
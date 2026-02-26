<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'from_date',
        'to_date',
        'type',
        'reason',
        'status',
    ];

    /**
     * Date casting
     */
    protected $casts = [
        'from_date' => 'date',
        'to_date'   => 'date',
    ];

    /**
     * Default values
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Relationship: Leave belongs to a User (Employer)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helpers (optional but useful)
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}

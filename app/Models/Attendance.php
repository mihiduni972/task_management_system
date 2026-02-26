<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'marked_by',
        'date',
        'check_in',
        'check_out',
        'worked_minutes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /* =====================================================
     * RELATIONSHIPS
     * ===================================================== */

    // Employer / Employee
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Marker (attendance marker or admin)
    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /* =====================================================
     * ACCESSORS (OPTIONAL BUT VERY USEFUL)
     * ===================================================== */

    // worked_minutes → "8.50 hrs"
    public function getWorkedHoursAttribute()
    {
        if (!$this->worked_minutes) {
            return '-';
        }

        return number_format($this->worked_minutes / 60, 2) . ' hrs';
    }

    // check_in formatted
    public function getCheckInFormattedAttribute()
    {
        return $this->check_in
            ? Carbon::parse($this->check_in)->format('H:i')
            : '-';
    }

    // check_out formatted
    public function getCheckOutFormattedAttribute()
    {
        return $this->check_out
            ? Carbon::parse($this->check_out)->format('H:i')
            : '-';
    }
}

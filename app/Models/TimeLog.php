<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeLog extends Model
{
    /**
     * 🔹 Mass assignable fields (columns in the time_logs table)
     */
    protected $fillable = [
        'task_id',     // Associated task
        'user_id',     // Employee who logged the time
        'hours',       // Decimal hours worked
        'description', // Optional description of work
        'log_date'     // Date of the log
    ];

    /**
     * 🔹 Relationship: User (Employee) who created this time log
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🔹 Relationship: Task associated with this time log
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * 🔹 Helper: Access the Project associated via Task
     * Usage in Blade: $log->task->project
     * Returns null if task or project is missing
     */
    public function getProjectAttribute()
    {
        return $this->task && $this->task->project ? $this->task->project : null;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable; // 🔹 HasFactory + Notifiable

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'username',
        'full_name',
        'email',
        'phone',
        'birthdate',
        'gender',       // male / female
        'password',
        'user_type',    // admin / employer / attendance_marker
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // 🔹 User → Many Attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // 🔹 User → Many Tasks (Daily Tasks)
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // 🔹 User → Many Projects (Project Management)
    public function projects()
    {
        // Explicit pivot table + keys
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    }

    // 🔹 User → Assigned Tasks under projects
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * =========================
     * PROFILE DISPLAY NAME
     * =========================
     *
     * admin    → Admin
     * employer → Mr John / Miss Sarah
     */
    public function getProfileDisplayNameAttribute(): string
    {
        // 🔹 Admin → always "Admin"
        if ($this->user_type === 'admin') {
            return 'Admin';
        }

        // 🔹 Employer → Mr / Miss + First Name
        $firstName = explode(' ', trim($this->full_name))[0] ?? '';

        if ($this->gender === 'male') {
            return 'Mr ' . $firstName;
        }

        if ($this->gender === 'female') {
            return 'Miss ' . $firstName;
        }

        // 🔹 Fallback
        return $firstName ?: $this->full_name;
    }
}

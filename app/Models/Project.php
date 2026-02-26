<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // 🔹 Mass assignable fields
    protected $fillable = [
        'name',
        'client_id',    // Foreign key to associate with a client
        'client_name',  // Store client name for quick reference
        'description',
        'start_date',
        'end_date',
        'status',
        'created_by'    // ID of the user who created this project
    ];

    /**
     * 🔹 Relationship: A project belongs to a client.
     */
    public function client()
    {
        // Link via 'client_id' to Client model
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * 🔹 Relationship: A project can have many tasks.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * 🔹 Relationship: A project can be assigned to many employers (users).
     */
    public function employers()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }

    /**
     * 🔹 Relationship: A project can have many milestones.
     */
    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    /**
     * 🔹 Accessor: Automatically calculate overall project progress
     * Returns average progress of all milestones (0 if no milestones exist)
     */
    public function getProgressPercentageAttribute()
    {
        $totalMilestones = $this->milestones()->count();

        if ($totalMilestones === 0) {
            return 0;
        }

        $totalProgressSum = $this->milestones()->sum('progress');

        // Calculate average progress across all milestones
        return round($totalProgressSum / $totalMilestones);
    }
}
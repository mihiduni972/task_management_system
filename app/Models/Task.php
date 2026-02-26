<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Project;
use App\Models\TimeLog;

class Task extends Model
{
    use HasFactory;

    // 🔹 Mass assignable fields
    protected $fillable = [
        'user_id',      
        'project_id',   
        'task',         
        'sub_task',     
        'start_date',   
        'end_date',     
        'status',      
        'assigned_to',  
        'due_date',     
    ];

    /**
     * 🔹 Relationship: Owner / creator of the task
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🔹 Relationship: Project associated with this task
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * 🔹 Relationship: Time logs related to this task
     */
    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'task_id');
    }

    /**
     * 🔹 Relationship: User to whom this task is assigned
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
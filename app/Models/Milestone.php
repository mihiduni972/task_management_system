<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    
    protected $fillable = [
        'project_id',
        'title',
        'due_date',
        'status',
        'progress' 
    ];

    /**
     * Relationship: Every milestone belongs to a specific project.
     */
    public function project() {
        return $this->belongsTo(Project::class);
    }
}
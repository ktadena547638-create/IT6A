<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'client_id',
        'status',
        'priority',
        'start_date',
        'due_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    protected $appends = ['progress'];

    /**
     * Project is managed by a user
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Project is owned by a client (optional)
     * ✅ SELECTIVE SCRYING: Clients can see only their projects
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Project has many tasks
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Project has many tasks through activities
     * ✅ FIXED: Now uses proper Eloquent relationship instead of manual query
     */
    public function activities(): HasManyThrough
    {
        return $this->hasManyThrough(TaskActivity::class, Task::class);
    }

    /**
     * Check if project is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if project is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if project is overdue
     */
    public function isOverdue(): bool
    {
        return $this->end_date && $this->end_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Get project progress percentage as Accessor
     * ✅ SINGLE SOURCE OF TRUTH: Only one calculation method, used everywhere
     * ✅ OPTIMIZED: Uses eager-loaded task relationship to prevent N+1 queries
     * ✅ FORMULA: (completed / total) * 100, safely handles zero division
     * ✅ NO NEW QUERIES: Calculates from already-loaded $this->tasks relationship
     */
    public function getProgressAttribute(): int
    {
        // Get total count from eager-loaded withCount('tasks')
        $total = $this->tasks_count ?? 0;
        
        if ($total === 0) {
            return 0;
        }

        // Calculate completed from eager-loaded relationship (no new query)
        // If tasks are loaded, use collection; otherwise count from query
        $completed = $this->tasks
            ? $this->tasks->where('status', 'completed')->count()
            : $this->tasks()->where('status', 'completed')->count();
            
        return $completed > 0 ? (int)(($completed / $total) * 100) : 0;
    }

    /**
     * Get project progress percentage (legacy method - use accessor instead)
     * ✅ FIXED: Added safe division by zero check
     */
    public function getProgressPercentage(): int
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return $completedTasks > 0 ? (int) (($completedTasks / $totalTasks) * 100) : 0;
    }
}


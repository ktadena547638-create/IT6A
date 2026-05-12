<?php

namespace App\Models;

use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_user_id',
        'created_by',
        'due_date',
        'estimated_hours',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * ✅ EVENT DISPATCH: Fire events when tasks are created or updated
     * Triggers notification listeners automatically
     */
    protected $dispatchesEvents = [
        'created' => TaskCreated::class,
        'updated' => TaskUpdated::class,
    ];

    /**
     * Task belongs to a project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Task is assigned to a user
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Task was created by a user
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Task has many comments
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * Task has many activity logs
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TaskActivity::class);
    }

    /**
     * Task has many attachments
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Check if task is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if task is high priority
     */
    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    /**
     * Get tasks ordered by priority
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderByRaw("CASE LOWER(priority) WHEN 'critical' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END");
    }

    /**
     * Get tasks ordered by due date
     */
    public function scopeOrderByDueDate($query)
    {
        return $query->orderBy('due_date', 'asc');
    }

    /**
     * Filter tasks by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}

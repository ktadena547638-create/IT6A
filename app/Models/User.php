<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'theme_preference',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is project manager
     */
    public function isProjectManager(): bool
    {
        return $this->role === 'project_manager';
    }

    /**
     * Check if user is team member
     */
    public function isTeamMember(): bool
    {
        return $this->role === 'team_member';
    }

    /**
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    // ✅ RELATIONSHIPS FOR ROLE-BASED ACCESS

    /**
     * Projects managed by this user (for Project Managers)
     */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'manager_id', 'id');
    }

    /**
     * Projects owned by this client (for Clients)
     */
    public function clientProjects()
    {
        return $this->hasMany(Project::class, 'client_id', 'id');
    }

    /**
     * Tasks assigned to this user (for Team Members)
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_user_id', 'id');
    }
}

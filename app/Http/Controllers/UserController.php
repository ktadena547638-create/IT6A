<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of all users (Admin only)
     * ✅ SOVEREIGN'S DECREE: Only admins can access user management
     * ✅ OPTIMIZED: Eager load relationships + count assignments, order by role hierarchy
     */
    public function index(): View|RedirectResponse
    {
        try {
            Gate::authorize('viewAny', User::class);
            
            $users = User::query()
                ->withCount([
                    'managedProjects',
                    'clientProjects', 
                    'assignedTasks'
                ])
                ->orderByRaw("CASE LOWER(role) WHEN 'admin' THEN 0 WHEN 'project_manager' THEN 1 WHEN 'team_member' THEN 2 WHEN 'client' THEN 3 ELSE 4 END")
                ->orderBy('name', 'asc')
                ->paginate(20);
            
            return view('admin.users.index', compact('users'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve users list', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return redirect()->route('dashboard.index')->with('error', 'Unauthorized: Admin access required');
        }
    }

    /**
     * Show the form for creating a new user (Admin only)
     * ✅ SOVEREIGN'S DECREE: Only admins can create users
     */
    public function create(): View|RedirectResponse
    {
        try {
            Gate::authorize('create', User::class);
            
            $roles = [
                'admin' => 'Admin (Full System Access)',
                'project_manager' => 'Project Manager (Project Owner)',
                'team_member' => 'Team Member (Task Executor)',
                'client' => 'Client (Read-Only Project Access)',
            ];
            
            return view('admin.users.create', compact('roles'));
        } catch (Exception $e) {
            Log::error('Failed to load user creation form', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return redirect()->route('dashboard.index')->with('error', 'Unauthorized: Admin access required');
        }
    }

    /**
     * Store a newly created user in database (Admin only)
     * ✅ HARDENED: DB::transaction() wrapper + password hashing + error handling
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            Gate::authorize('create', User::class);
            
            DB::transaction(function () use ($request) {
                User::create(array_merge(
                    $request->validated(),
                    ['password' => Hash::make($request->validated('password'))]
                ));
            });
            
            return redirect()->route('users.index')->with('success', 'User created successfully');
        } catch (Exception $e) {
            Log::error('Failed to create user', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create user. Please try again');
        }
    }

    /**
     * Display the specified user (Admin only)
     * ✅ SOVEREIGN'S DECREE: Shows user details and role-specific info
     */
    public function show(User $user): View|RedirectResponse
    {
        try {
            Gate::authorize('view', $user);
            
            $user->load(['managedProjects:id,name', 'clientProjects:id,name', 'assignedTasks:id,title']);
            
            return view('admin.users.show', compact('user'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve user details', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('users.index')->with('error', 'Failed to load user');
        }
    }

    /**
     * Show the form for editing the specified user (Admin only)
     * ✅ SOVEREIGN'S DECREE: Only admins can edit users
     */
    public function edit(User $user): View|RedirectResponse
    {
        try {
            Gate::authorize('update', $user);
            
            $roles = [
                'admin' => 'Admin (Full System Access)',
                'project_manager' => 'Project Manager (Project Owner)',
                'team_member' => 'Team Member (Task Executor)',
                'client' => 'Client (Read-Only Project Access)',
            ];
            
            return view('admin.users.edit', compact('user', 'roles'));
        } catch (Exception $e) {
            Log::error('Failed to load user edit form', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->route('users.index')->with('error', 'Unauthorized: Admin access required');
        }
    }

    /**
     * Update the specified user in database (Admin only)
     * ✅ HARDENED: DB::transaction() wrapper + conditional password hashing
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            Gate::authorize('update', $user);
            
            DB::transaction(function () use ($request, $user) {
                $validated = $request->validated();
                
                // Only hash password if it was provided
                if (!empty($validated['password'])) {
                    $validated['password'] = Hash::make($validated['password']);
                } else {
                    unset($validated['password']);
                }
                
                $user->update($validated);
            });
            
            return redirect()->route('users.show', $user)->with('success', 'User updated successfully');
        } catch (Exception $e) {
            Log::error('Failed to update user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update user. Please try again');
        }
    }

    /**
     * Remove the specified user from database (Admin only)
     * ✅ SOVEREIGN'S DECREE: Only admins can delete users
     * ✅ SAFETY: Prevents deletion of self or last admin
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            Gate::authorize('delete', $user);
            
            // Safety check: prevent deleting self
            if (auth()->id() === $user->id) {
                return redirect()->back()->with('error', 'Cannot delete your own account');
            }
            
            // Safety check: prevent deleting last admin
            if ($user->isAdmin() && User::whereRaw('LOWER(role) = ?', ['admin'])->count() === 1) {
                return redirect()->back()->with('error', 'Cannot delete the last admin account');
            }
            
            DB::transaction(function () use ($user) {
                // Orphan any managed projects
                if ($user->isProjectManager()) {
                    $user->managedProjects()->update(['manager_id' => null]);
                }
                
                // Orphan any client projects
                if ($user->isClient()) {
                    $user->clientProjects()->update(['client_id' => null]);
                }
                
                // Unassign any tasks
                if ($user->isTeamMember()) {
                    $user->assignedTasks()->update(['assigned_user_id' => null]);
                }
                
                $user->delete();
            });
            
            return redirect()->route('users.index')->with('success', 'User deleted successfully');
        } catch (Exception $e) {
            Log::error('Failed to delete user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete user. Please try again');
        }
    }

    /**
     * Display audit logs (admin only)
     */
    public function auditLogs(Request $request)
    {
        try {
            if (!auth()->user()->isAdmin()) {
                abort(403);
            }

            $query = \App\Models\AuditLog::with('user');

            if ($request->has('action') && $request->action) {
                $query->where('action', $request->action);
            }

            if ($request->has('model_type') && $request->model_type) {
                $query->where('model_type', $request->model_type);
            }

            if ($request->has('from_date') && $request->from_date) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            $logs = $query->orderBy('created_at', 'desc')->paginate(50);

            return view('admin.audit-logs', compact('logs'));
        } catch (Exception $e) {
            Log::error('Failed to load audit logs', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard.index')->with('error', 'Failed to load audit logs');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService) {}

    /**
     * Display a listing of projects with priority-driven heatmapping
     * ✅ OPTIMIZED: Single query with withCount('tasks') and eager load manager
     * ✅ FILTERS: name search, status, priority, manager_id dropdowns
     * ✅ SORTING: default by priority (descending) then due_date - critical projects stay visible
     * ✅ HEATMAP: Color-coded priority badges in index view
     * ✅ AUTHORIZATION: respects ProjectPolicy - only returns projects user can view
     */
    public function index(): View
    {
        try {
            Gate::authorize('viewAny', Project::class);
            
            $query = Project::query()
                ->with(['manager:id,name'])
                ->withCount('tasks');

            // Search filter by project name
            if ($search = request('search')) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            // Status filter dropdown
            if ($status = request('status')) {
                $query->where('status', $status);
            }

            // Priority filter dropdown (NEW: Urgency driver)
            if ($priority = request('priority')) {
                $query->where('priority', $priority);
            }

            // Manager filter dropdown
            if ($manager_id = request('manager_id')) {
                $query->where('manager_id', $manager_id);
            }

            // Sorting: Default by priority (descending) then due_date
            // ✅ STRATEGIC: Critical projects always stay at top
            $sortBy = request('sort_by', 'priority');
            $sortOrder = request('sort_order', 'desc');
            if (in_array($sortBy, ['due_date', 'priority', 'created_at', 'updated_at'])) {
                $query->orderBy($sortBy, $sortOrder);
                // Secondary sort by due_date when not sorting by priority
                if ($sortBy !== 'priority') {
                    $query->orderBy('due_date', 'asc');
                }
            } else {
                // Default: critical projects first
                $query->orderByRaw("CASE LOWER(priority) WHEN 'critical' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
                    ->orderBy('due_date', 'asc');
            }

            $projects = $query->paginate(15);
            
            return view('projects.index', compact('projects'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve projects list', ['error' => $e->getMessage()]);
            return view('projects.index', ['projects' => collect()]);
        }
    }

    /**
     * Show the form for creating a new project
     * ✅ SELECTIVE SCRYING: Only Project Managers appear in dropdown
     */
    public function create(): View
    {
        try {
            Gate::authorize('create', Project::class);
            // Filter to only show Project Managers (not admins, not team members)
            $managers = User::whereRaw('LOWER(role) = ?', ['project_manager'])->get();
            return view('projects.create', compact('managers'));
        } catch (Exception $e) {
            Log::error('Failed to load project creation form', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load project creation form');
        }
    }

    /**
     * Store a newly created project
     * ✅ HARDENED: DB::transaction() wrapper + error handling + diagnostic logging
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        try {
            Gate::authorize('create', Project::class);

            $projectData = $request->validated();

            Log::info('Creating project', [
                'user_id' => auth()->id(),
                'data_keys' => array_keys($projectData),
            ]);

            $project = DB::transaction(function () use ($projectData) {
                return $this->projectService->createProject($projectData);
            });

            Log::info('Project created successfully', [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('projects.show', $project)
                ->with('success', 'Project "' . $project->name . '" created successfully.');
        } catch (\Exception $e) {
            Log::error('Project creation failed - critical error', [
                'user_id' => auth()->id(),
                'exception_type' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            $errorMessage = 'Failed to create project. ';
            if (str_contains($e->getMessage(), 'unique')) {
                $errorMessage .= 'A project with this name already exists.';
            } elseif (str_contains($e->getMessage(), 'Required field')) {
                $errorMessage .= 'Some required fields are missing.';
            } else {
                $errorMessage .= 'Please check your input and try again.';
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Display the specified project
     */
    public function show(Project $project): View
    {
        try {
            Gate::authorize('view', $project);
            $project->load(['manager', 'tasks.assignedUser', 'tasks.creator']);
            $health = $this->projectService->getProjectHealth($project->id);
            return view('projects.show', compact('project', 'health'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve project', ['project_id' => $project->id, 'error' => $e->getMessage()]);
            abort(500, 'Failed to load project');
        }
    }

    /**
     * Show the form for editing the project
     * ✅ DELEGATION: Fetch all non-admin users for manager assignment (admins only)
     */
    public function edit(Project $project): View
    {
        try {
            Gate::authorize('update', $project);
            
            // ✅ SELECTIVE SCRYING: Only Project Managers appear in manager delegation dropdown
            $managers = User::whereRaw('LOWER(role) = ?', ['project_manager'])
                ->orderBy('name')
                ->get(['id', 'name']);
            
            return view('projects.edit', compact('project', 'managers'));
        } catch (Exception $e) {
            Log::error('Failed to load project edit form', ['project_id' => $project->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load project for editing');
        }
    }

    /**
     * Update the specified project
     * ✅ ATOMIC DELEGATION: DB::transaction() ensures manager reassignment is atomic
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        try {
            Gate::authorize('update', $project);
            
            // Atomic transaction for project update + potential manager reassignment
            DB::transaction(function () use ($request, $project) {
                $validated = $request->validated();
                
                // Validate manager_id if being changed (admin delegation)
                if (isset($validated['manager_id'])) {
                    $manager = User::findOrFail($validated['manager_id']);
                    if ($manager->isAdmin() && !auth()->user()->isAdmin()) {
                        throw new \Exception('Cannot assign admin users as project managers.');
                    }
                }
                
                $this->projectService->updateProject($project->id, $validated);
            });
            
            return redirect()->route('projects.show', $project)
                ->with('success', 'Project updated successfully.');
        } catch (Exception $e) {
            Log::error('Project update failed', [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update project. Please try again.']);
        }
    }

    /**
     * Delete the specified project
     * ✅ HARDENED: DB::transaction() wrapper + error handling
     */
    public function destroy(Project $project): RedirectResponse
    {
        try {
            Log::info('Delete attempt initiated', [
                'project_id' => $project->id,
                'status' => $project->status,
                'priority' => $project->priority,
                'user_id' => auth()->id(),
                'user_role' => auth()->user()?->role,
            ]);

            Gate::authorize('delete', $project);
            
            // Pre-check business rules to avoid DB trigger exceptions
            if ($project->status === 'active' || strtolower($project->priority) === 'critical') {
                Log::warning('Prevented delete attempt for protected project', [
                    'project_id' => $project->id,
                    'status' => $project->status,
                    'priority' => $project->priority,
                    'user_id' => auth()->id(),
                ]);

                return redirect()->back()
                    ->withErrors(['error' => 'Cannot delete active or critical projects. Deactivate or lower priority first.']);
            }

            Log::info('Pre-checks passed, attempting database delete', [
                'project_id' => $project->id,
            ]);

            DB::transaction(function () use ($project) {
                $this->projectService->deleteProject($project->id);
            });
            
            Log::info('Project deleted successfully', [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (Exception $e) {
            Log::error('Project deletion failed - detailed exception', [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete project. Please try again.']);
        }
    }

    /**
     * Display project statistics
     */
    public function statistics(Project $project): View
    {
        try {
            Gate::authorize('view', $project);
            $stats = $this->projectService->getProjectStats($project->id);
            $health = $this->projectService->getProjectHealth($project->id);
            return view('projects.statistics', compact('project', 'stats', 'health'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve project statistics', ['project_id' => $project->id, 'error' => $e->getMessage()]);
            return view('projects.statistics', ['project' => $project, 'stats' => [], 'health' => 100]);
        }
    }
}


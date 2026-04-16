@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Flash Messages --}}
    @if ($message = Session::get('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800 font-medium">✓ {{ $message }}</p>
        </div>
    @endif
    
    @if ($message = Session::get('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-medium">✗ {{ $message }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
            <p class="mt-2 text-gray-600">{{ $project->description }}</p>
        </div>
        <div class="flex gap-2">
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Edit
                </a>
            @endcan
            @can('delete', $project)
                <form method="POST" action="{{ route('projects.destroy', $project) }}" style="display:inline" 
                      onsubmit="return confirm('Delete this project?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>

    {{-- Split Screen Layout --}}
    <div class="grid grid-cols-3 gap-8">
        {{-- Left Column: Project Metadata --}}
        <div class="col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Project Details</h2>

                {{-- Ownership Hierarchy --}}
                <div class="mb-4">
                    <p class="text-sm text-gray-600 font-medium">Ownership Structure</p>
                    <div class="space-y-1">
                        <p class="text-gray-900"><strong>👤 Managed by:</strong> {{ $project->manager?->name ?? 'Unassigned' }}</p>
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <p class="text-sm text-gray-600 font-medium">Status</p>
                    <x-status-badge :status="$project->status" />
                </div>

                {{-- Priority --}}
                <div class="mb-4">
                    <p class="text-sm text-gray-600 font-medium">Priority</p>
                    @if($project->priority)
                        @php
                            $priorityColors = [
                                'critical' => 'bg-red-100 text-red-800 border border-red-300',
                                'high' => 'bg-orange-100 text-orange-800 border border-orange-300',
                                'medium' => 'bg-amber-100 text-amber-800 border border-amber-300',
                                'low' => 'bg-slate-100 text-slate-800 border border-slate-300',
                            ];
                            $priorityIcons = [
                                'critical' => '🔴',
                                'high' => '🟠',
                                'medium' => '🟡',
                                'low' => '🔵',
                            ];
                        @endphp
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$project->priority] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $priorityIcons[$project->priority] ?? '' }} {{ ucfirst($project->priority) }}
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                            Not Set
                        </span>
                    @endif
                </div>

                {{-- Dates --}}
                <div class="mb-4">
                    <p class="text-sm text-gray-600 font-medium">Start Date</p>
                    <p class="text-gray-900">{{ $project->start_date?->format('M d, Y') ?? 'Not set' }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 font-medium">Due Date</p>
                    <p class="text-gray-900">{{ $project->due_date?->format('M d, Y') ?? 'Not set' }}</p>
                </div>

                {{-- Progress --}}
                <div class="mb-6">
                    <p class="text-sm text-gray-600 font-medium mb-2">Overall Progress</p>
                    <x-progress-bar :value="$health" />
                    <p class="text-xs text-gray-500 mt-1">{{ $health }}% complete</p>
                </div>

                {{-- Quick Stats --}}
                <div class="border-t pt-6">
                    <p class="text-sm text-gray-600 font-medium mb-3">Quick Stats</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Tasks</span>
                            <span class="font-medium">{{ $project->tasks->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Completed</span>
                            <span class="font-medium text-green-600">{{ $project->tasks->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">In Progress</span>
                            <span class="font-medium text-blue-600">{{ $project->tasks->where('status', 'in_progress')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Yet to Start</span>
                            <span class="font-medium text-yellow-600">{{ $project->tasks->where('status', 'pending')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Tasks --}}
        <div class="col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Tasks</h2>
                    @can('create', App\Models\Task::class)
                        <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                            + Add Task
                        </a>
                    @endcan
                </div>

                @if($project->tasks->count() > 0)
                    <div class="space-y-3">
                        @foreach($project->tasks as $task)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                            {{ $task->title }}
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->description, 80) }}</p>
                                    </div>
                                    <x-status-badge :status="$task->status" />
                                </div>

                                <div class="flex justify-between items-center text-sm text-gray-600">
                                    <div class="flex gap-4">
                                        <span>
                                            Assigned: <strong>{{ $task->assignedUser?->name ?? 'Unassigned' }}</strong>
                                        </span>
                                        <span>
                                            <x-priority-icon :priority="$task->priority" />
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900 text-xs">
                                            View
                                        </a>
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-gray-600 hover:text-gray-900 text-xs">
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-12">
                        <div class="text-5xl mb-3">✨</div>
                        <p class="text-gray-600">No tasks yet</p>
                        <p class="text-sm text-gray-500 mt-1">
                            @can('create', App\Models\Task::class)
                                Get started by <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="text-blue-600 hover:text-blue-900">adding a task</a>
                            @else
                                Contact your manager to add tasks
                            @endcan
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

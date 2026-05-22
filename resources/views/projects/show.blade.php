@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <style>
        .project-view-card {
            background: #0d0d12;
            border: 1px solid #1f2430;
            transition: all 180ms ease;
        }
        .project-view-card:hover {
            border-color: rgba(14, 165, 233, 0.45);
            background: #0f1118;
        }
        .project-view-soft {
            background: rgba(14, 165, 233, 0.08);
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
    </style>

    {{-- Flash Messages --}}
    @if ($message = Session::get('success'))
        <div class="mb-6 p-4 rounded-lg border border-green-500/30 bg-green-500/10">
            <p class="text-green-300 font-medium">✓ {{ $message }}</p>
        </div>
    @endif
    
    @if ($message = Session::get('error'))
        <div class="mb-6 p-4 rounded-lg border border-red-500/30 bg-red-500/10">
            <p class="text-red-300 font-medium">✗ {{ $message }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-4xl font-bold text-white">{{ $project->name }}</h1>
            <p class="mt-2 text-gray-300">{{ $project->description }}</p>
        </div>
        <div class="flex gap-2">
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="px-5 py-2.5 rounded-lg border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 transition font-medium">
                    Edit
                </a>
            @endcan
            @can('delete', $project)
                <form method="POST" action="{{ route('projects.destroy', $project) }}" style="display:inline" 
                      onsubmit="return confirm('Delete this project?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 rounded-lg border border-red-500/40 bg-red-500/10 text-red-300 hover:bg-red-500/20 transition font-medium">
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
            <div class="project-view-card rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-white mb-5">Project Details</h2>

                {{-- Ownership Hierarchy --}}
                <div class="mb-4">
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider">Ownership Structure</p>
                    <div class="space-y-1">
                        <p class="text-gray-100"><strong>👤 Managed by:</strong> {{ $project->manager?->name ?? 'Unassigned' }}</p>
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Status</p>
                    <x-status-badge :status="$project->status" />
                </div>

                {{-- Priority --}}
                <div class="mb-4">
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Priority</p>
                    @if($project->priority)
                        @php
                            $priorityColors = [
                                'critical' => 'bg-red-500/10 text-red-300 border border-red-500/40',
                                'high' => 'bg-orange-500/10 text-orange-300 border border-orange-500/40',
                                'medium' => 'bg-amber-500/10 text-amber-300 border border-amber-500/40',
                                'low' => 'bg-slate-500/10 text-slate-300 border border-slate-500/40',
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
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-500/10 text-gray-300 border border-gray-500/40">
                            Not Set
                        </span>
                    @endif
                </div>

                {{-- Dates --}}
                <div class="mb-4">
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider">Start Date</p>
                    <p class="text-gray-100">{{ $project->start_date?->format('M d, Y') ?? 'Not set' }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider">Due Date</p>
                    <p class="text-gray-100">{{ $project->due_date?->format('M d, Y') ?? 'Not set' }}</p>
                </div>

                {{-- Progress --}}
                <div class="mb-6">
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Overall Progress</p>
                    <x-progress-bar :value="$health" />
                    <p class="text-xs text-gray-400 mt-1">{{ $health }}% complete</p>
                </div>

                {{-- Quick Stats --}}
                <div class="border-t border-[#222936] pt-6">
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-3">Quick Stats</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Tasks</span>
                            <span class="font-medium text-white">{{ $project->tasks->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Completed</span>
                            <span class="font-medium text-green-600">{{ $project->tasks->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">In Progress</span>
                            <span class="font-medium text-blue-400">{{ $project->tasks->where('status', 'in_progress')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Yet to Start</span>
                            <span class="font-medium text-yellow-400">{{ $project->tasks->where('status', 'pending')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Tasks --}}
        <div class="col-span-2">
            <div class="project-view-card rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-white">Tasks</h2>
                    @can('create', App\Models\Task::class)
                        <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="text-cyan-300 hover:text-cyan-200 text-sm font-medium">
                            + Add Task
                        </a>
                    @endcan
                </div>

                @if($project->tasks->count() > 0)
                    <div class="space-y-3">
                        @foreach($project->tasks as $task)
                            <div class="project-view-soft rounded-lg p-4 hover:bg-cyan-500/10 transition border border-cyan-500/20">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-cyan-300 hover:text-cyan-200 font-semibold text-xl">
                                            {{ $task->title }}
                                        </a>
                                        <p class="text-sm text-gray-300 mt-1">{{ Str::limit($task->description, 80) }}</p>
                                    </div>
                                    <x-status-badge :status="$task->status" />
                                </div>

                                <div class="flex justify-between items-center text-sm text-gray-300">
                                    <div class="flex gap-4">
                                        <span>
                                            Assigned: <strong class="text-white">{{ $task->assignedUser?->name ?? 'Unassigned' }}</strong>
                                        </span>
                                        <span>
                                            <x-priority-icon :priority="$task->priority" />
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-cyan-300 hover:text-cyan-200 text-xs font-semibold">
                                            View
                                        </a>
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-gray-300 hover:text-white text-xs">
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
                        <p class="text-gray-300">No tasks yet</p>
                        <p class="text-sm text-gray-400 mt-1">
                            @can('create', App\Models\Task::class)
                                Get started by <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="text-cyan-300 hover:text-cyan-200">adding a task</a>
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


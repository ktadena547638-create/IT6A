@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
            <p class="mt-1 text-gray-500">Manage and monitor all your projects</p>
        </div>
        @can('create', App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                + New Project
            </a>
        @endcan
    </div>

    {{-- Filters & Priority Heatmap Controls --}}
    <div class="mb-6 flex gap-3 flex-wrap">
        <form method="GET" class="flex gap-3 flex-wrap">
            <select name="priority" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Priorities</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🟠 High</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>🔵 Low</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Statuses</option>
                <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <input type="text" name="search" placeholder="Search projects..." value="{{ request('search') }}" 
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Search
            </button>
        </form>
    </div>

    {{-- Projects Table --}}
    @if($projects->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($projects as $project)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $project->manager?->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$project->status" />
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $priorityColors = [
                                        'critical' => 'bg-red-100 text-red-800 border border-red-300 animate-pulse',
                                        'high' => 'bg-orange-100 text-orange-800 border border-orange-300',
                                        'medium' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
                                        'low' => 'bg-slate-100 text-slate-800 border border-slate-300',
                                    ];
                                    $priorityIcons = [
                                        'critical' => '🔴',
                                        'high' => '🟠',
                                        'medium' => '🟡',
                                        'low' => '🔵',
                                    ];
                                @endphp
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$project->priority] ?? 'bg-gray-100' }}">
                                    {{ $priorityIcons[$project->priority] ?? '' }} {{ ucfirst($project->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($project->tasks_count > 0)
                                        <div class="flex-1">
                                            <x-progress-bar :value="$project->progress ?? 0" class="w-32" />
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $project->progress ?? 0 }}%
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            —
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($project->due_date)
                                    <span class="{{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-red-600 font-medium' : '' }}">
                                        {{ $project->due_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No date</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $project->tasks_count ?? 0 }} tasks
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View Details →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="text-6xl mb-4">📋</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No projects yet</h3>
            <p class="text-gray-500 mb-6">
                @can('create', App\Models\Project::class)
                    Get started by creating your first project.
                @else
                    No projects available. Contact your manager or admin.
                @endcan
            </p>
            @can('create', App\Models\Project::class)
                <a href="{{ route('projects.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Create Project
                </a>
            @endcan
        </div>
    @endif
</div>
@endsection

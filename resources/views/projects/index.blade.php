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

    {{-- Projects Table with Modern Design --}}
    @if($projects->count() > 0)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 border-b-2 border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Project Name</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Manager</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Tasks</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($projects as $project)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200 group">
                                <td class="px-6 py-4">
                                    <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-bold text-sm transition-colors">
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                                    {{ $project->manager?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$project->status" />
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $priorityColors = [
                                            'critical' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-300 dark:border-red-700',
                                            'high' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 border border-orange-300 dark:border-orange-700',
                                            'medium' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-700',
                                            'low' => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600',
                                        ];
                                        $priorityIcons = [
                                            'critical' => '🔴',
                                            'high' => '🟠',
                                            'medium' => '🟡',
                                            'low' => '🔵',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-bold rounded-lg {{ $priorityColors[$project->priority] ?? 'bg-slate-100' }} transition-all transform group-hover:scale-105">
                                        <span>{{ $priorityIcons[$project->priority] ?? '' }}</span>
                                        <span>{{ ucfirst($project->priority) }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($project->tasks_count > 0)
                                        <div class="w-32">
                                            <x-progress-bar :value="$project->progress ?? 0" />
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-medium">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    @if($project->due_date)
                                        <span class="px-2 py-1 rounded-lg {{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 font-bold' : 'text-slate-600 dark:text-slate-400' }}">
                                            {{ $project->due_date->format('M d') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-400">
                                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 rounded-full text-xs font-bold">{{ $project->tasks_count ?? 0 }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white text-sm font-bold rounded-lg transition-all transform hover:scale-105 shadow-md hover:shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination with Modern Styling --}}
        <div class="mt-8 flex justify-center">
            {{ $projects->links('pagination::tailwind') }}
        </div>
    @else
        {{-- Enhanced Empty State --}}
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900/30 dark:to-slate-800/30 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 p-16 text-center shadow-sm">
            <div class="text-7xl mb-4 opacity-50">📋</div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">No Projects Found</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-8 text-lg">
                @can('create', App\Models\Project::class)
                    Get started by creating your first project.
                @else
                    No projects available. Contact your manager or admin.
                @endcan
            </p>
            @can('create', App\Models\Project::class)
                <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Your First Project
                </a>
            @endcan
        </div>
    @endif
</div>
@endsection

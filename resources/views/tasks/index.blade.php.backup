@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Enhanced Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 dark:from-white dark:to-slate-200 bg-clip-text text-transparent">Tasks Management</h1>
            <p class="mt-2 text-slate-600 dark:text-slate-400 font-medium">Organize and track all your work in one place</p>
        </div>
        @can('create', App\Models\Task::class)
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Task
            </a>
        @endcan
    </div>

    {{-- Enhanced Filters & Search --}}
    <div class="mb-8 p-5 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <form method="GET" class="flex gap-3 flex-wrap items-center">
            <select name="priority" onchange="this.form.submit()" class="px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <option value="">📊 All Priorities</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🟠 High</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>🔵 Low</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <option value="">📋 All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>⚙️ In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>✅ Completed</option>
            </select>
            <input type="text" name="search" placeholder="🔍 Search tasks..." value="{{ request('search') }}" 
                   class="px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition flex-1 min-w-48">
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-slate-600 to-slate-700 hover:from-slate-700 hover:to-slate-800 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-md">
                Search
            </button>
            @if(request('search') || request('status') || request('priority'))
                <a href="{{ route('tasks.index') }}" class="px-4 py-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium transition">
                    ✕ Clear Filters
                </a>
            @endif
        </form>
    </div>

    {{-- Tasks Table with Modern Design --}}
    @if($tasks->count() > 0)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 border-b-2 border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Task Title</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Assignee</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($tasks as $task)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200 group">
                                <td class="px-6 py-4">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-bold text-sm transition-colors">
                                        {{ Str::limit($task->title, 50) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    @isset($task->project)
                                        <a href="{{ route('projects.show', $task->project) }}" class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                                            {{ $task->project->name ?? 'Untitled' }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">—</span>
                                    @endisset
                                </td>
                                <td class="px-6 py-4">
                                    @can('update', $task)
                                        <x-inline-status-switcher :task="$task" />
                                    @else
                                        <x-status-badge :status="$task->status" />
                                    @endcan
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <x-priority-icon :priority="$task->priority" />
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-full text-xs">
                                        {{ $task->assignedUser?->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    @if($task->due_date)
                                        <span class="px-2 py-1 rounded-lg {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 font-bold' : 'text-slate-600 dark:text-slate-400' }}">
                                            {{ $task->due_date->format('M d') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white text-sm font-bold rounded-lg transition-all transform hover:scale-105 shadow-md hover:shadow-lg">
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

        {{-- Enhanced Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $tasks->links('pagination::tailwind') }}
        </div>
    @else
        {{-- Enhanced Empty State --}}
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900/30 dark:to-slate-800/30 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 p-16 text-center shadow-sm">
            <div class="text-7xl mb-4 opacity-50">📝</div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">No Tasks Found</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-8 text-lg">
                @if(request('search') || request('status') || request('priority'))
                    No tasks match your search criteria. Try adjusting your filters.
                @else
                    @can('create', App\Models\Task::class)
                        Get started by creating your first task.
                    @else
                        No tasks assigned to you yet.
                    @endcan
                @endif
            </p>
            @if(request('search') || request('status') || request('priority'))
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-slate-600 to-slate-700 hover:from-slate-700 hover:to-slate-800 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                    ↺ Clear All Filters
                </a>
            @elseif(Auth::user()->can('create', App\Models\Task::class))
                <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Your First Task
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

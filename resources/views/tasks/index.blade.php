@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header with New Task Button --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tasks</h1>
            <p class="mt-1 text-gray-500">Manage all your tasks in one place</p>
        </div>
        @can('create', App\Models\Task::class)
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Task
            </a>
        @endcan
    </div>

    {{-- Filters & Search --}}
    <div class="mb-6 flex gap-3 flex-wrap">
        <form method="GET" class="flex gap-3 flex-wrap">
            <select name="priority" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Priorities</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <input type="text" name="search" placeholder="Search tasks..." value="{{ request('search') }}" 
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Search
            </button>
        </form>
    </div>

    {{-- Tasks Table --}}
    @if($tasks->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($tasks as $task)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ Str::limit($task->title, 50) }}
                                </a>
                            </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @isset($task->project)
                            <a href="{{ route('projects.show', $task->project) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $task->project->name ?? $task->project['name'] ?? 'Untitled' }}
                            </a>
                        @else
                            <span class="text-gray-500">No project</span>
                        @endisset
                    </td>
                            <td class="px-6 py-4">
                                {{-- GENIUS FEATURE: Inline Status Switcher --}}
                                @can('update', $task)
                                    <x-inline-status-switcher :task="$task" />
                                @else
                                    <x-status-badge :status="$task->status" />
                                @endcan
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <x-priority-icon :priority="$task->priority" />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $task->assignedUser?->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($task->due_date)
                                    <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600 font-medium' : '' }}">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No date</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900 font-medium">
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
            {{ $tasks->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="text-6xl mb-4">📝</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks found</h3>
            <p class="text-gray-500">
                @if($search || $status || $priority)
                    No tasks found matching your criteria.
                @else
                    @can('create', App\Models\Task::class)
                        Get started by creating your first task.
                    @else
                        No tasks assigned to you yet.
                    @endcan
                @endif
            </p>
        </div>
    @endif
</div>
@endsection

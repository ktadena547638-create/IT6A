@extends('layouts.app')

@section('title', 'Tasks - TaskFlow')
@section('page-title', '✓ Tasks')

@section('content')
    <!-- HEADER WITH ACTION -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl accent-cyan font-bold">Task Inventory</h2>
            <p class="text-xs text-gray-500 mt-1">{{ $tasks->total() }} total tasks</p>
        </div>
        @can('create', App\Models\Task::class)
            <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-[#0ea5e9] hover:bg-[#0284c7] text-[#050509] font-semibold text-sm rounded border border-[#0ea5e9] transition">
                + New Task
            </a>
        @endcan
    </div>

    <!-- OBSIDIAN FILTER CONTROLS -->
    <div class="grid-panel p-4 mb-6">
        <form method="GET" class="flex gap-3 flex-wrap items-center">
            <select name="priority" onchange="this.form.submit()" class="px-3 py-2 bg-[#1e1e28] border border-gray-700 text-gray-300 text-sm rounded hover:border-gray-600 transition focus:outline-none focus:border-[#0ea5e9]" style="background-color: var(--carbon);">
                <option value="" style="background-color: var(--carbon); color: var(--white);">All Priorities</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">🔴 Critical</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">🟠 High</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">🟡 Medium</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">🔵 Low</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-[#1e1e28] border border-gray-700 text-gray-300 text-sm rounded hover:border-gray-600 transition focus:outline-none focus:border-[#0ea5e9]" style="background-color: var(--carbon);">
                <option value="" style="background-color: var(--carbon); color: var(--white);">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">Pending</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">Completed</option>
            </select>

            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" 
                   class="px-3 py-2 bg-[#1e1e28] border border-gray-700 text-gray-300 placeholder-gray-600 text-sm rounded hover:border-gray-600 transition focus:outline-none focus:border-[#0ea5e9]" style="background-color: var(--carbon);">

            <button type="submit" class="px-4 py-2 bg-[#0ea5e9] hover:bg-[#0284c7] text-[#050509] font-semibold text-sm rounded transition">
                Filter
            </button>

            @if(request('search') || request('status') || request('priority'))
                <a href="{{ route('tasks.index') }}" class="px-3 py-2 text-gray-400 hover:text-gray-300 text-sm transition">
                    ✕ Clear
                </a>
            @endif
        </form>
    </div>

    <!-- SYSTEM LOG TABLE: HIGH-DENSITY FORENSIC DESIGN -->
    @if($tasks->count() > 0)
        <div class="grid-panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <!-- Header: Uppercase, minimal styling -->
                    <thead class="border-b border-gray-700/50">
                        <tr class="bg-[#1e1e28]">
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Task</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Project</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Status</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Priority</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Assignee</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Due Date</th>
                            <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-gray-500 font-semibold">Action</th>
                        </tr>
                    </thead>

                    <!-- Body: Minimal rows with micro-borders -->
                    <tbody class="divide-y divide-gray-700/30">
                        @foreach($tasks as $task)
                            <tr class="hover:bg-[#1e1e28] transition">
                                <!-- Task Title -->
                                <td class="px-4 py-3 text-white font-medium truncate">
                                    <a href="{{ route('tasks.show', $task) }}" class="accent-cyan hover:underline">
                                        {{ Str::limit($task->title, 40) }}
                                    </a>
                                </td>

                                <!-- Project -->
                                <td class="px-4 py-3 text-gray-400 text-sm">
                                    @isset($task->project)
                                        <a href="{{ route('projects.show', $task->project) }}" class="hover:accent-cyan transition">
                                            {{ $task->project->name ?? 'Untitled' }}
                                        </a>
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endisset
                                </td>

                                <!-- Status Badge -->
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-1 rounded border {{ 
                                        $task->status === 'completed' ? 'border-green-600/50 text-green-400' : 
                                        ($task->status === 'in_progress' ? 'border-[#0ea5e9] text-[#0ea5e9]' : 'border-gray-600/50 text-gray-400')
                                    }}">{{ strtoupper(str_replace('_', ' ', $task->status)) }}</span>
                                </td>

                                <!-- Priority Badge -->
                                <td class="px-4 py-3">
                                    @php
                                        $priorityColors = [
                                            'critical' => 'text-red-400',
                                            'high' => 'text-orange-400',
                                            'medium' => 'text-amber-400',
                                            'low' => 'text-blue-400',
                                        ];
                                        $priorityIcons = [
                                            'critical' => '🔴',
                                            'high' => '🟠',
                                            'medium' => '🟡',
                                            'low' => '🔵',
                                        ];
                                    @endphp
                                    <span class="text-xs font-mono {{ $priorityColors[$task->priority] ?? 'text-gray-400' }}">
                                        {{ $priorityIcons[$task->priority] ?? '◯' }} {{ ucfirst($task->priority) }}
                                    </span>
                                </td>

                                <!-- Assignee -->
                                <td class="px-4 py-3 text-gray-400 text-sm">
                                    {{ $task->assignedUser?->name ?? '—' }}
                                </td>

                                <!-- Due Date -->
                                <td class="px-4 py-3 text-sm font-mono {{ ($task->due_date && $task->due_date->isPast() && $task->status !== 'completed') ? 'text-red-400' : 'text-gray-400' }}">
                                    @if($task->due_date)
                                        {{ $task->due_date->format('M d, Y') }}
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endif
                                </td>

                                <!-- Action Button -->
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-xs accent-cyan font-mono hover:text-white transition">
                                        view →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAGINATION -->
        @if($tasks->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $tasks->links('pagination::tailwind') }}
            </div>
        @endif
    @else
        <!-- EMPTY STATE -->
        <div class="grid-panel p-12 text-center">
            <p class="text-gray-500 mb-4">
                @if(request('search') || request('status') || request('priority'))
                    No tasks match your criteria
                @else
                    No tasks available
                @endif
            </p>
            @if(request('search') || request('status') || request('priority'))
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#0ea5e9] hover:bg-[#0284c7] text-[#050509] font-semibold text-sm rounded transition">
                    ↺ Clear Filters
                </a>
            @elseif(Auth::user()->can('create', App\Models\Task::class))
                <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#0ea5e9] hover:bg-[#0284c7] text-[#050509] font-semibold text-sm rounded transition">
                    + Create First Task
                </a>
            @endif
        </div>
    @endif
@endsection

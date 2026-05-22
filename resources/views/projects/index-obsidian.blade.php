@extends('layouts.app')

@section('title', 'Projects - TaskFlow')
@section('page-title', '📦 Projects')

@section('content')
    <!-- HEADER WITH ACTION -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl accent-cyan font-bold">Project Inventory</h2>
            <p class="text-xs text-gray-500 mt-1">{{ $projects->total() }} total projects</p>
        </div>
        @can('create', App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-[#0ea5e9] hover:bg-[#0284c7] text-[#050509] font-semibold text-sm rounded border border-[#0ea5e9] transition">
                + New Project
            </a>
        @endcan
    </div>

    <!-- OBSIDIAN FILTER CONTROLS -->
    <div class="grid-panel p-4 mb-6">
        <form method="GET" class="flex gap-3 flex-wrap">
            <!-- Priority Filter -->
            <select name="priority" onchange="this.form.submit()" class="px-3 py-2 bg-[#1e1e28] border border-gray-700 text-gray-300 text-sm rounded hover:border-gray-600 transition focus:outline-none focus:border-[#0ea5e9]">
                <option value="" style="background-color: var(--carbon); color: var(--white);">All Priorities</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">🔴 Critical</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">🟠 High</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">🟡 Medium</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">🔵 Low</option>
            </select>
            
            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-[#1e1e28] border border-gray-700 text-gray-300 text-sm rounded hover:border-gray-600 transition focus:outline-none focus:border-[#0ea5e9]">
                <option value="" style="background-color: var(--carbon); color: var(--white);">All Statuses</option>
                <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">Planning</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">Active</option>
                <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">On Hold</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }} style="background-color: var(--carbon); color: var(--white);">Completed</option>
            </select>
            
            <!-- Search Input -->
            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" 
                   class="px-3 py-2 bg-[#1e1e28] border border-gray-700 text-gray-300 placeholder-gray-600 text-sm rounded hover:border-gray-600 transition focus:outline-none focus:border-[#0ea5e9]">
            
            <!-- Submit Button -->
            <button type="submit" class="px-4 py-2 bg-[#0ea5e9] hover:bg-[#0284c7] text-[#050509] font-semibold text-sm rounded transition">
                Filter
            </button>
        </form>
    </div>

    <!-- SYSTEM LOG TABLE: HIGH-DENSITY FORENSIC DESIGN -->
    @if($projects->count() > 0)
        <div class="grid-panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <!-- Header: Uppercase, minimal styling -->
                    <thead class="border-b border-gray-700/50">
                        <tr class="bg-[#1e1e28]">
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Project</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Manager</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Status</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Priority</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Progress</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Due Date</th>
                            <th class="px-4 py-3 text-center text-xs uppercase tracking-wider text-gray-500 font-semibold">Tasks</th>
                            <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-gray-500 font-semibold">Action</th>
                        </tr>
                    </thead>
                    
                    <!-- Body: Minimal rows with micro-borders -->
                    <tbody class="divide-y divide-gray-700/30">
                        @foreach($projects as $project)
                            <tr class="hover:bg-[#1e1e28] transition">
                                <!-- Project Name -->
                                <td class="px-4 py-3 text-white font-medium">
                                    <a href="{{ route('projects.show', $project) }}" class="accent-cyan hover:underline">
                                        {{ $project->name }}
                                    </a>
                                </td>
                                
                                <!-- Manager -->
                                <td class="px-4 py-3 text-gray-400 text-sm">
                                    {{ $project->manager?->name ?? '—' }}
                                </td>
                                
                                <!-- Status Badge -->
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-1 rounded border {{ 
                                        $project->status === 'active' ? 'border-[#0ea5e9] text-[#0ea5e9]' : 
                                        ($project->status === 'completed' ? 'border-green-600/50 text-green-400' : 
                                        ($project->status === 'planning' ? 'border-amber-600/50 text-amber-400' : 'border-gray-600/50 text-gray-400'))
                                    }}">{{ strtoupper($project->status) }}</span>
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
                                    <span class="text-xs font-mono {{ $priorityColors[$project->priority] ?? 'text-gray-400' }}">
                                        {{ $priorityIcons[$project->priority] ?? '◯' }} {{ ucfirst($project->priority) }}
                                    </span>
                                </td>
                                
                                <!-- Progress Bar -->
                                <td class="px-4 py-3">
                                    @if($project->tasks_count > 0)
                                        <div class="w-16 h-1.5 bg-gray-800 rounded overflow-hidden">
                                            <div class="h-full" style="width: {{ ($project->progress ?? 0) }}%; background-color: #0ea5e9;"></div>
                                        </div>
                                    @else
                                        <span class="text-gray-600 text-xs">—</span>
                                    @endif
                                </td>
                                
                                <!-- Due Date -->
                                <td class="px-4 py-3 text-sm font-mono text-gray-400">
                                    @if($project->due_date)
                                        <span class="{{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-red-400' : '' }}">
                                            {{ $project->due_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endif
                                </td>
                                
                                <!-- Task Count -->
                                <td class="px-4 py-3 text-center font-mono text-gray-400">
                                    {{ $project->tasks_count ?? 0 }}
                                </td>
                                
                                <!-- Action Button -->
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('projects.show', $project) }}" class="text-xs accent-cyan font-mono hover:text-white transition">
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
        @if($projects->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $projects->links('pagination::tailwind') }}
            </div>
        @endif
    @else
        <!-- EMPTY STATE -->
        <div class="grid-panel p-12 text-center">
            <p class="text-gray-500 mb-4">No projects found</p>
            @can('create', App\Models\Project::class)
                <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#0ea5e9] hover:bg-[#0284c7] text-[#050509] font-semibold text-sm rounded transition">
                    + Create First Project
                </a>
            @endcan
        </div>
    @endif
@endsection


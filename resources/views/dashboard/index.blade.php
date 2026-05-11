@extends('layouts.app')

@section('title', 'Dashboard - Task Management System')
@section('page-title', 'Dashboard')

@section('content')
    <!-- BENTO GRID: KPI Metrics with Enterprise Design -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 auto-rows-max">
        <!-- KPI Card 1: Total Projects -->
        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <!-- Header with icon -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest letter-spacing-1">Total Projects</h3>
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <!-- Main metric -->
            <p class="text-4xl font-bold text-slate-900 mb-2">{{ $projectCount }}</p>
            <!-- Secondary info -->
            <p class="text-sm text-slate-600">Health Score: <span class="font-semibold text-emerald-600">{{ $projectHealth }}%</span></p>
        </div>

        <!-- KPI Card 2: My Tasks -->
        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <!-- Header with icon -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest">My Tasks</h3>
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <!-- Main metric -->
            <p class="text-4xl font-bold text-slate-900 mb-2">{{ $assignedTasks }}</p>
            <!-- Secondary info -->
            <p class="text-sm text-slate-600">Completed: <span class="font-semibold text-indigo-600">{{ $completedTasks }}</span></p>
        </div>

        <!-- KPI Card 3: Overdue Tasks -->
        <div class="bg-white border {{ $overdueTasks > 0 ? 'border-red-200' : 'border-slate-200' }} rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <!-- Header with icon -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-semibold {{ $overdueTasks > 0 ? 'text-red-600' : 'text-slate-500' }} uppercase tracking-widest">Overdue Tasks</h3>
                <svg class="w-5 h-5 {{ $overdueTasks > 0 ? 'text-red-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <!-- Main metric -->
            <p class="text-4xl font-bold {{ $overdueTasks > 0 ? 'text-red-700' : 'text-slate-900' }} mb-2">{{ $overdueTasks }}</p>
            <!-- Secondary info -->
            <p class="text-sm {{ $overdueTasks > 0 ? 'text-red-600' : 'text-slate-600' }}">Due Today: <span class="font-semibold">{{ $tasksDueToday }}</span></p>
        </div>

        <!-- KPI Card 4: Priority Breakdown Summary -->
        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <!-- Header -->
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4">Priority Distribution</h3>
            <!-- Compact breakdown -->
            <div class="space-y-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-600">Critical</span>
                    <span class="font-semibold text-red-600">{{ $projectsByPriority['critical'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-600">High</span>
                    <span class="font-semibold text-orange-600">{{ $projectsByPriority['high'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-600">Medium</span>
                    <span class="font-semibold text-amber-600">{{ $projectsByPriority['medium'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center text-xs pt-2 border-t border-slate-100">
                    <span class="text-slate-600 font-semibold">Total</span>
                    <span class="font-bold text-slate-900">{{ ($projectsByPriority['critical'] ?? 0) + ($projectsByPriority['high'] ?? 0) + ($projectsByPriority['medium'] ?? 0) + ($projectsByPriority['low'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ENTERPRISE LAYOUT: Two-column grid for Project Health & Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- LEFT: Project Health (spanning 2 columns) - Lean forensic table design -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <!-- Card header -->
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Project Health Monitor
                </h3>
            </div>
            
            <!-- Card content -->
            <div class="px-6 py-4">
                @if(count($recentProjects) > 0)
                    <div class="space-y-3">
                        @foreach($recentProjects as $project)
                            <!-- Forensic table row: condensed, clean hover -->
                            <div class="border border-slate-100 rounded p-4 hover:bg-slate-50 transition duration-150">
                                <!-- Row header: Project name + status badge -->
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-slate-900">{{ $project['name'] ?? 'Untitled' }}</h4>
                                        <p class="text-xs text-slate-500 mt-1">Manager: <span class="font-medium text-slate-700">{{ $project['manager']['name'] ?? 'Unassigned' }}</span></p>
                                    </div>
                                    <x-status-badge :status="$project['status']" />
                                </div>
                                <!-- Health progress bar -->
                                <div class="mb-3">
                                    <x-progress-bar 
                                        :percentage="($project['health_score'] ?? 0)" 
                                        :color="(($project['health_score'] ?? 0) >= 75 ? 'bg-emerald-600' : ((($project['health_score'] ?? 0) >= 50) ? 'bg-amber-600' : 'bg-red-600'))"
                                    />
                                </div>
                                <!-- Meta info -->
                                <p class="text-xs text-slate-600">
                                    <span class="font-semibold text-slate-900">{{ count($project['tasks'] ?? []) }}</span> tasks
                                    @if($project['tasks'])
                                        · <span class="font-semibold text-slate-900">{{ collect($project['tasks'])->where('status', 'completed')->count() }}</span> completed
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty state -->
                    <div class="text-center py-8">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-slate-500 text-xs font-medium">No projects yet. Create one to get started.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT: Recent Activity Feed (Database Trigger Integration) -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <!-- Card header -->
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Activity Ledger
                </h3>
            </div>
            
            <!-- Card content -->
            <div class="px-6 py-4">
                @if($recentActivities->count() > 0)
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @foreach($recentActivities as $activity)
                            <!-- Activity row: minimal, left border accent -->
                            <div class="border-l-2 border-emerald-500 pl-3 py-2 hover:bg-slate-50 rounded-r transition duration-150">
                                <p class="text-xs font-medium text-slate-900">
                                    {{ $activity->user->name ?? 'System' }}
                                </p>
                                <p class="text-xs text-slate-600 mt-0.5 line-clamp-2">
                                    {{ $activity->description }}
                                </p>
                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty state -->
                    <div class="text-center py-6">
                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-slate-500 text-xs font-medium">No activity yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- ALERT SECTION: Overdue Tasks Warning (only shows if overdue tasks exist) -->
    @if($overdueTasks > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 shadow-sm">
            <!-- Alert header -->
            <div class="flex items-center gap-3 mb-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2m0 4v2m0 4v2"></path>
                </svg>
                <h3 class="text-sm font-semibold text-red-900">Overdue Tasks Alert</h3>
            </div>
            <!-- Alert message -->
            <p class="text-sm text-red-700 mb-3">{{ $overdueTasks }} task(s) past due date. Immediate action required.</p>
            <!-- Alert action -->
            <a href="{{ route('tasks.index') }}?status=overdue" class="inline-block text-sm font-medium text-red-600 hover:text-red-700 underline">
                View overdue tasks →
            </a>
        </div>
    @endif
@endsection

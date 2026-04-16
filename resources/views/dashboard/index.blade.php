@extends('layouts.app')

@section('title', 'Dashboard - Task Management System')
@section('page-title', 'Dashboard')

@section('content')
    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Projects -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Total Projects</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $projectCount }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-4">Average Health: <span class="font-semibold text-slate-900">{{ $projectHealth }}%</span></p>
        </div>

        <!-- My Tasks -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">My Tasks</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $assignedTasks }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-4">Completed: <span class="font-semibold text-slate-900">{{ $completedTasks }}</span></p>
        </div>

        <!-- Overdue Tasks -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm hover:shadow-md transition {{ $overdueTasks > 0 ? 'border-red-300 border-2' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Overdue Tasks</p>
                    <p class="text-3xl font-bold {{ $overdueTasks > 0 ? 'text-red-600' : 'text-slate-900' }} mt-2">{{ $overdueTasks }}</p>
                </div>
                <div class="w-12 h-12 {{ $overdueTasks > 0 ? 'bg-red-100' : 'bg-green-100' }} rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $overdueTasks > 0 ? 'text-red-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-4">Due Today: <span class="font-semibold text-slate-900">{{ $tasksDueToday }}</span></p>
        </div>

        <!-- Priority Breakdown (Projects) -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
            <p class="text-slate-600 text-sm font-medium mb-4">📊 Project Priority Breakdown</p>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600">🔴 Critical</span>
                    <span class="font-semibold text-red-600">{{ $projectsByPriority['critical'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600">🟠 High</span>
                    <span class="font-semibold text-orange-600">{{ $projectsByPriority['high'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600">🟡 Medium</span>
                    <span class="font-semibold text-amber-600">{{ $projectsByPriority['medium'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600">🔵 Low</span>
                    <span class="font-semibold text-slate-600">{{ $projectsByPriority['low'] ?? 0 }}</span>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-4 pt-3 border-t border-slate-100">
                Total: <span class="font-semibold text-slate-900">{{ ($projectsByPriority['critical'] ?? 0) + ($projectsByPriority['high'] ?? 0) + ($projectsByPriority['medium'] ?? 0) + ($projectsByPriority['low'] ?? 0) }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Project Health Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Project Health
                </h3>
                
                @if(count($recentProjects) > 0)
                    <div class="space-y-4">
                        @foreach($recentProjects as $project)
                            <div class="border border-slate-100 rounded-lg p-4 hover:bg-slate-50 transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-slate-900">{{ $project['name'] ?? 'Untitled' }}</h4>
                                        <p class="text-xs text-slate-500 mt-1">Manager: <span class="font-medium">{{ $project['manager']['name'] ?? 'Unassigned' }}</span></p>
                                    </div>
                                    <x-status-badge :status="$project['status']" />
                                </div>
                                <x-progress-bar 
                                    :percentage="($project['health_score'] ?? 0)" 
                                    :color="(($project['health_score'] ?? 0) >= 75 ? 'bg-green-600' : ((($project['health_score'] ?? 0) >= 50) ? 'bg-yellow-600' : 'bg-red-600'))"
                                />
                                <p class="text-xs text-slate-600 mt-2">
                                    <span class="font-medium">{{ count($project['tasks'] ?? []) }}</span> tasks
                                    @if($project['tasks'])
                                        · <span class="font-medium">{{ collect($project['tasks'])->where('status', 'completed')->count() }}</span> completed
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-slate-500 text-sm">No projects yet. Create one to get started!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Recent Activity
            </h3>

            @if($recentActivities->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($recentActivities as $activity)
                        <div class="border-l-2 border-indigo-400 pl-3 py-2 hover:bg-slate-50 rounded-r transition">
                            <p class="text-xs font-medium text-slate-900">
                                {{ $activity->user->name ?? 'System' }}
                            </p>
                            <p class="text-xs text-slate-600 mt-1">
                                {{ $activity->description }}
                            </p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-slate-500 text-xs">No activity yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Burning Tasks Section -->
    @if($overdueTasks > 0)
        <div class="mt-8 bg-white rounded-lg border border-red-300 border-2 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-red-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2m0 4v2m0 4v2"></path>
                </svg>
                🔥 Burning Tasks (Overdue)
            </h3>
            <p class="text-sm text-red-700 mb-4">These tasks are past their due date. Action required!</p>
            <div class="text-center py-4 text-red-600 font-semibold">
                {{ $overdueTasks }} task(s) needs attention
            </div>
        </div>
    @endif
@endsection

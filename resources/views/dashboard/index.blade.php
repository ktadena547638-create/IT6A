@extends('layouts.app')

@section('title', 'Dashboard - Task Management System')
@section('page-title', 'Dashboard')

@section('content')
    <!-- KPI Grid with Enhanced Design -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Projects - Blue Gradient -->
        <div class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl border border-blue-200 dark:border-blue-700 p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 dark:text-blue-300 text-xs font-semibold uppercase tracking-wider">Total Projects</p>
                    <p class="text-4xl font-bold text-blue-900 dark:text-blue-100 mt-3">{{ $projectCount }}</p>
                </div>
                <div class="w-16 h-16 bg-white dark:bg-blue-800/50 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                <p class="text-xs text-blue-600 dark:text-blue-300">Health Score: <span class="font-bold text-lg">{{ $projectHealth }}%</span></p>
            </div>
        </div>

        <!-- My Tasks - Indigo Gradient -->
        <div class="group bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/30 dark:to-indigo-800/30 rounded-xl border border-indigo-200 dark:border-indigo-700 p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-600 dark:text-indigo-300 text-xs font-semibold uppercase tracking-wider">My Tasks</p>
                    <p class="text-4xl font-bold text-indigo-900 dark:text-indigo-100 mt-3">{{ $assignedTasks }}</p>
                </div>
                <div class="w-16 h-16 bg-white dark:bg-indigo-800/50 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-indigo-200 dark:border-indigo-700">
                <p class="text-xs text-indigo-600 dark:text-indigo-300">Completed: <span class="font-bold text-lg">{{ $completedTasks }}</span></p>
            </div>
        </div>

        <!-- Overdue Tasks - Red/Green Gradient -->
        <div class="group bg-gradient-to-br {{ $overdueTasks > 0 ? 'from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 border-red-200 dark:border-red-700' : 'from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 border-green-200 dark:border-green-700' }} rounded-xl border p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="{{  $overdueTasks > 0 ? 'text-red-600 dark:text-red-300' : 'text-green-600 dark:text-green-300' }} text-xs font-semibold uppercase tracking-wider">Overdue Tasks</p>
                    <p class="text-4xl font-bold {{ $overdueTasks > 0 ? 'text-red-900 dark:text-red-100' : 'text-green-900 dark:text-green-100' }} mt-3">{{ $overdueTasks }}</p>
                </div>
                <div class="w-16 h-16 {{ $overdueTasks > 0 ? 'bg-white dark:bg-red-800/50' : 'bg-white dark:bg-green-800/50' }} rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all">
                    <svg class="w-8 h-8 {{ $overdueTasks > 0 ? 'text-red-600 dark:text-red-300' : 'text-green-600 dark:text-green-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 {{ $overdueTasks > 0 ? 'border-t border-red-200 dark:border-red-700' : 'border-t border-green-200 dark:border-green-700' }}">
                <p class="text-xs {{ $overdueTasks > 0 ? 'text-red-600 dark:text-red-300' : 'text-green-600 dark:text-green-300' }}">Due Today: <span class="font-bold text-lg">{{ $tasksDueToday }}</span></p>
            </div>
        </div>

        <!-- Priority Breakdown - Purple Gradient -->
        <div class="group bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl border border-purple-200 dark:border-purple-700 p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <p class="text-purple-600 dark:text-purple-300 text-xs font-semibold uppercase tracking-wider mb-4">📊 Priority Breakdown</p>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">🔴 Critical</span>
                    <span class="font-bold px-3 py-1 bg-red-200 dark:bg-red-900/50 text-red-700 dark:text-red-200 rounded-full text-sm">{{ $projectsByPriority['critical'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">🟠 High</span>
                    <span class="font-bold px-3 py-1 bg-orange-200 dark:bg-orange-900/50 text-orange-700 dark:text-orange-200 rounded-full text-sm">{{ $projectsByPriority['high'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">🟡 Medium</span>
                    <span class="font-bold px-3 py-1 bg-amber-200 dark:bg-amber-900/50 text-amber-700 dark:text-amber-200 rounded-full text-sm">{{ $projectsByPriority['medium'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">🔵 Low</span>
                    <span class="font-bold px-3 py-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-full text-sm">{{ $projectsByPriority['low'] ?? 0 }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-purple-200 dark:border-purple-700">
                <p class="text-xs text-purple-600 dark:text-purple-300 font-semibold">Total: <span class="text-lg">{{ ($projectsByPriority['critical'] ?? 0) + ($projectsByPriority['high'] ?? 0) + ($projectsByPriority['medium'] ?? 0) + ($projectsByPriority['low'] ?? 0) }}</span></p>
            </div>
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

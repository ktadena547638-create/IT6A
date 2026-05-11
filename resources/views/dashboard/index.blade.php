@extends('layouts.app')

@section('title', 'Dashboard - TaskFlow')
@section('page-title', '📊 Dashboard')

@section('content')
    <!-- OBSIDIAN HIGH-DENSITY GRID: KPI Metrics with monospaced numbers -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <!-- KPI 1: Total Projects -->
        <div class="grid-panel p-4 hover:border-[#2d2d38]">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Projects</p>
            <p class="metrics-display text-3xl text-white mb-2">{{ str_pad($projectCount, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">Health: <span class="accent-cyan">{{ $projectHealth }}%</span></p>
        </div>

        <!-- KPI 2: My Tasks -->
        <div class="grid-panel p-4 hover:border-[#2d2d38]">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Tasks</p>
            <p class="metrics-display text-3xl text-white mb-2">{{ str_pad($assignedTasks, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">Done: <span class="accent-cyan">{{ $completedTasks }}</span></p>
        </div>

        <!-- KPI 3: Overdue (Alert if > 0) -->
        <div class="grid-panel p-4 {{ $overdueTasks > 0 ? 'border-red-600/50' : 'hover:border-[#2d2d38]' }}">
            <p class="text-xs {{ $overdueTasks > 0 ? 'text-red-400' : 'text-gray-500' }} uppercase tracking-wider mb-3">Overdue</p>
            <p class="metrics-display text-3xl {{ $overdueTasks > 0 ? 'text-red-400' : 'text-white' }} mb-2">{{ str_pad($overdueTasks, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">Today: <span class="{{ $tasksDueToday > 0 ? 'text-orange-400' : 'accent-cyan' }}">{{ $tasksDueToday }}</span></p>
        </div>

        <!-- KPI 4: Priority Breakdown -->
        <div class="grid-panel p-4 hover:border-[#2d2d38]">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Distribution</p>
            <div class="space-y-1 text-xs">
                <div class="flex justify-between"><span class="text-red-400">Critical:</span> <span>{{ $projectsByPriority['critical'] ?? 0 }}</span></div>
                <div class="flex justify-between"><span class="text-orange-400">High:</span> <span>{{ $projectsByPriority['high'] ?? 0 }}</span></div>
                <div class="flex justify-between"><span class="accent-cyan">Medium:</span> <span>{{ $projectsByPriority['medium'] ?? 0 }}</span></div>
            </div>
        </div>
    </div>


    <!-- OBSIDIAN SYSTEM LOG: Project Health Monitor -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- LEFT: Project Health Table -->
        <div class="lg:col-span-2 grid-panel">
            <div class="border-b border-gray-700/50 px-4 py-3 flex items-center justify-between">
                <p class="text-xs uppercase tracking-wider accent-cyan font-semibold">📋 Project Health Monitor</p>
                <span class="text-xs text-gray-500">{{ count($recentProjects) }} active</span>
            </div>
            
            @if(count($recentProjects) > 0)
                <div class="divide-y divide-gray-700/50">
                    @foreach($recentProjects as $project)
                        <div class="px-4 py-3 hover:bg-[#1e1e28] transition text-sm">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <p class="font-medium text-white">{{ $project['name'] ?? 'Untitled' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">📌 {{ $project['manager']['name'] ?? 'Unassigned' }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded border {{ 
                                    $project['status'] === 'active' ? 'border-[#0ea5e9] text-[#0ea5e9]' : 
                                    ($project['status'] === 'completed' ? 'border-green-600/50 text-green-400' : 'border-gray-600/50 text-gray-400')
                                }}">{{ strtoupper($project['status']) }}</span>
                            </div>
                            <!-- Health bar -->
                            <div class="h-1 bg-gray-800 rounded overflow-hidden mb-2">
                                <div class="h-full" style="width: {{ ($project['health_score'] ?? 0) }}%; background-color: #0ea5e9;"></div>
                            </div>
                            <p class="text-xs text-gray-500">{{ count($project['tasks'] ?? []) }} tasks • {{ collect($project['tasks'])->where('status', 'completed')->count() }} done</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <p class="text-xs text-gray-500">No projects available</p>
                </div>
            @endif
        </div>

        <!-- RIGHT: Activity Ledger -->
        <div class="grid-panel">
            <div class="border-b border-gray-700/50 px-4 py-3 flex items-center justify-between">
                <p class="text-xs uppercase tracking-wider accent-cyan font-semibold">📝 Activity Log</p>
                <span class="text-xs text-gray-500">{{ $recentActivities->count() }}</span>
            </div>
            
            @if($recentActivities->count() > 0)
                <div class="max-h-72 overflow-y-auto divide-y divide-gray-700/50">
                    @foreach($recentActivities as $activity)
                        <div class="px-4 py-2 hover:bg-[#1e1e28] transition text-xs border-l-2 border-[#0ea5e9]">
                            <p class="font-medium text-white">{{ $activity->user->name ?? 'System' }}</p>
                            <p class="text-gray-400 mt-0.5 line-clamp-2">{{ $activity->description }}</p>
                            <p class="text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <p class="text-xs text-gray-500">No activity yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- CRITICAL ALERT: Overdue Tasks -->
    @if($overdueTasks > 0)
        <div class="grid-panel border-red-600/50 p-4">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs uppercase tracking-wider text-red-400 font-semibold mb-2">⚠️ Critical: Overdue Tasks</p>
                    <p class="text-sm text-gray-300">{{ $overdueTasks }} task(s) past due. Immediate action required.</p>
                </div>
                <a href="{{ route('tasks.index') }}?status=overdue" class="text-xs accent-cyan font-semibold hover:underline ml-4 flex-shrink-0">View →</a>
            </div>
        </div>
    @endif
@endsection
@endsection

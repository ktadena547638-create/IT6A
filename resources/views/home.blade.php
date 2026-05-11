@extends('layouts.app')

@section('title', 'Home')

@section('content')
<style>
    .hero-grid {
        background-image:
            linear-gradient(rgba(14, 165, 233, 0.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(14, 165, 233, 0.08) 1px, transparent 1px),
            radial-gradient(circle at 85% 20%, rgba(14, 165, 233, 0.24), transparent 45%);
        background-size: 24px 24px, 24px 24px, auto;
    }
    .dashboard-card {
        transition: transform 180ms ease, border-color 180ms ease, background-color 180ms ease;
    }
    .dashboard-card:hover {
        transform: translateY(-3px);
        border-color: rgba(14, 165, 233, 0.5) !important;
        background-color: rgba(14, 165, 233, 0.06);
    }
    .mini-pill {
        border: 1px solid #2a2f3a;
        background: #11151d;
    }
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 rounded-2xl border border-cyan-500/30 hero-grid" style="background-color: #0b0f17;">
        <div class="p-8 md:p-10 flex items-center justify-between gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-400 mb-3">Mission Control</p>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    Welcome back, <span class="text-cyan-300">{{ $userName ?? 'Guest' }}</span>
                </h1>
                <p class="text-gray-300 text-base md:text-lg">
                    {{ Auth::user()?->role === 'admin' ? 'You have full system access' : 'Keep your tasks on track' }}
                </p>
            </div>
            <div class="hidden md:flex items-center justify-center w-20 h-20 rounded-2xl border border-cyan-500/40 bg-cyan-500/10 text-3xl">📊</div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="grid-panel rounded-xl p-5 border-cyan-500/20 dashboard-card">
            <div class="flex items-start justify-between mb-4">
                <p class="text-xs uppercase tracking-[0.14em] text-cyan-400 font-semibold">Total Projects</p>
                <div class="mini-pill w-10 h-10 rounded-lg flex items-center justify-center text-lg">📁</div>
            </div>
            <p class="metrics-display text-4xl text-white mb-2">{{ str_pad($quickStats['total_projects'] ?? 0, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">All your projects</p>
        </div>

        <div class="grid-panel rounded-xl p-5 border-green-500/20 dashboard-card">
            <div class="flex items-start justify-between mb-4">
                <p class="text-xs uppercase tracking-[0.14em] text-green-400 font-semibold">Active Now</p>
                <div class="mini-pill w-10 h-10 rounded-lg flex items-center justify-center text-lg">🚀</div>
            </div>
            <p class="metrics-display text-4xl text-white mb-2">{{ str_pad($quickStats['active_projects'] ?? 0, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">In progress</p>
        </div>

        <div class="grid-panel rounded-xl p-5 border-orange-500/20 dashboard-card">
            <div class="flex items-start justify-between mb-4">
                <p class="text-xs uppercase tracking-[0.14em] text-orange-400 font-semibold">Assigned Tasks</p>
                <div class="mini-pill w-10 h-10 rounded-lg flex items-center justify-center text-lg">✓</div>
            </div>
            <p class="metrics-display text-4xl text-white mb-2">{{ str_pad($quickStats['assigned_tasks'] ?? 0, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">Awaiting attention</p>
        </div>

        <div class="grid-panel rounded-xl p-5 border-purple-500/20 dashboard-card">
            <div class="flex items-start justify-between mb-4">
                <p class="text-xs uppercase tracking-[0.14em] text-purple-400 font-semibold">Completed Today</p>
                <div class="mini-pill w-10 h-10 rounded-lg flex items-center justify-center text-lg">⭐</div>
            </div>
            <p class="metrics-display text-4xl text-white mb-2">{{ str_pad($quickStats['completed_today'] ?? 0, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">Great progress</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <span>📊</span> Live Activity Ledger
                </h2>
                <a href="{{ route('tasks.index') }}" class="text-sm text-cyan-400 hover:text-cyan-300 font-semibold">View All →</a>
            </div>

            <div class="space-y-3">
                @forelse($activityFeed ?? [] as $activity)
                    <div class="grid-panel rounded-lg p-4 border-l-2 border-cyan-500/60 dashboard-card">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex px-2 py-1 text-xs rounded mini-pill text-cyan-300 font-semibold">{{ $activity['activity_type'] ?? 'activity' }}</span>
                            <span class="text-xs text-gray-500">{{ $activity['created_at'] ?? 'recently' }}</span>
                        </div>
                        <p class="text-sm text-gray-200 font-medium">
                            <span class="text-white">{{ $activity['user_name'] ?? 'System' }}</span> {{ $activity['description'] ?? '' }}
                        </p>
                        @if($activity['task_title'] ?? null)
                            <p class="text-xs text-gray-400 mt-1">
                                Task:
                                <a href="{{ route('tasks.show', $activity['task_id']) }}" class="text-cyan-400 hover:text-cyan-300 hover:underline">
                                    {{ Str::limit($activity['task_title'], 60) }}
                                </a>
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="grid-panel rounded-lg p-8 text-center text-gray-400">
                        <p class="text-sm">📭 No recent activities</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-3">
                    <span>⏰</span> Upcoming Tasks
                </h3>

                <div class="space-y-2">
                    @forelse($upcomingTasks ?? [] as $task)
                        <a href="{{ route('tasks.show', $task['id']) }}" class="block grid-panel rounded-lg p-3 border-l-2 {{ $task['is_overdue'] ? 'border-red-500/70' : 'border-gray-600/70' }} dashboard-card">
                            <div class="flex items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ Str::limit($task['title'], 40) }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $task['due_date'] ?? 'No date' }}
                                        @if($task['is_overdue'])
                                            <span class="ml-1 text-red-400 font-semibold">⚠ OVERDUE</span>
                                        @endif
                                    </p>
                                </div>
                                @php
                                    $priority = $task['priority'] ?? 'medium';
                                    $priorityClass = $priority === 'critical' ? 'border-red-500/40 text-red-300 bg-red-500/10' :
                                        ($priority === 'high' ? 'border-orange-500/40 text-orange-300 bg-orange-500/10' :
                                        ($priority === 'low' ? 'border-green-500/40 text-green-300 bg-green-500/10' :
                                        'border-yellow-500/40 text-yellow-300 bg-yellow-500/10'));
                                @endphp
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold whitespace-nowrap border {{ $priorityClass }}">
                                    {{ ucfirst($priority) }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="grid-panel rounded-lg p-4 text-center text-gray-400">
                            <p class="text-sm">✨ No pending tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-3">
                    <span>📋</span> Recent Projects
                </h3>

                <div class="space-y-3">
                    @forelse($recentProjects ?? [] as $project)
                        <a href="{{ route('projects.show', $project['id']) }}" class="block grid-panel rounded-lg p-4 border-l-2 border-cyan-500/60 dashboard-card">
                            <div class="flex items-start justify-between mb-2 gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-white truncate">{{ Str::limit($project['name'], 35) }}</p>
                                    <p class="text-xs text-gray-400 mt-1">by {{ $project['manager_name'] ?? 'Unassigned' }}</p>
                                </div>
                                @php
                                    $status = $project['status'] ?? 'unknown';
                                    $statusClass = $status === 'active' ? 'border-blue-500/40 text-blue-300 bg-blue-500/10' :
                                        ($status === 'planning' ? 'border-gray-500/40 text-gray-300 bg-gray-500/10' :
                                        ($status === 'on_hold' ? 'border-yellow-500/40 text-yellow-300 bg-yellow-500/10' :
                                        ($status === 'completed' ? 'border-green-500/40 text-green-300 bg-green-500/10' :
                                        'border-red-500/40 text-red-300 bg-red-500/10')));
                                @endphp
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold whitespace-nowrap border {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-400 font-semibold">{{ $project['stats']['completed_tasks'] ?? 0 }}/{{ $project['stats']['total_tasks'] ?? 0 }} tasks</span>
                                    <span class="text-xs text-gray-300 font-bold">{{ $project['completion_percent'] ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-[#10131a] rounded-full h-2 border border-[#1d2330] overflow-hidden">
                                    <div class="h-2 rounded-full" style="width: {{ $project['completion_percent'] ?? 0 }}%; background: linear-gradient(90deg, #0891b2 0%, #22d3ee 100%);"></div>
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                <div class="mini-pill rounded p-1.5 text-center">
                                    <p class="text-green-300 font-bold">{{ $project['stats']['completed_tasks'] ?? 0 }}</p>
                                    <p class="text-gray-400 text-xs">Done</p>
                                </div>
                                <div class="mini-pill rounded p-1.5 text-center">
                                    <p class="text-orange-300 font-bold">{{ $project['stats']['in_progress_tasks'] ?? 0 }}</p>
                                    <p class="text-gray-400 text-xs">In Progress</p>
                                </div>
                                <div class="mini-pill rounded p-1.5 text-center">
                                    <p class="text-gray-300 font-bold">{{ $project['stats']['pending_tasks'] ?? 0 }}</p>
                                    <p class="text-gray-400 text-xs">Pending</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="grid-panel rounded-lg p-4 text-center text-gray-400">
                            <p class="text-sm">📭 No projects</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

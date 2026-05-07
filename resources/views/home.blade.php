@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- ========== WELCOME HERO SECTION ========== --}}
        <div class="mb-8 p-8 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-700 dark:to-purple-700 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">
                        Welcome back, {{ $userName ?? 'Guest' }}! 👋
                    </h1>
                    <p class="text-indigo-100 text-lg">
                        {{ Auth::user()?->role === 'admin' ? 'You have full system access' : 'Keep your tasks on track' }}
                    </p>
                </div>
                <div class="hidden md:flex items-center justify-center w-24 h-24 bg-white/20 rounded-full">
                    <div class="text-5xl">📊</div>
                </div>
            </div>
        </div>

        {{-- ========== QUICK STATS GRID - ENHANCED ========== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- Total Projects Card --}}
            <div class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl border border-blue-200 dark:border-blue-700 p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 dark:text-blue-300 text-xs font-bold uppercase tracking-wider">Total Projects</p>
                        <p class="text-4xl font-bold text-blue-900 dark:text-blue-100 mt-3">
                            {{ $quickStats['total_projects'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-white dark:bg-blue-800/50 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all text-2xl">📁</div>
                </div>
                <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                    <p class="text-xs text-blue-600 dark:text-blue-300 font-medium">All your projects</p>
                </div>
            </div>

            {{-- Active Projects Card --}}
            <div class="group bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl border border-green-200 dark:border-green-700 p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 dark:text-green-300 text-xs font-bold uppercase tracking-wider">Active Now</p>
                        <p class="text-4xl font-bold text-green-900 dark:text-green-100 mt-3">
                            {{ $quickStats['active_projects'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-white dark:bg-green-800/50 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all text-2xl">🚀</div>
                </div>
                <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-700">
                    <p class="text-xs text-green-600 dark:text-green-300 font-medium">In progress</p>
                </div>
            </div>

            {{-- Assigned Tasks Card --}}
            <div class="group bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl border border-orange-200 dark:border-orange-700 p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-600 dark:text-orange-300 text-xs font-bold uppercase tracking-wider">Assigned Tasks</p>
                        <p class="text-4xl font-bold text-orange-900 dark:text-orange-100 mt-3">
                            {{ $quickStats['assigned_tasks'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-white dark:bg-orange-800/50 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all text-2xl">✓</div>
                </div>
                <div class="mt-4 pt-4 border-t border-orange-200 dark:border-orange-700">
                    <p class="text-xs text-orange-600 dark:text-orange-300 font-medium">Awaiting attention</p>
                </div>
            </div>

            {{-- Completed Today Card --}}
            <div class="group bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl border border-purple-200 dark:border-purple-700 p-6 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-600 dark:text-purple-300 text-xs font-bold uppercase tracking-wider">Completed Today</p>
                        <p class="text-4xl font-bold text-purple-900 dark:text-purple-100 mt-3">
                            {{ $quickStats['completed_today'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-white dark:bg-purple-800/50 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all text-2xl">⭐</div>
                </div>
                <div class="mt-4 pt-4 border-t border-purple-200 dark:border-purple-700">
                    <p class="text-xs text-purple-600 dark:text-purple-300 font-medium">Great progress!</p>
                </div>
            </div>
        </div>

        {{-- ========== MAIN CONTENT GRID ========== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- ========== LEFT COLUMN: Activity Feed (2 cols on desktop) ========== --}}
            <div class="lg:col-span-2">
                {{-- Live Activity Ledger Header --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="text-2xl">📊</span> Live Activity Ledger
                        </h2>
                        <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold">
                            View All →
                        </a>
                    </div>
                </div>

                {{-- Activity Feed Cards --}}
                <div class="space-y-3">
                    @forelse($activityFeed ?? [] as $activity)
                        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4 border-l-4 border-indigo-500 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-block px-2 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-semibold rounded">
                                            {{ $activity['activity_type'] ?? 'activity' }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['created_at'] ?? 'recently' }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                        <strong>{{ $activity['user_name'] ?? 'System' }}</strong> {{ $activity['description'] ?? '' }}
                                    </p>
                                    @if($activity['task_title'] ?? null)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Task: <a href="{{ route('tasks.show', $activity['task_id']) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                {{ Str::limit($activity['task_title'], 60) }}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-8 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-sm">📭 No recent activities</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ========== RIGHT COLUMN: Upcoming Tasks + Recent Projects ========== --}}
            <div class="space-y-8">
                {{-- Upcoming Tasks Section --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="text-xl">⏰</span> Upcoming Tasks
                        </h3>
                    </div>

                    <div class="space-y-2">
                        @forelse($upcomingTasks ?? [] as $task)
                            <a href="{{ route('tasks.show', $task['id']) }}" class="block bg-white dark:bg-slate-800 rounded-lg shadow p-3 border-l-4 hover:shadow-md transition-all group"
                               :class="{'border-red-500': {{ $task['is_overdue'] ? 'true' : 'false' }}, 'border-gray-300 dark:border-slate-700': !{{ $task['is_overdue'] ? 'true' : 'false' }}}">
                                <div class="flex items-start gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">
                                            {{ Str::limit($task['title'], 40) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $task['due_date'] ?? 'No date' }}
                                            @if($task['is_overdue'])
                                                <span class="ml-1 text-red-600 dark:text-red-400 font-semibold">⚠️ OVERDUE</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold whitespace-nowrap"
                                          :class="{
                                              'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': '{{ $task['priority'] }}' === 'critical',
                                              'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200': '{{ $task['priority'] }}' === 'high',
                                              'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': '{{ $task['priority'] }}' === 'medium',
                                              'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': '{{ $task['priority'] }}' === 'low'
                                          }">
                                        {{ ucfirst($task['priority'] ?? 'medium') }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-sm">✨ No pending tasks</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Projects Section --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="text-xl">📋</span> Recent Projects
                        </h3>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentProjects ?? [] as $project)
                            <a href="{{ route('projects.show', $project['id']) }}" class="block bg-white dark:bg-slate-800 rounded-lg shadow p-4 border-l-4 border-indigo-500 hover:shadow-lg transition-all">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                            {{ Str::limit($project['name'], 35) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            by {{ $project['manager_name'] ?? 'Unassigned' }}
                                        </p>
                                    </div>
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold whitespace-nowrap"
                                          :class="{
                                              'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': '{{ $project['status'] }}' === 'active',
                                              'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200': '{{ $project['status'] }}' === 'planning',
                                              'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': '{{ $project['status'] }}' === 'on_hold',
                                              'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': '{{ $project['status'] }}' === 'completed',
                                              'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': '{{ $project['status'] }}' === 'cancelled'
                                          }">
                                        {{ ucfirst(str_replace('_', ' ', $project['status'] ?? 'unknown')) }}
                                    </span>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="mt-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 font-semibold">
                                            {{ $project['stats']['completed_tasks'] ?? 0 }}/{{ $project['stats']['total_tasks'] ?? 0 }} tasks
                                        </span>
                                        <span class="text-xs text-gray-600 dark:text-gray-400 font-bold">
                                            {{ $project['completion_percent'] ?? 0 }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all"
                                             style="width: {{ $project['completion_percent'] ?? 0 }}%"></div>
                                    </div>
                                </div>

                                {{-- Task Stats --}}
                                <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                    <div class="bg-green-50 dark:bg-slate-700 rounded p-1.5 text-center">
                                        <p class="text-green-700 dark:text-green-400 font-bold">{{ $project['stats']['completed_tasks'] ?? 0 }}</p>
                                        <p class="text-gray-600 dark:text-gray-400 text-xs">Done</p>
                                    </div>
                                    <div class="bg-orange-50 dark:bg-slate-700 rounded p-1.5 text-center">
                                        <p class="text-orange-700 dark:text-orange-400 font-bold">{{ $project['stats']['in_progress_tasks'] ?? 0 }}</p>
                                        <p class="text-gray-600 dark:text-gray-400 text-xs">In Progress</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-slate-700 rounded p-1.5 text-center">
                                        <p class="text-gray-700 dark:text-gray-400 font-bold">{{ $project['stats']['pending_tasks'] ?? 0 }}</p>
                                        <p class="text-gray-600 dark:text-gray-400 text-xs">Pending</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-sm">📭 No projects</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

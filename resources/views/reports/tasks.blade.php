@extends('layouts.app')

@section('title', 'Analytics - TaskFlow')
@section('page-title', '📊 Task Analytics')

@section('content')
    <!-- HEADER -->
    <div class="mb-6">
        <h2 class="text-xl accent-cyan font-bold">Task Report Analysis</h2>
        <p class="text-xs text-gray-500 mt-1">Priority breakdown and deadline tracking</p>
    </div>

    <!-- METRIC CARDS GRID: High-density KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Card 1: Overdue Tasks -->
        <div class="grid-panel p-4">
            <p class="text-xs text-red-400 uppercase tracking-wider mb-2">⚠️ Overdue Tasks</p>
            <p class="metrics-display text-3xl text-white mb-2">
                @if(is_countable($overdueTasks))
                    {{ str_pad(count($overdueTasks), 2, '0', STR_PAD_LEFT) }}
                @else
                    {{ str_pad($overdueTasks ?? 0, 2, '0', STR_PAD_LEFT) }}
                @endif
            </p>
            <p class="text-xs {{ (is_countable($overdueTasks) ? count($overdueTasks) : $overdueTasks) > 0 ? 'text-red-400' : 'text-green-400' }}">
                @if((is_countable($overdueTasks) ? count($overdueTasks) : $overdueTasks) > 0)
                    Action required
                @else
                    On schedule
                @endif
            </p>
        </div>

        <!-- Card 2: Due Today -->
        <div class="grid-panel p-4">
            <p class="text-xs text-amber-400 uppercase tracking-wider mb-2">📅 Due Today</p>
            <p class="metrics-display text-3xl text-white mb-2">
                @if(is_countable($tasksDueToday))
                    {{ str_pad(count($tasksDueToday), 2, '0', STR_PAD_LEFT) }}
                @else
                    {{ str_pad($tasksDueToday ?? 0, 2, '0', STR_PAD_LEFT) }}
                @endif
            </p>
            <p class="text-xs text-gray-400">
                @if((is_countable($tasksDueToday) ? count($tasksDueToday) : $tasksDueToday) > 0)
                    Today's focus
                @else
                    No deadlines
                @endif
            </p>
        </div>

        <!-- Card 3: Total Tasks -->
        <div class="grid-panel p-4">
            <p class="text-xs accent-cyan uppercase tracking-wider mb-2">📋 Total Tasks</p>
            <p class="metrics-display text-3xl text-white mb-2">
                {{ str_pad(($tasksByPriority['critical'] ?? 0) + ($tasksByPriority['high'] ?? 0) + ($tasksByPriority['medium'] ?? 0) + ($tasksByPriority['low'] ?? 0), 3, '0', STR_PAD_LEFT) }}
            </p>
            <p class="text-xs text-gray-400">All priorities combined</p>
        </div>
    </div>

    <!-- PRIORITY DISTRIBUTION: System log bars -->
    <div class="grid-panel">
        <div class="border-b border-gray-700/50 px-4 py-3">
            <p class="text-xs uppercase tracking-wider accent-cyan font-semibold">📈 Priority Distribution</p>
        </div>

        <div class="px-4 py-6">
            @php
                $total = ($tasksByPriority['critical'] ?? 0) + 
                        ($tasksByPriority['high'] ?? 0) + 
                        ($tasksByPriority['medium'] ?? 0) + 
                        ($tasksByPriority['low'] ?? 0);
                $getBarWidth = fn($count) => $total > 0 ? round(($count / $total) * 100, 1) : 0;
            @endphp

            <div class="space-y-4">
                <!-- Critical -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-mono text-red-400">🔴 CRITICAL</span>
                        <span class="text-xs font-mono text-red-400">{{ $tasksByPriority['critical'] ?? 0 }} ({{ $getBarWidth($tasksByPriority['critical'] ?? 0) }}%)</span>
                    </div>
                    <div class="h-1.5 bg-gray-800 rounded overflow-hidden">
                        <div class="h-full bg-red-600" style="width: {{ $getBarWidth($tasksByPriority['critical'] ?? 0) }}%"></div>
                    </div>
                </div>

                <!-- High -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-mono text-orange-400">🟠 HIGH</span>
                        <span class="text-xs font-mono text-orange-400">{{ $tasksByPriority['high'] ?? 0 }} ({{ $getBarWidth($tasksByPriority['high'] ?? 0) }}%)</span>
                    </div>
                    <div class="h-1.5 bg-gray-800 rounded overflow-hidden">
                        <div class="h-full bg-orange-600" style="width: {{ $getBarWidth($tasksByPriority['high'] ?? 0) }}%"></div>
                    </div>
                </div>

                <!-- Medium -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-mono text-amber-400">🟡 MEDIUM</span>
                        <span class="text-xs font-mono text-amber-400">{{ $tasksByPriority['medium'] ?? 0 }} ({{ $getBarWidth($tasksByPriority['medium'] ?? 0) }}%)</span>
                    </div>
                    <div class="h-1.5 bg-gray-800 rounded overflow-hidden">
                        <div class="h-full bg-amber-600" style="width: {{ $getBarWidth($tasksByPriority['medium'] ?? 0) }}%"></div>
                    </div>
                </div>

                <!-- Low -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-mono text-blue-400">🔵 LOW</span>
                        <span class="text-xs font-mono text-blue-400">{{ $tasksByPriority['low'] ?? 0 }} ({{ $getBarWidth($tasksByPriority['low'] ?? 0) }}%)</span>
                    </div>
                    <div class="h-1.5 bg-gray-800 rounded overflow-hidden">
                        <div class="h-full bg-blue-600" style="width: {{ $getBarWidth($tasksByPriority['low'] ?? 0) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Summary line -->
            <div class="mt-4 pt-4 border-t border-gray-700/50">
                <p class="text-xs text-gray-500 font-mono">Total analyzed: <span class="accent-cyan">{{ $total }}</span> tasks</p>
            </div>
        </div>
    </div>

    <!-- ACTION SECTION -->
    <div class="mt-6 text-center">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#0ea5e9] hover:bg-[#0284c7] text-[#050509] font-semibold text-sm rounded transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            View All Tasks
        </a>
    </div>
@endsection


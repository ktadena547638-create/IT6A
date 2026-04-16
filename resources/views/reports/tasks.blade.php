@extends('layouts.app')

@section('title', 'Task Analytics Report')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Task Analytics Report</h1>
        <p class="mt-1 text-gray-500">Overview of task priorities, deadlines, and completion status</p>
    </div>

    {{-- Metric Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Overdue Tasks Card --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Overdue Tasks</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        @if(is_countable($overdueTasks))
                            {{ count($overdueTasks) }}
                        @else
                            {{ $overdueTasks ?? 0 }}
                        @endif
                    </p>
                </div>
                <div class="text-4xl text-red-500">⚠️</div>
            </div>
            <p class="mt-4 text-sm text-gray-500">
                @if((is_countable($overdueTasks) ? count($overdueTasks) : $overdueTasks) > 0)
                    Requires immediate attention
                @else
                    All caught up! ✨
                @endif
            </p>
        </div>

        {{-- Due Today Card --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Due Today</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        @if(is_countable($tasksDueToday))
                            {{ count($tasksDueToday) }}
                        @else
                            {{ $tasksDueToday ?? 0 }}
                        @endif
                    </p>
                </div>
                <div class="text-4xl text-amber-500">📅</div>
            </div>
            <p class="mt-4 text-sm text-gray-500">
                @if((is_countable($tasksDueToday) ? count($tasksDueToday) : $tasksDueToday) > 0)
                    Keep focused on today
                @else
                    No pressing deadlines
                @endif
            </p>
        </div>

        {{-- Total Tasks Card --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ ($tasksByPriority['critical'] ?? 0) + ($tasksByPriority['high'] ?? 0) + ($tasksByPriority['medium'] ?? 0) + ($tasksByPriority['low'] ?? 0) }}
                    </p>
                </div>
                <div class="text-4xl text-blue-500">📊</div>
            </div>
            <p class="mt-4 text-sm text-gray-500">Across all priorities</p>
        </div>
    </div>

    {{-- Priority Breakdown Card --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Priority Breakdown</h2>
        
        {{-- Calculate total safely --}}
        @php
            $total = ($tasksByPriority['critical'] ?? 0) + 
                    ($tasksByPriority['high'] ?? 0) + 
                    ($tasksByPriority['medium'] ?? 0) + 
                    ($tasksByPriority['low'] ?? 0);
            $getBarWidth = fn($count) => $total > 0 ? round(($count / $total) * 100, 1) : 0;
        @endphp
        
        <div class="space-y-4">
            {{-- Critical Priority --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="inline-block w-3 h-3 rounded-full bg-red-600 mr-3"></span>
                    <span class="text-sm font-medium text-gray-700">Critical</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-red-600" style="width: {{ $getBarWidth($tasksByPriority['critical'] ?? 0) }}%"></div>
                    </div>
                    <span class="text-lg font-bold text-gray-900 w-12 text-right">{{ $tasksByPriority['critical'] ?? 0 }}</span>
                </div>
            </div>

            {{-- High Priority --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="inline-block w-3 h-3 rounded-full bg-orange-500 mr-3"></span>
                    <span class="text-sm font-medium text-gray-700">High</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-500" style="width: {{ $getBarWidth($tasksByPriority['high'] ?? 0) }}%"></div>
                    </div>
                    <span class="text-lg font-bold text-gray-900 w-12 text-right">{{ $tasksByPriority['high'] ?? 0 }}</span>
                </div>
            </div>

            {{-- Medium Priority --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-3"></span>
                    <span class="text-sm font-medium text-gray-700">Medium</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-500" style="width: {{ $getBarWidth($tasksByPriority['medium'] ?? 0) }}%"></div>
                    </div>
                    <span class="text-lg font-bold text-gray-900 w-12 text-right">{{ $tasksByPriority['medium'] ?? 0 }}</span>
                </div>
            </div>

            {{-- Low Priority --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-3"></span>
                    <span class="text-sm font-medium text-gray-700">Low</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500" style="width: {{ $getBarWidth($tasksByPriority['low'] ?? 0) }}%"></div>
                    </div>
                    <span class="text-lg font-bold text-gray-900 w-12 text-right">{{ $tasksByPriority['low'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Button --}}
    <div class="mt-8 text-center">
        <a href="{{ route('tasks.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            View All Tasks
        </a>
    </div>
</div>
@endsection

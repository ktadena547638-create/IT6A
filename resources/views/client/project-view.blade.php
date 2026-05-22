@extends('layouts.app')

@section('title', 'Project: ' . $project->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
            <p class="text-gray-600 mt-2">{{ $project->description }}</p>
        </div>
        <a href="{{ route('client.dashboard') }}" class="px-4 py-2 text-blue-600 hover:text-blue-700 font-medium">
            ← Back to Dashboard
        </a>
    </div>

    {{-- Project Health Card --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Project Status</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Completion Progress --}}
            <div>
                <p class="text-sm text-gray-600 mb-2">Overall Completion</p>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <div class="bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">{{ $completionPercentage }}%</span>
                </div>
            </div>

            {{-- Task Breakdown --}}
            <div>
                <p class="text-sm text-gray-600 mb-3">Task Status</p>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 uppercase mb-1">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-green-600 uppercase mb-1 font-semibold">Completed</p>
                        <p class="text-2xl font-bold text-green-600">{{ $completedTasks }}</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-orange-600 uppercase mb-1 font-semibold">Active</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $activeTasks }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Project Details --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Project Details</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Project Manager</p>
                <p class="text-lg font-medium text-gray-900">{{ $project->manager->name ?? 'Unassigned' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Status</p>
                <p class="text-lg font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $project->status) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Priority</p>
                <span class="inline-block px-2 py-1 rounded text-sm font-semibold
                    {{ $project->priority === 'critical' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $project->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                    {{ $project->priority === 'medium' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $project->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}
                ">
                    {{ ucfirst($project->priority) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Due Date</p>
                <p class="text-lg font-medium text-gray-900">{{ $project->due_date?->format('M d, Y') ?? 'No due date' }}</p>
            </div>
        </div>
    </div>

    {{-- Tasks List --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">✅ Tasks</h2>
        </div>

        @if ($project->tasks->isEmpty())
            <div class="p-6 text-center text-gray-500">
                No tasks in this project
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach ($project->tasks as $task)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900">{{ $task->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                            </div>
                            <div class="ml-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $task->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                            <span>
                                <strong>Assigned to:</strong> {{ $task->assignedUser->name ?? 'Unassigned' }}
                            </span>
                            <span class="text-xs">
                                <strong>Priority:</strong>
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold
                                    {{ $task->priority === 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $task->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $task->priority === 'medium' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}
                                ">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </span>
                            @if ($task->due_date)
                                <span>
                                    <strong>Due:</strong> {{ $task->due_date->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection


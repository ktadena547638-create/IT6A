@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">👁️ Your Projects</h1>
        <p class="text-gray-600 mt-2">Monitor your project progress and active tasks</p>
    </div>

    {{-- Empty State --}}
    @if ($projectsData->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-500 text-lg mb-4">No projects assigned to you yet</p>
            <p class="text-gray-400">Once a project is assigned to you, you'll see it here</p>
        </div>
    @else
        {{-- Projects Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($projectsData as $data)
                <a href="{{ route('client.project', $data['project']) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <div class="p-6">
                        {{-- Project Header --}}
                        <div class="mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $data['project']->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Manager: {{ $data['project']->manager->name ?? 'Unassigned' }}</p>
                        </div>

                        {{-- Project Health Summary --}}
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600 mb-2">
                                <strong>Project Health:</strong>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $data['completion_percentage'] }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $data['completion_percentage'] }}%</span>
                            </div>
                        </div>

                        {{-- Task Summary --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-blue-50 rounded p-2 text-center">
                                <p class="text-xs text-blue-600 uppercase font-semibold">Total</p>
                                <p class="text-lg font-bold text-blue-900">{{ $data['total_tasks'] }}</p>
                            </div>
                            <div class="bg-green-50 rounded p-2 text-center">
                                <p class="text-xs text-green-600 uppercase font-semibold">Completed</p>
                                <p class="text-lg font-bold text-green-900">{{ $data['completed_tasks'] }}</p>
                            </div>
                            <div class="bg-orange-50 rounded p-2 text-center">
                                <p class="text-xs text-orange-600 uppercase font-semibold">Active</p>
                                <p class="text-lg font-bold text-orange-900">{{ $data['active_tasks'] }}</p>
                            </div>
                        </div>

                        {{-- Due Date --}}
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500">
                                <strong>Due:</strong> {{ $data['project']->due_date?->format('M d, Y') ?? 'No due date' }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Overall Summary --}}
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Portfolio Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Total Projects</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $projectsData->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Tasks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $projectsData->sum('total_tasks') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-green-600">{{ $projectsData->sum('completed_tasks') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">In Progress</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $projectsData->sum('active_tasks') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

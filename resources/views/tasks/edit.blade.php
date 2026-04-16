@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Task</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <h3 class="font-medium text-red-900 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.update', $task) }}" class="bg-white rounded-lg shadow p-8">
        @csrf
        @method('PUT')

        {{-- Project --}}
        <div class="mb-6">
            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project *</label>
            <select id="project_id" name="project_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('project_id') border-red-500 @enderror">
                @forelse($projects as $project)
                    <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @empty
                    <option value="" disabled>No projects available</option>
                @endforelse
            </select>
            @error('project_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Title --}}
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Task Title *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $task->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
            <select id="status" name="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Priority --}}
        <div class="mb-6">
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
            <select id="priority" name="priority" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('priority') border-red-500 @enderror">
                <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
                <option value="critical" {{ old('priority', $task->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
            @error('priority')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Assigned User --}}
        <div class="mb-6">
            <label for="assigned_user_id" class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
            <select id="assigned_user_id" name="assigned_user_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('assigned_user_id') border-red-500 @enderror">
                <option value="">Unassigned</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_user_id', $task->assigned_user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            @error('assigned_user_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Due Date --}}
        <div class="mb-6">
            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
            <input type="date" id="due_date" name="due_date" 
                   value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('due_date') border-red-500 @enderror">
            @error('due_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex gap-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Update Task
            </button>
            <a href="{{ route('tasks.show', $task) }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

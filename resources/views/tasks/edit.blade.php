@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="mb-10">
        <h1 class="text-4xl font-bold text-white mb-2">
            {{ auth()->user()->isTeamMember() ? '✅ Update Task Status' : '🛠 Edit Task' }}
        </h1>
        <p class="text-gray-400 text-sm">Modify task details while preserving assignment and workflow integrity</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg border border-red-500/30 bg-red-500/10">
            <h3 class="font-medium text-red-300 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-red-300 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.update', $task) }}" class="rounded-lg overflow-hidden border border-cyan-500/30 p-8" style="background-color: #0d0d12; box-shadow: 0 10px 30px rgba(14, 165, 233, 0.1);">
        @csrf
        @method('PUT')

        {{-- GENERALS ONLY: Project --}}
        @if(!auth()->user()->isTeamMember())
            <div class="mb-6">
                <label for="project_id" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Project *</label>
                <select id="project_id" name="project_id" required
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('project_id') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                        style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                    @forelse($projects as $project)
                        <option style="background-color: #0d0d12; color: #d1d5db;" value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @empty
                        <option value="" disabled style="background-color: #0d0d12; color: #d1d5db;">No projects available</option>
                    @endforelse
                </select>
                @error('project_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Title --}}
            <div class="mb-6">
                <label for="title" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Task Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" required
                       class="w-full px-4 py-3 rounded-lg border-2 transition @error('title') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                       style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                @error('title')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-4 py-3 rounded-lg border-2 transition @error('description') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                          style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        {{-- Status (ALL ROLES) --}}
        <div class="mb-6">
            <label for="status" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">
                {{ auth()->user()->isTeamMember() ? 'Task Status (Update Your Progress)' : 'Status *' }}
            </label>
            <select id="status" name="status" required
                    class="w-full px-4 py-3 rounded-lg border-2 transition @error('status') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                    style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                <option style="background-color: #0d0d12; color: #d1d5db;" value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                <option style="background-color: #0d0d12; color: #d1d5db;" value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option style="background-color: #0d0d12; color: #d1d5db;" value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- GENERALS ONLY: Priority --}}
        @if(!auth()->user()->isTeamMember())
            <div class="mb-6">
                <label for="priority" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Priority *</label>
                <select id="priority" name="priority" required
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('priority') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                        style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                    <option style="background-color: #0d0d12; color: #d1d5db;" value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                    <option style="background-color: #0d0d12; color: #d1d5db;" value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option style="background-color: #0d0d12; color: #d1d5db;" value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
                    <option style="background-color: #0d0d12; color: #d1d5db;" value="critical" {{ old('priority', $task->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
                @error('priority')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Assigned User --}}
            <div class="mb-6">
                <label for="assigned_user_id" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Assign To</label>
                <select id="assigned_user_id" name="assigned_user_id"
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('assigned_user_id') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                        style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                    <option style="background-color: #0d0d12; color: #d1d5db;" value="">Unassigned</option>
                    @forelse($users as $user)
                        <option style="background-color: #0d0d12; color: #d1d5db;" value="{{ $user->id }}" {{ old('assigned_user_id', $task->assigned_user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @empty
                        <option value="" disabled style="background-color: #0d0d12; color: #d1d5db;">No team members available</option>
                    @endforelse
                </select>
                @error('assigned_user_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Due Date --}}
            <div class="mb-6">
                <label for="due_date" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Due Date</label>
                <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d\TH:i')) }}"
                       class="w-full px-4 py-3 rounded-lg border-2 transition @error('due_date') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                       style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                @error('due_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        {{-- Submit --}}
        <div class="flex gap-4 pt-6 border-t border-cyan-500/20">
            <button type="submit" class="px-6 py-3 border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 rounded-lg hover:bg-cyan-500/20 transition font-medium">
                Save Changes
            </button>
            <a href="{{ route('tasks.show', $task) }}" class="px-6 py-3 border border-gray-500/40 text-gray-300 rounded-lg hover:bg-gray-500/10 transition font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection


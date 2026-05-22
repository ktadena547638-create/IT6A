@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="mb-10">
        <h1 class="text-4xl font-bold text-white mb-2">🛠 Edit Project</h1>
        <p class="text-gray-400 text-sm">Update details, ownership, and project strategy settings</p>
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

    <form method="POST" action="{{ route('projects.update', $project) }}" class="rounded-lg overflow-hidden border border-cyan-500/30 p-8" style="background-color: #0d0d12; box-shadow: 0 10px 30px rgba(14, 165, 233, 0.1);">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-8">
            <label for="name" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Project Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" required
                   class="w-full px-4 py-3 rounded-lg border-2 transition @error('name') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                   style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
            @error('name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-8">
            <label for="description" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-4 py-3 rounded-lg border-2 transition @error('description') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                      style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">{{ old('description', $project->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Manager Assignment (Admin Delegation) --}}
        @if(auth()->user()->isAdmin())
            <div class="mb-8">
                <label for="manager_id" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">
                    <span>👥 Assign Manager</span>
                    <span class="text-xs text-gray-400 ml-2">(Delegate project responsibility)</span>
                </label>
                <select id="manager_id" name="manager_id" required
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('manager_id') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                        style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                    <option value="" style="background-color: #0d0d12; color: #d1d5db;">-- Select Project Manager --</option>
                    @forelse($managers as $manager)
                        <option value="{{ $manager->id }}" style="background-color: #0d0d12; color: #d1d5db;"
                                {{ old('manager_id', $project->manager_id) == $manager->id ? 'selected' : '' }}>
                            {{ $manager->name }}
                        </option>
                    @empty
                        <option value="" disabled style="background-color: #0d0d12; color: #d1d5db;">No project managers available</option>
                    @endforelse
                </select>
                @error('manager_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-400">Current Manager: <strong class="text-gray-200">{{ $project->manager?->name ?? 'Unassigned' }}</strong></p>
            </div>
        @endif

        {{-- Status & Priority Row --}}
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <label for="status" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Status *</label>
                <select id="status" name="status" required
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('status') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                        style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                    <option value="planning" style="background-color: #0d0d12; color: #d1d5db;" {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="active" style="background-color: #0d0d12; color: #d1d5db;" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="on_hold" style="background-color: #0d0d12; color: #d1d5db;" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="completed" style="background-color: #0d0d12; color: #d1d5db;" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">
                    <span>🎯 Project Priority</span>
                    <span class="text-xs text-gray-400 ml-2">(Strategic Urgency)</span>
                </label>
                <select id="priority" name="priority" required
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('priority') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                        style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                    <option value="low" style="background-color: #0d0d12; color: #d1d5db;" {{ old('priority', $project->priority) === 'low' ? 'selected' : '' }}>🔵 Low</option>
                    <option value="medium" style="background-color: #0d0d12; color: #d1d5db;" {{ old('priority', $project->priority) === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="high" style="background-color: #0d0d12; color: #d1d5db;" {{ old('priority', $project->priority) === 'high' ? 'selected' : '' }}>🟠 High</option>
                    <option value="critical" style="background-color: #0d0d12; color: #d1d5db;" {{ old('priority', $project->priority) === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                </select>
                @error('priority')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Start Date --}}
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <label for="start_date" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Start Date *</label>
                <input type="date" id="start_date" name="start_date"
                       value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" required
                       class="w-full px-4 py-3 rounded-lg border-2 transition @error('start_date') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                       style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                @error('start_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- End Date --}}
            <div>
                <label for="end_date" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">End Date *</label>
                <input type="date" id="end_date" name="end_date"
                       value="{{ old('end_date', $project->due_date?->format('Y-m-d')) }}" required
                       class="w-full px-4 py-3 rounded-lg border-2 transition @error('end_date') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                       style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                @error('end_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-4 pt-6 border-t border-cyan-500/20">
            <button type="submit" class="px-6 py-3 border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 rounded-lg hover:bg-cyan-500/20 transition font-medium">
                Update Project
            </button>
            <a href="{{ route('projects.show', $project) }}" class="px-6 py-3 border border-gray-500/40 text-gray-300 rounded-lg hover:bg-gray-500/10 transition font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection


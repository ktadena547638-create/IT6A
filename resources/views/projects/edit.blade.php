@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Project</h1>

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

    <form method="POST" action="{{ route('projects.update', $project) }}" class="bg-white rounded-lg shadow p-8">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Project Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $project->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Manager Assignment (Admin Delegation) --}}
        @if(auth()->user()->isAdmin())
            <div class="mb-6">
                <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="font-semibold">👥 Assign Manager</span>
                    <span class="text-xs text-gray-500 ml-2">(Delegate project responsibility)</span>
                </label>
                <select id="manager_id" name="manager_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('manager_id') border-red-500 @enderror">
                    <option value="">-- Select Project Manager --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                                {{ old('manager_id', $project->manager_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
                        </option>
                    @endforeach
                </select>
                @error('manager_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">Current Manager: <strong>{{ $project->manager?->name ?? 'Unassigned' }}</strong></p>
            </div>
        @endif

        {{-- Status & Priority Row --}}
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select id="status" name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                    <option value="planning" {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    <span>🎯 Project Priority</span>
                    <span class="text-xs text-gray-500 ml-2">(Strategic Urgency)</span>
                </label>
                <select id="priority" name="priority" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('priority') border-red-500 @enderror">
                    <option value="low" {{ old('priority', $project->priority) === 'low' ? 'selected' : '' }}>🔵 Low</option>
                    <option value="medium" {{ old('priority', $project->priority) === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="high" {{ old('priority', $project->priority) === 'high' ? 'selected' : '' }}>🟠 High</option>
                    <option value="critical" {{ old('priority', $project->priority) === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                </select>
                @error('priority')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Start Date --}}
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                <input type="date" id="start_date" name="start_date" 
                       value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_date') border-red-500 @enderror">
                @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- End Date --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                <input type="date" id="end_date" name="end_date" 
                       value="{{ old('end_date', $project->due_date?->format('Y-m-d')) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('end_date') border-red-500 @enderror">
                @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Update Project
            </button>
            <a href="{{ route('projects.show', $project) }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

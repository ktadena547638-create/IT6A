@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-4xl font-bold accent-cyan mb-2">🚀 Create New Project</h1>
        <p class="text-gray-400 text-sm">Initialize a new project with team members</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg border border-red-500/30 bg-red-500/10">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-medium text-red-400 mb-2">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.store') }}" class="rounded-lg overflow-hidden border border-cyan-500/30 p-8" style="background-color: #0d0d12; box-shadow: 0 10px 30px rgba(14, 165, 233, 0.1);">
        @csrf

        {{-- Name --}}
        <div class="mb-8">
            <label for="name" class="block text-sm font-semibold text-cyan-400 mb-3 uppercase tracking-wider">Project Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter project name..."
                   class="w-full px-4 py-3 rounded-lg border-2 transition placeholder-gray-600 @error('name') border-red-500 @else border-cyan-500/30 focus:border-cyan-500 @enderror" style="background-color: rgba(14, 165, 233, 0.05); color: #ffffff;">
            @error('name')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-8">
            <label for="description" class="block text-sm font-semibold text-cyan-400 mb-3 uppercase tracking-wider">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Add project description..."
                      class="w-full px-4 py-3 rounded-lg border-2 transition placeholder-gray-600 @error('description') border-red-500 @else border-cyan-500/30 focus:border-cyan-500 @enderror" style="background-color: rgba(14, 165, 233, 0.05); color: #ffffff; resize: vertical;">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
            @enderror
        </div>

        {{-- Manager (Admin only) --}}
        @if(auth()->user()->isAdmin())
            <div class="mb-8">
                <label for="manager_id" class="block text-sm font-semibold text-cyan-400 mb-3 uppercase tracking-wider">Project Manager *</label>
                <select id="manager_id" name="manager_id" required
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('manager_id') border-red-500 @else border-cyan-500/30 focus:border-cyan-500 @enderror" style="background-color: rgba(14, 165, 233, 0.05); color: #ffffff;">
                    <option value="" style="background-color: #0d0d12; color: #d1d5db;">Select a manager...</option>
                    @forelse($managers as $manager)
                        <option value="{{ $manager->id }}" style="background-color: #0d0d12; color: #d1d5db;" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                            👤 {{ $manager->name }}
                        </option>
                    @empty
                        <option value="" disabled style="background-color: #0d0d12; color: #d1d5db;">No project managers available</option>
                    @endforelse
                </select>
                @error('manager_id')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
                @enderror
            </div>
        @else
            <input type="hidden" name="manager_id" value="{{ auth()->id() }}">
        @endif

        {{-- Status --}}
        <div class="mb-8 grid grid-cols-2 gap-6">
            <div>
                <label for="status" class="block text-sm font-semibold text-cyan-400 mb-3 uppercase tracking-wider">Status *</label>
                <select id="status" name="status" required
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('status') border-red-500 @else border-cyan-500/30 focus:border-cyan-500 @enderror" style="background-color: rgba(14, 165, 233, 0.05); color: #ffffff;">
                    <option value="" style="background-color: #0d0d12; color: #d1d5db;">Select a status...</option>
                    <option value="planning" style="background-color: #0d0d12; color: #d1d5db;" {{ old('status') === 'planning' ? 'selected' : '' }}>📝 Planning</option>
                    <option value="active" style="background-color: #0d0d12; color: #d1d5db;" {{ old('status') === 'active' ? 'selected' : '' }}>✅ Active</option>
                    <option value="on_hold" style="background-color: #0d0d12; color: #d1d5db;" {{ old('status') === 'on_hold' ? 'selected' : '' }}>⏸️ On Hold</option>
                    <option value="completed" style="background-color: #0d0d12; color: #d1d5db;" {{ old('status') === 'completed' ? 'selected' : '' }}>🎉 Completed</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-semibold text-cyan-400 mb-3 uppercase tracking-wider">Priority</label>
                <select id="priority" name="priority"
                        class="w-full px-4 py-3 rounded-lg border-2 transition @error('priority') border-red-500 @else border-cyan-500/30 focus:border-cyan-500 @enderror" style="background-color: rgba(14, 165, 233, 0.05); color: #ffffff;">
                    <option value="" style="background-color: #0d0d12; color: #d1d5db;">Select priority...</option>
                    <option value="low" style="background-color: #0d0d12; color: #d1d5db;" {{ old('priority') === 'low' ? 'selected' : '' }}>🔵 Low</option>
                    <option value="medium" style="background-color: #0d0d12; color: #d1d5db;" {{ old('priority') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="high" style="background-color: #0d0d12; color: #d1d5db;" {{ old('priority') === 'high' ? 'selected' : '' }}>🟠 High</option>
                    <option value="critical" style="background-color: #0d0d12; color: #d1d5db;" {{ old('priority') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                </select>
                @error('priority')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Start & End Dates --}}
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <label for="start_date" class="block text-sm font-semibold text-cyan-400 mb-3 uppercase tracking-wider">Start Date *</label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                       class="w-full px-4 py-3 rounded-lg border-2 transition @error('start_date') border-red-500 @else border-cyan-500/30 focus:border-cyan-500 @enderror" style="background-color: rgba(14, 165, 233, 0.05); color: #ffffff;">
                @error('start_date')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="end_date" class="block text-sm font-semibold text-cyan-400 mb-3 uppercase tracking-wider">End Date *</label>
                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                       class="w-full px-4 py-3 rounded-lg border-2 transition @error('end_date') border-red-500 @else border-cyan-500/30 focus:border-cyan-500 @enderror" style="background-color: rgba(14, 165, 233, 0.05); color: #ffffff;">
                @error('end_date')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-4 pt-6 border-t border-cyan-500/20">
            <button type="submit" class="px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-500 transition font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Project
            </button>
            <a href="{{ route('projects.index') }}" class="px-6 py-3 border border-cyan-500/30 text-cyan-400 rounded-lg hover:bg-cyan-500/10 transition font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
    
    select option {
        background-color: #0d0d12;
        color: #d1d5db;
    }
    
    select option:checked {
        background: linear-gradient(#0ea5e9, #0ea5e9);
        background-color: #0ea5e9 !important;
        color: white;
    }
</style>
@endsection

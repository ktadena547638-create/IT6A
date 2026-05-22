@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-4xl font-bold text-white mb-2"><span class="text-cyan-300">👤 Create New</span> User</h1>
        <p class="text-gray-300 text-sm">Add a new user to your system</p>
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

    <form method="POST" action="{{ route('users.store') }}" class="rounded-lg overflow-hidden border border-cyan-400/50 p-8" style="background-color: #0b0f17; box-shadow: 0 16px 40px rgba(14, 165, 233, 0.18);">
        @csrf

        {{-- Name --}}
        <div class="mb-8">
                 <label for="name" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Full Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter full name..."
                     class="w-full px-4 py-3 rounded-lg border-2 transition placeholder-gray-400 @error('name') border-red-500 @else border-cyan-400/40 focus:border-cyan-300 @enderror" style="background-color: rgba(14, 165, 233, 0.12); color: #f8fafc;">
            @error('name')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-8">
                 <label for="email" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Email Address *</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="user@example.com"
                     class="w-full px-4 py-3 rounded-lg border-2 transition placeholder-gray-400 @error('email') border-red-500 @else border-cyan-400/40 focus:border-cyan-300 @enderror" style="background-color: rgba(14, 165, 233, 0.12); color: #f8fafc; font-family: monospace;">
            @error('email')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div class="mb-8">
                <label for="role" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Role *</label>
            <select id="role" name="role" required
                    class="w-full px-4 py-3 rounded-lg border-2 transition @error('role') border-red-500 @else border-cyan-400/40 focus:border-cyan-300 @enderror" style="background-color: rgba(14, 165, 233, 0.12); color: #f8fafc;">
                <option value="" style="background-color: #0d0d12; color: #d1d5db;">Select a role...</option>
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" style="background-color: #0d0d12; color: #d1d5db;" {{ old('role') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
            @enderror
            
            <!-- Role Descriptions -->
            <div class="mt-4 p-4 rounded border border-cyan-400/30 bg-cyan-500/10 space-y-2">
                <div class="text-xs text-gray-300">
                    <span class="font-semibold text-cyan-300">👑 Admin:</span>
                    <span class="text-gray-300">Full system access & user management</span>
                </div>
                <div class="text-xs text-gray-300">
                    <span class="font-semibold text-blue-400">👔 Manager:</span>
                    <span class="text-gray-300">Create & manage own projects</span>
                </div>
                <div class="text-xs text-gray-300">
                    <span class="font-semibold text-green-400">👥 Member:</span>
                    <span class="text-gray-300">Work on assigned tasks</span>
                </div>
                <div class="text-xs text-gray-300">
                    <span class="font-semibold text-orange-400">🔍 Client:</span>
                    <span class="text-gray-300">Read-only project access</span>
                </div>
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-8">
                 <label for="password" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Password *</label>
            <input type="password" id="password" name="password" required placeholder="Enter password..."
                     class="w-full px-4 py-3 rounded-lg border-2 transition placeholder-gray-400 @error('password') border-red-500 @else border-cyan-400/40 focus:border-cyan-300 @enderror" style="background-color: rgba(14, 165, 233, 0.12); color: #f8fafc;">
            @error('password')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-gray-300 flex items-center gap-1"><svg class="w-3 h-3 text-cyan-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 12a1 1 0 102 0V9a1 1 0 10-2 0v3zm1 6a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"/></svg>Minimum 8 characters</p>
        </div>

        {{-- Password Confirmation --}}
        <div class="mb-8">
                 <label for="password_confirmation" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Confirm Password *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm password..."
                     class="w-full px-4 py-3 rounded-lg border-2 transition placeholder-gray-400 @error('password_confirmation') border-red-500 @else border-cyan-400/40 focus:border-cyan-300 @enderror" style="background-color: rgba(14, 165, 233, 0.12); color: #f8fafc;">
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z"/></svg>{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex gap-4 pt-6 border-t border-cyan-500/20">
            <button type="submit" class="px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-500 transition font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create User
            </button>
            <a href="{{ route('users.index') }}" class="px-6 py-3 border border-cyan-500/30 text-cyan-400 rounded-lg hover:bg-cyan-500/10 transition font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
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


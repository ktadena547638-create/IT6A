@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="mb-10">
        <h1 class="text-4xl font-bold text-white mb-2">👤 Edit User</h1>
        <p class="text-gray-400 text-sm">Update account details, role, and optional password reset</p>
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

    <form method="POST" action="{{ route('users.update', $user) }}" class="rounded-lg overflow-hidden border border-cyan-500/30 p-8" style="background-color: #0d0d12; box-shadow: 0 10px 30px rgba(14, 165, 233, 0.1);">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-8">
            <label for="name" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Full Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full px-4 py-3 rounded-lg border-2 transition @error('name') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                   style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
            @error('name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-8">
            <label for="email" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Email Address *</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full px-4 py-3 rounded-lg border-2 transition @error('email') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                   style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
            @error('email')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div class="mb-8">
            <label for="role" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Role *</label>
            <select id="role" name="role" required
                    class="w-full px-4 py-3 rounded-lg border-2 transition @error('role') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                    style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" style="background-color: #0d0d12; color: #d1d5db;" {{ old('role', $user->role) === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password (Optional for Edit) --}}
        <div class="mb-8">
            <label for="password" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Password (Leave blank to keep current)</label>
            <input type="password" id="password" name="password"
                   class="w-full px-4 py-3 rounded-lg border-2 transition @error('password') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                   style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
            @error('password')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-gray-400">Minimum 8 characters if provided</p>
        </div>

        {{-- Password Confirmation --}}
        <div class="mb-8">
            <label for="password_confirmation" class="block text-sm font-semibold text-cyan-300 mb-3 uppercase tracking-wider">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="w-full px-4 py-3 rounded-lg border-2 border-cyan-500/30 focus:border-cyan-400 transition"
                   style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;">
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex gap-4 pt-6 border-t border-cyan-500/20">
            <button type="submit" class="px-6 py-3 border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 rounded-lg hover:bg-cyan-500/20 transition font-medium">
                Update User
            </button>
            <a href="{{ route('users.show', $user) }}" class="px-6 py-3 border border-gray-500/40 text-gray-300 rounded-lg hover:bg-gray-500/10 transition font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold accent-cyan mb-2">👥 User Management</h1>
            <p class="text-gray-400 text-sm">{{ $users->count() }} active users</p>
        </div>
        <a href="{{ route('users.create') }}" class="px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-500 transition font-medium flex items-center gap-2" title="Add new user">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add User
        </a>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg border border-red-500/30 bg-red-500/10">
            <h3 class="font-medium text-red-400 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg border border-green-500/30 bg-green-500/10">
            <p class="text-green-400 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-lg border border-red-500/30 bg-red-500/10">
            <p class="text-red-400 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Users Table Card -->
    <div class="rounded-lg overflow-hidden border border-cyan-500/30" style="background-color: #0d0d12; box-shadow: 0 10px 30px rgba(14, 165, 233, 0.1);">
        @if ($users->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-cyan-500/10 rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.048M12 4.354a4 4 0 110 8.048M12 4.354a4 4 0 110 8.048M3.172 15.172a8 8 0 1116 0M9 10h.01M15 10h.01"></path>
                    </svg>
                </div>
                <p class="text-gray-400 text-lg mb-4">No users found</p>
                <a href="{{ route('users.create') }}" class="text-cyan-400 hover:text-cyan-300 font-medium transition">
                    Create the first user →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-cyan-500/20" style="background-color: rgba(14, 165, 233, 0.05);">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-cyan-400 uppercase tracking-wider">Assigned</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-cyan-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cyan-500/10">
                    @foreach ($users as $user)
                        <tr class="hover:bg-cyan-500/5 transition" style="border-color: rgba(14, 165, 233, 0.1);">
                            <!-- Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-400 font-semibold text-xs">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                </div>
                            </td>
                            <!-- Email -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300 font-mono">{{ $user->email }}</div>
                            </td>
                            <!-- Role -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border
                                    @if ($user->role === 'admin')
                                        border-purple-500/50 bg-purple-500/10 text-purple-400
                                    @elseif ($user->role === 'project_manager')
                                        border-blue-500/50 bg-blue-500/10 text-blue-400
                                    @elseif ($user->role === 'team_member')
                                        border-green-500/50 bg-green-500/10 text-green-400
                                    @elseif ($user->role === 'client')
                                        border-orange-500/50 bg-orange-500/10 text-orange-400
                                    @endif
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <!-- Assigned Items -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-400">
                                    @if ($user->isAdmin())
                                        <span class="text-xs text-gray-500">System Admin</span>
                                    @elseif ($user->isProjectManager())
                                        <span class="text-cyan-400 font-semibold">{{ $user->managed_projects_count ?? 0 }}</span>
                                        <span class="text-gray-500 text-xs">projects</span>
                                    @elseif ($user->isClient())
                                        <span class="text-cyan-400 font-semibold">{{ $user->client_projects_count ?? 0 }}</span>
                                        <span class="text-gray-500 text-xs">projects</span>
                                    @else
                                        <span class="text-cyan-400 font-semibold">{{ $user->assigned_tasks_count ?? 0 }}</span>
                                        <span class="text-gray-500 text-xs">tasks</span>
                                    @endif
                                </div>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('users.show', $user) }}" class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-cyan-400 hover:text-cyan-300 hover:bg-cyan-500/10 rounded transition border border-cyan-500/30 hover:border-cyan-500/50">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-blue-400 hover:text-blue-300 hover:bg-blue-500/10 rounded transition border border-blue-500/30 hover:border-blue-500/50">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    @if (auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded transition border border-red-500/30 hover:border-red-500/50">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-cyan-500/20" style="background-color: rgba(14, 165, 233, 0.03);">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .pagination a, .pagination span {
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid rgba(14, 165, 233, 0.3);
        color: #d1d5db;
        transition: all 0.2s;
    }
    
    .pagination a:hover {
        background-color: rgba(14, 165, 233, 0.1);
        border-color: rgba(14, 165, 233, 0.5);
        color: #0ea5e9;
    }
    
    .pagination .active {
        background-color: rgba(14, 165, 233, 0.2);
        border-color: #0ea5e9;
        color: #0ea5e9;
        font-weight: 600;
    }
</style>
@endsection

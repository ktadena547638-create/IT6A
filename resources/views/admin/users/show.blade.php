@extends('layouts.app')

@section('title', 'User: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-white">{{ $user->name }}</h1>
            <p class="text-gray-400 mt-2">{{ $user->email }}</p>
        </div>
        <div class="text-right">
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full border
                {{ $user->role === 'admin' ? 'bg-purple-500/10 text-purple-300 border-purple-500/40' : '' }}
                {{ $user->role === 'project_manager' ? 'bg-blue-500/10 text-blue-300 border-blue-500/40' : '' }}
                {{ $user->role === 'team_member' ? 'bg-green-500/10 text-green-300 border-green-500/40' : '' }}
                {{ $user->role === 'client' ? 'bg-orange-500/10 text-orange-300 border-orange-500/40' : '' }}
            ">
                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg border border-green-500/30 bg-green-500/10">
            <p class="text-green-300 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- User Details Card --}}
    <div class="rounded-lg border border-cyan-500/30 p-6 mb-8" style="background-color: #0d0d12; box-shadow: 0 10px 30px rgba(14, 165, 233, 0.1);">
        <h2 class="text-2xl font-semibold text-white mb-4">Account Information</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-cyan-300 uppercase tracking-wider">Name</p>
                <p class="text-lg font-medium text-white">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-cyan-300 uppercase tracking-wider">Email</p>
                <p class="text-lg font-medium text-white">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-cyan-300 uppercase tracking-wider">Role</p>
                <p class="text-lg font-medium text-white">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
            </div>
            <div>
                <p class="text-sm text-cyan-300 uppercase tracking-wider">Member Since</p>
                <p class="text-lg font-medium text-white">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Role-Specific Information --}}
    @if ($user->isProjectManager())
        <div class="rounded-lg p-6 mb-8 border border-blue-500/30 bg-blue-500/10">
            <h2 class="text-lg font-semibold text-blue-300 mb-4">Managed Projects</h2>
            @if ($user->managedProjects->isEmpty())
                <p class="text-blue-200">No projects assigned yet</p>
            @else
                <ul class="space-y-2">
                    @foreach ($user->managedProjects as $project)
                        <li class="text-blue-200">
                            <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-100 font-medium">
                                {{ $project->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @elseif ($user->isClient())
        <div class="rounded-lg p-6 mb-8 border border-orange-500/30 bg-orange-500/10">
            <h2 class="text-lg font-semibold text-orange-300 mb-4">Client Projects</h2>
            @if ($user->clientProjects->isEmpty())
                <p class="text-orange-200">No projects assigned yet</p>
            @else
                <ul class="space-y-2">
                    @foreach ($user->clientProjects as $project)
                        <li class="text-orange-200">
                            <a href="{{ route('projects.show', $project) }}" class="hover:text-orange-100 font-medium">
                                {{ $project->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @elseif ($user->isTeamMember())
        <div class="rounded-lg p-6 mb-8 border border-green-500/30 bg-green-500/10">
            <h2 class="text-lg font-semibold text-green-300 mb-4">Assigned Tasks</h2>
            @if ($user->assignedTasks->isEmpty())
                <p class="text-green-200">No tasks assigned yet</p>
            @else
                <ul class="space-y-2">
                    @foreach ($user->assignedTasks as $task)
                        <li class="text-green-200">
                            <a href="{{ route('tasks.show', $task) }}" class="hover:text-green-100 font-medium">
                                {{ $task->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex gap-4">
        <a href="{{ route('users.edit', $user) }}" class="px-6 py-2 border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 rounded-lg hover:bg-cyan-500/20 transition font-medium">
            Edit User
        </a>
        @if (auth()->id() !== $user->id)
            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 border border-red-500/40 bg-red-500/10 text-red-300 rounded-lg hover:bg-red-500/20 transition font-medium">
                    Delete User
                </button>
            </form>
        @endif
        <a href="{{ route('users.index') }}" class="px-6 py-2 border border-gray-500/40 text-gray-300 rounded-lg hover:bg-gray-500/10 transition font-medium">
            Back to Users
        </a>
    </div>
</div>
@endsection


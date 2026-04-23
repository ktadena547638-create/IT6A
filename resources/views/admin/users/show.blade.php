@extends('layouts.app')

@section('title', 'User: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-gray-600 mt-2">{{ $user->email }}</p>
        </div>
        <div class="text-right">
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full
                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                {{ $user->role === 'project_manager' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $user->role === 'team_member' ? 'bg-green-100 text-green-800' : '' }}
                {{ $user->role === 'client' ? 'bg-orange-100 text-orange-800' : '' }}
            ">
                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- User Details Card --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Account Information</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Name</p>
                <p class="text-lg font-medium text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="text-lg font-medium text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Role</p>
                <p class="text-lg font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Member Since</p>
                <p class="text-lg font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Role-Specific Information --}}
    @if ($user->isProjectManager())
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-blue-900 mb-4">Managed Projects</h2>
            @if ($user->managedProjects->isEmpty())
                <p class="text-blue-700">No projects assigned yet</p>
            @else
                <ul class="space-y-2">
                    @foreach ($user->managedProjects as $project)
                        <li class="text-blue-700">
                            <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-900 font-medium">
                                {{ $project->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @elseif ($user->isClient())
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-orange-900 mb-4">Client Projects</h2>
            @if ($user->clientProjects->isEmpty())
                <p class="text-orange-700">No projects assigned yet</p>
            @else
                <ul class="space-y-2">
                    @foreach ($user->clientProjects as $project)
                        <li class="text-orange-700">
                            <a href="{{ route('projects.show', $project) }}" class="hover:text-orange-900 font-medium">
                                {{ $project->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @elseif ($user->isTeamMember())
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-green-900 mb-4">Assigned Tasks</h2>
            @if ($user->assignedTasks->isEmpty())
                <p class="text-green-700">No tasks assigned yet</p>
            @else
                <ul class="space-y-2">
                    @foreach ($user->assignedTasks as $task)
                        <li class="text-green-700">
                            <a href="{{ route('tasks.show', $task) }}" class="hover:text-green-900 font-medium">
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
        <a href="{{ route('users.edit', $user) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Edit User
        </a>
        @if (auth()->id() !== $user->id)
            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Delete User
                </button>
            </form>
        @endif
        <a href="{{ route('users.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            Back to Users
        </a>
    </div>
</div>
@endsection

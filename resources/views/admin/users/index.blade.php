@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">User Management</h1>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Users Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach(\App\Models\User::all() as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : ($user->role === 'project_manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open" type="button" class="text-blue-600 hover:text-blue-900 text-sm">
                                    Change Role
                                </button>

                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg z-10">
                                    <form method="POST" action="/admin/users/{{ $user->id }}/role" class="py-2">
                                        @csrf
                                        @method('PUT')
                                        
                                        <button type="submit" name="role" value="admin" class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm {{ $user->role === 'admin' ? 'bg-blue-50' : '' }}">
                                            Admin
                                        </button>
                                        <button type="submit" name="role" value="project_manager" class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm {{ $user->role === 'project_manager' ? 'bg-blue-50' : '' }}">
                                            Project Manager
                                        </button>
                                        <button type="submit" name="role" value="team_member" class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm {{ $user->role === 'team_member' ? 'bg-blue-50' : '' }}">
                                            Team Member
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('page-title', 'Audit Logs - System History')

@section('content')
<div class="p-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">🔍 Audit Logs</h1>
        <span class="text-sm text-slate-600 dark:text-slate-400">System Sovereignty Oversight</span>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Action</label>
                <select name="action" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                    <option value="">All Actions</option>
                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="assign" {{ request('action') === 'assign' ? 'selected' : '' }}>Assign</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Model Type</label>
                <select name="model_type" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                    <option value="">All Types</option>
                    <option value="Task" {{ request('model_type') === 'Task' ? 'selected' : '' }}>Task</option>
                    <option value="Project" {{ request('model_type') === 'Project' ? 'selected' : '' }}>Project</option>
                    <option value="User" {{ request('model_type') === 'User' ? 'selected' : '' }}>User</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date Range</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Changes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <td class="px-6 py-4 text-sm font-medium">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            {{ $log->action === 'create' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : '' }}
                            {{ $log->action === 'update' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : '' }}
                            {{ $log->action === 'delete' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : '' }}
                            {{ $log->action === 'assign' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' : '' }}
                        ">
                            {{ $log->getActionLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                        {{ $log->model_type }} #{{ $log->model_id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                        {{ $log->user?->name ?? 'System' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        <details class="cursor-pointer">
                            <summary class="font-medium hover:text-slate-900 dark:hover:text-white">
                                {{ Str::limit($log->getChangeSummary(), 40) }}
                            </summary>
                            <div class="mt-2 p-2 bg-slate-100 dark:bg-slate-900 rounded text-xs font-mono max-w-xs overflow-auto">
                                @if($log->changes)
                                    @foreach($log->changes as $field => $change)
                                    <div class="mb-1">
                                        <strong>{{ $field }}:</strong>
                                        <br/>{{ $change['before'] ?? 'null' }} → {{ $change['after'] ?? 'null' }}
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </details>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        {{ $log->ip_address }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                        {{ $log->created_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">
                        No audit logs found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
@endsection


@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('page-title', 'Analytics Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Key Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Tasks --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Total Tasks</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $stats['total_tasks'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Completion Rate --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Completion Rate</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $stats['completion_rate'] }}%</p>
                    <p class="text-sm text-slate-500 mt-1">{{ $stats['completed_tasks'] }} completed</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Overdue Tasks --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Overdue Tasks</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $stats['overdue_tasks'] }}</p>
                    <p class="text-sm text-red-600 mt-1 font-medium">Needs attention</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Projects --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600 font-medium">Active Projects</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $stats['active_projects'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">of {{ $stats['total_projects'] }} total</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.5a1 1 0 00-1 1v2a1 1 0 11-2 0v-2a1 1 0 00-1-1H7a2 2 0 00-2 2v4a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Task Status Distribution (Pie Chart) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Task Status Distribution</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        {{-- Completion Trend (Line Chart) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Completion Trend (Last 7 Days)</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Task Status Breakdown Table --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Task Status Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Status</th>
                        <th class="text-right py-3 px-4 text-sm font-medium text-slate-700">Count</th>
                        <th class="text-right py-3 px-4 text-sm font-medium text-slate-700">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = $stats['total_tasks'];
                    @endphp
                    @foreach($taskStats as $stat)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4 text-sm text-slate-900">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $stat['color'] }}"></span>
                                    {{ $stat['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-sm font-medium text-slate-900">{{ $stat['value'] }}</td>
                            <td class="py-3 px-4 text-right text-sm text-slate-600">{{ round(($stat['value'] / $total) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Chart.js Library --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<script>
    // Status Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($taskStats);
    
    const pieChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => item.label),
            datasets: [{
                data: statusData.map(item => item.value),
                backgroundColor: statusData.map(item => item.color),
                borderColor: '#fff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 }
                    }
                },
            }
        }
    });

    // Completion Trend Line Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendData = @json($completionTrend);
    
    const lineChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendData.labels,
            datasets: [
                {
                    label: 'Tasks Completed',
                    data: trendData.completed,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#10B981',
                },
                {
                    label: 'Tasks Created',
                    data: trendData.created,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 4,
                    pointBackgroundColor: '#3B82F6',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 },
                        usePointStyle: true,
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                    }
                }
            }
        }
    });
</script>

    {{-- Total Users --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="text-4xl">👥</div>
            </div>
        </div>

        {{-- Total Projects --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Projects</p>
                    <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Project::count() }}</p>
                </div>
                <div class="text-4xl">📊</div>
            </div>
        </div>

        {{-- Total Tasks --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Tasks</p>
                    <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Task::count() }}</p>
                </div>
                <div class="text-4xl">✓</div>
            </div>
        </div>

        {{-- Completed Tasks --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completed Tasks</p>
                    <p class="text-3xl font-bold text-green-600">{{ \App\Models\Task::where('status', 'completed')->count() }}</p>
                </div>
                <div class="text-4xl">🎉</div>
            </div>
        </div>
    </div>

    {{-- Charts/Breakdowns --}}
    <div class="grid grid-cols-2 gap-8">
        {{-- Tasks by Status --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Tasks by Status</h2>
            
            @php
                $tasksByStatus = \App\Models\Task::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
            @endphp

            <div class="space-y-4">
                @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $status => $label)
                    @php
                        $count = $tasksByStatus[$status] ?? 0;
                        $total = \App\Models\Task::count();
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-600">{{ $label }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tasks by Priority --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Tasks by Priority</h2>

            @php
                $tasksByPriority = \App\Models\Task::selectRaw('priority, count(*) as count')->groupBy('priority')->pluck('count', 'priority');
            @endphp

            <div class="space-y-4">
                @foreach(['critical' => 'Critical', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $priority => $label)
                    @php
                        $count = $tasksByPriority[$priority] ?? 0;
                        $total = \App\Models\Task::count();
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-600">{{ $label }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Projects by Status --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Projects by Status</h2>

            @php
                $projectsByStatus = \App\Models\Project::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
            @endphp

            <div class="space-y-4">
                @foreach(['planning' => 'Planning', 'active' => 'Active', 'on_hold' => 'On Hold', 'completed' => 'Completed'] as $status => $label)
                    @php
                        $count = $projectsByStatus[$status] ?? 0;
                        $total = \App\Models\Project::count();
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-600">{{ $label }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- User Roles --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Users by Role</h2>

            @php
                $usersByRole = \App\Models\User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role');
            @endphp

            <div class="space-y-4">
                @foreach(['admin' => 'Admins', 'project_manager' => 'Project Managers', 'team_member' => 'Team Members'] as $role => $label)
                    @php
                        $count = $usersByRole[$role] ?? 0;
                        $total = \App\Models\User::count();
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-600">{{ $label }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Recent Activity</h2>

        @php
            $activities = \App\Models\TaskActivity::with(['task', 'user'])->latest()->limit(10)->get();
        @endphp

        @if($activities->count() > 0)
            <div class="space-y-4">
                @foreach($activities as $activity)
                    <div class="flex gap-4 pb-4 border-b last:border-b-0">
                        <div class="text-sm">
                            <p class="text-gray-900">
                                <strong>{{ $activity->user->name ?? 'System' }}</strong> {{ strtolower($activity->action) }} 
                                <a href="{{ route('tasks.show', $activity->task) }}" class="text-blue-600 hover:text-blue-900">
                                    "{{ Str::limit($activity->task->title, 50) }}"
                                </a>
                            </p>
                            <p class="text-gray-500 text-xs mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No recent activity</p>
        @endif
    </div>
</div>
@endsection

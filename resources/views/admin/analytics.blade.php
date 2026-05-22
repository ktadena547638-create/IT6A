@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('page-title', '📊 Analytics Dashboard')

@section('content')
<style>
    @keyframes ping-pulse {
        75%, 100% { transform: scale(2); opacity: 0; }
    }
    .animate-ping-pulse {
        animation: ping-pulse 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }
    .metric-card {
        background: #0d0d12;
        border: 1px solid #1e1e28;
        transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    .metric-card:hover {
        border-color: #2d2d38;
        transform: translateY(-2px);
    }
    .metric-card.critical {
        border-color: rgba(239, 68, 68, 0.5);
    }
</style>

<div class="space-y-6">
    {{-- Key Metrics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Tasks --}}
        <div class="metric-card p-4 rounded">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Total Tasks</p>
            <p class="metrics-display text-3xl text-white mb-1">{{ str_pad($stats['total_tasks'], 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">All statuses</p>
        </div>

        {{-- Completion Rate --}}
        <div class="metric-card p-4 rounded">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Completion</p>
            <p class="metrics-display text-3xl text-emerald-400 mb-1">{{ $stats['completion_rate'] }}%</p>
            <p class="text-xs text-gray-400">{{ $stats['completed_tasks'] }} done</p>
        </div>

        {{-- Overdue Tasks --}}
        <div class="metric-card p-4 rounded {{ $stats['overdue_tasks'] > 0 ? 'critical' : '' }}">
            <p class="text-xs {{ $stats['overdue_tasks'] > 0 ? 'text-red-400' : 'text-gray-500' }} uppercase tracking-wider mb-2">Overdue</p>
            <p class="metrics-display text-3xl {{ $stats['overdue_tasks'] > 0 ? 'text-red-400' : 'text-white' }} mb-1">{{ str_pad($stats['overdue_tasks'], 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">Needs action</p>
        </div>

        {{-- Active Projects --}}
        <div class="metric-card p-4 rounded">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Active</p>
            <p class="metrics-display text-3xl accent-cyan mb-1">{{ str_pad($stats['active_projects'], 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">of {{ $stats['total_projects'] }} total</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Task Status Distribution (Pie Chart) --}}
        <div class="grid-panel p-4 rounded">
            <h3 class="text-xs uppercase tracking-wider accent-cyan font-semibold mb-4">📊 Task Status Distribution</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        {{-- Completion Trend (Line Chart) --}}
        <div class="grid-panel p-4 rounded">
            <h3 class="text-xs uppercase tracking-wider accent-cyan font-semibold mb-4">📈 Completion Trend (7 Days)</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Task Status Breakdown Table --}}
    <div class="grid-panel p-4 rounded">
        <h3 class="text-xs uppercase tracking-wider accent-cyan font-semibold mb-4">Task Status Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-gray-500 font-semibold">Status</th>
                        <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-gray-500 font-semibold">Count</th>
                        <th class="px-4 py-3 text-right text-xs uppercase tracking-wider text-gray-500 font-semibold">Percentage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @php
                        $total = $stats['total_tasks'];
                    @endphp
                    @foreach($taskStats as $stat)
                        <tr class="hover:bg-[#1e1e28] transition">
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $stat['color'] }}"></span>
                                    <span class="text-gray-300">{{ $stat['label'] }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-mono text-white">{{ $stat['value'] }}</td>
                            <td class="px-4 py-3 text-right text-sm font-mono text-gray-400">{{ round(($stat['value'] / $total) * 100, 1) }}%</td>
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
    // Chart.js theme setup
    const chartTextColor = '#d1d5db';
    const chartGridColor = 'rgba(30, 30, 40, 0.5)';

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
                borderColor: '#0d0d12',
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
                        font: { size: 12 },
                        color: chartTextColor,
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
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#0d0d12',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Tasks Created',
                    data: trendData.created,
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.05)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#0ea5e9',
                    pointBorderColor: '#0d0d12',
                    pointBorderWidth: 2,
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
                        color: chartTextColor,
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: chartTextColor,
                    },
                    grid: {
                        color: chartGridColor,
                    }
                },
                x: {
                    ticks: {
                        color: chartTextColor,
                    },
                    grid: {
                        color: chartGridColor,
                    }
                }
            }
        }
    });
</script>

    {{-- System Overview Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Users --}}
        <div class="metric-card p-4 rounded">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Users</p>
            <p class="metrics-display text-3xl text-white mb-1">{{ str_pad(\App\Models\User::count(), 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">active accounts</p>
        </div>

        {{-- Total Projects --}}
        <div class="metric-card p-4 rounded">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Projects</p>
            <p class="metrics-display text-3xl accent-cyan mb-1">{{ str_pad(\App\Models\Project::count(), 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">all statuses</p>
        </div>

        {{-- Completed Tasks --}}
        <div class="metric-card p-4 rounded">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Completed</p>
            <p class="metrics-display text-3xl text-emerald-400 mb-1">{{ str_pad(\App\Models\Task::where('status', 'completed')->count(), 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-gray-400">finished tasks</p>
        </div>

        {{-- System Health --}}
        <div class="metric-card p-4 rounded">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Health</p>
            <p class="metrics-display text-3xl text-white mb-1">✓ 100%</p>
            <p class="text-xs text-gray-400">operational</p>
        </div>
    </div>

    {{-- Priority & Status Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Tasks by Status --}}
        <div class="grid-panel p-4 rounded">
            <h3 class="text-xs uppercase tracking-wider accent-cyan font-semibold mb-4">Status Breakdown</h3>
            
            @php
                $tasksByStatus = \App\Models\Task::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
            @endphp

            <div class="space-y-3">
                @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $status => $label)
                    @php
                        $count = $tasksByStatus[$status] ?? 0;
                        $total = \App\Models\Task::count();
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-mono text-gray-300">{{ $label }}</span>
                            <span class="text-xs font-mono text-white">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-cyan-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tasks by Priority --}}
        <div class="grid-panel p-4 rounded">
            <h3 class="text-xs uppercase tracking-wider accent-cyan font-semibold mb-4">Priority Distribution</h3>

            @php
                $tasksByPriority = \App\Models\Task::selectRaw('priority, count(*) as count')->groupBy('priority')->pluck('count', 'priority');
            @endphp

            <div class="space-y-3">
                @foreach(['critical' => ['Critical', '#ef4444'], 'high' => ['High', '#f97316'], 'medium' => ['Medium', '#eab308'], 'low' => ['Low', '#3b82f6']] as $priority => [$label, $color])
                    @php
                        $count = $tasksByPriority[$priority] ?? 0;
                        $total = \App\Models\Task::count();
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-mono text-gray-300">{{ $label }}</span>
                            <span class="text-xs font-mono text-white">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full rounded-full" style="background-color: {{ $color }}; width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Projects by Status --}}
        <div class="grid-panel p-4 rounded">
            <h3 class="text-xs uppercase tracking-wider accent-cyan font-semibold mb-4">Projects Status</h3>

            @php
                $projectsByStatus = \App\Models\Project::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
            @endphp

            <div class="space-y-3">
                @foreach(['planning' => 'Planning', 'active' => 'Active', 'on_hold' => 'On Hold', 'completed' => 'Completed'] as $status => $label)
                    @php
                        $count = $projectsByStatus[$status] ?? 0;
                        $total = \App\Models\Project::count();
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-mono text-gray-300">{{ $label }}</span>
                            <span class="text-xs font-mono text-white">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Users by Role --}}
        <div class="grid-panel p-4 rounded">
            <h3 class="text-xs uppercase tracking-wider accent-cyan font-semibold mb-4">User Roles</h3>

            @php
                $usersByRole = \App\Models\User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role');
            @endphp

            <div class="space-y-3">
                @foreach(['admin' => 'Admins', 'project_manager' => 'Managers', 'team_member' => 'Members', 'client' => 'Clients'] as $role => $label)
                    @php
                        $count = $usersByRole[$role] ?? 0;
                        $total = \App\Models\User::count();
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-mono text-gray-300">{{ $label }}</span>
                            <span class="text-xs font-mono text-white">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-rose-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="grid-panel p-4 rounded">
        <h3 class="text-xs uppercase tracking-wider accent-cyan font-semibold mb-4">📝 Recent Activity</h3>

        @php
            $activities = \App\Models\TaskActivity::with(['task:id,title', 'user:id,name'])->latest()->limit(10)->get();
        @endphp

        @if($activities->count() > 0)
            <div class="space-y-2 max-h-72 overflow-y-auto">
                @foreach($activities as $activity)
                    <div class="px-3 py-2 border-l-2 border-cyan-500 hover:bg-[#1e1e28] transition text-xs">
                        <p class="text-gray-300">
                            <span class="font-semibold text-white">{{ $activity->user?->name ?? 'System' }}</span>
                            <span class="text-gray-500">{{ strtolower($activity->action) }}</span>
                        </p>
                        <p class="text-gray-500 mt-0.5 line-clamp-1">{{ $activity->description ?? 'Task activity' }}</p>
                        <p class="text-gray-600 mt-1 text-xs">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 text-xs">No activity yet</p>
            </div>
        @endif
    </div>
</div>
@endsection


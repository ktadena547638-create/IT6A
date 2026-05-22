<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TaskFlow - Productivity Hub')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Import monospaced font for precision metrics display -->
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --obsidian: #050509;
            --carbon: #0d0d12;
            --cyan: #0ea5e9;
            --white: #ffffff;
        }
        
        * { font-family: 'Inter', sans-serif; }
        
        /* Monospaced numbers for KPI metrics - clinical precision */
        .metrics-display { font-family: 'JetBrains Mono', monospace; font-weight: 600; }
        
        /* High-density grid system with micro-borders only */
        .grid-panel {
            background: var(--carbon);
            border: 1px solid #1e1e28;
            transition: border-color 0.2s ease;
        }
        .grid-panel:hover {
            border-color: #2d2d38;
        }
        
        /* Remove ALL shadows - obsidian aesthetic uses borders only */
        * { box-shadow: none !important; }
        
        /* Obsidian forensic scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--obsidian); }
        ::-webkit-scrollbar-thumb { background: var(--carbon); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #2d2d38; }
        
        /* Icon dock sidebar - collapsing animation */
        .sidebar-dock {
            width: 64px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-dock.expanded {
            width: 240px;
        }
        
        /* Icon hover effects */
        .icon-button {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            color: #8b8b9a;
            border-left: 2px solid transparent;
            transition: all 0.2s;
            cursor: pointer;
        }
        .icon-button:hover {
            color: var(--cyan);
            background: rgba(14, 165, 233, 0.1);
            border-left-color: var(--cyan);
        }
        .icon-button.active {
            color: var(--cyan);
            border-left-color: var(--cyan);
        }
        
        /* Label hidden by default, shown on expand */
        .icon-label {
            display: none;
            margin-left: 12px;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
        }
        .sidebar-dock.expanded .icon-label {
            display: inline;
        }
        
        /* Clinical cyan accent for active states */
        .accent-cyan { color: var(--cyan); }
        .border-cyan { border-color: var(--cyan); }
        .bg-cyan-subtle { background: rgba(14, 165, 233, 0.1); }
    </style>
</head>
<!-- OBSIDIAN FORENSIC THEME: Deep black background, clinical precision, no decorative shadows -->
<body class="antialiased" style="background-color: var(--obsidian); color: var(--white);">
    <div class="flex h-screen" style="background-color: var(--obsidian);">
        
        <!-- ULTRA-MINIMALIST ICON DOCK SIDEBAR -->
        <aside class="sidebar-dock" x-data="{ expanded: false }" @mouseenter="expanded = true" @mouseleave="expanded = false">
            <!-- Dock background -->
            <div class="h-full" style="background-color: var(--carbon); border-right: 1px solid #1e1e28;">
                
                <!-- Logo / Home Icon -->
                <div class="flex items-center justify-center h-16 border-b" style="border-color: #1e1e28;">
                    <a href="{{ route('home.index') }}" class="icon-button {{ request()->routeIs('home.index') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="icon-label">TaskFlow</span>
                    </a>
                </div>
                
                <!-- Navigation Icons -->
                <nav class="flex flex-col space-y-1 p-2">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard.index') }}" class="icon-button {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" title="Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="icon-label">Dashboard</span>
                    </a>
                    
                    <!-- Projects -->
                    <a href="{{ route('projects.index') }}" class="icon-button {{ request()->routeIs('projects.*') ? 'active' : '' }}" title="Projects">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span class="icon-label">Projects</span>
                    </a>
                    
                    <!-- Tasks -->
                    <a href="{{ route('tasks.index') }}" class="icon-button {{ request()->routeIs('tasks.*') ? 'active' : '' }}" title="Tasks">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="icon-label">Tasks</span>
                    </a>
                    
                    <!-- Reports (Admin/PM) -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isProjectManager())
                    <a href="{{ route('reports.tasks') }}" class="icon-button {{ request()->routeIs('reports.*') ? 'active' : '' }}" title="Reports">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        <span class="icon-label">Reports</span>
                    </a>
                    
                    <!-- Analytics (Admin/PM) -->
                    <a href="{{ route('analytics.index') }}" class="icon-button {{ request()->routeIs('analytics.*') ? 'active' : '' }}" title="Analytics">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="icon-label">Analytics</span>
                    </a>
                    @endif
                    
                    <!-- User Management (Admin) -->
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="icon-button {{ request()->routeIs('users.*') ? 'active' : '' }}" title="Users">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.048M12 4.354a4 4 0 110 8.048M12 4.354a4 4 0 110 8.048M3.172 15.172a8 8 0 1116 0M9 10h.01M15 10h.01"></path>
                        </svg>
                        <span class="icon-label">Users</span>
                    </a>
                    @endif
                </nav>
                
                <!-- Spacer -->
                <div class="flex-1"></div>
                
                <!-- User Profile Section -->
                <div class="border-t" style="border-color: #1e1e28; padding: 8px;">
                    <div class="icon-button group relative">
                        <div class="w-6 h-6 rounded flex items-center justify-center accent-cyan text-xs font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="icon-label">{{ auth()->user()->name }}</span>
                        
                        <!-- Dropdown menu on hover -->
                        <div class="hidden group-hover:block absolute left-16 bottom-0 bg-carbon border" style="border-color: #1e1e28; border-radius: 4px; min-width: 160px; z-index: 50;">
                            <a href="{{ route('profile.preferences') }}" class="block px-3 py-2 text-sm hover:bg-[#1e1e28] text-gray-300 transition">
                                ⚙️ Preferences
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-3 py-2 text-sm hover:bg-[#1e1e28] text-red-400 transition border-t" style="border-color: #1e1e28;">
                                    🚪 Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- TOP STATUS BAR -->
            <header style="background-color: var(--carbon); border-bottom: 1px solid #1e1e28;">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h1 class="text-lg font-medium accent-cyan">@yield('page-title', 'Dashboard')</h1>
                    
                    <!-- Right side: Notifications + Search -->
                    <div class="flex items-center gap-6">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-cyan-400 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- User menu -->
                        <div class="text-right text-sm">
                            <p class="font-medium">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- PAGE CONTENT - HIGH-DENSITY GRID -->
            <main class="flex-1 overflow-auto" style="background-color: var(--obsidian);">
                <div class="p-6">
                    <!-- Alert Messages -->
                    @if ($errors->any())
                        <div class="grid-panel p-4 mb-6 border-red-600/50">
                            <p class="text-red-400 font-medium mb-2">Errors found:</p>
                            <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="grid-panel p-4 mb-6 border-red-600/50">
                            <p class="text-red-400">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="grid-panel p-4 mb-6 border-green-600/50">
                            <p class="text-green-400">{{ session('success') }}</p>
                        </div>
                    @endif

                    <!-- Content Yield -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>


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
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --space-10: 2.5rem;
            --space-12: 3rem;
            --radius-art: 14px;
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

        /* Global content harmonizer: unify remaining legacy light UI into premium obsidian style */
        .content-theme {
            background-image:
                radial-gradient(circle at 8% 12%, rgba(14, 165, 233, 0.08), transparent 32%),
                radial-gradient(circle at 92% 8%, rgba(59, 130, 246, 0.06), transparent 28%),
                linear-gradient(180deg, rgba(5, 5, 9, 0.98), rgba(5, 5, 9, 1));
        }

        .content-theme .bg-white,
        .content-theme [class*="bg-white"] {
            background-color: #0d0d12 !important;
            border-color: #1f2632 !important;
        }

        .content-theme .rounded-lg,
        .content-theme .rounded-xl,
        .content-theme .rounded-2xl {
            border: 1px solid #1f2632;
        }

        .content-theme .text-gray-900,
        .content-theme .text-gray-800,
        .content-theme .text-slate-900,
        .content-theme .text-slate-800 {
            color: #f1f5f9 !important;
        }

        .content-theme .text-gray-700,
        .content-theme .text-gray-600,
        .content-theme .text-slate-700,
        .content-theme .text-slate-600 {
            color: #cbd5e1 !important;
        }

        .content-theme .text-gray-500,
        .content-theme .text-slate-500,
        .content-theme .text-gray-400 {
            color: #94a3b8 !important;
        }

        .content-theme .border-gray-200,
        .content-theme .border-gray-300,
        .content-theme .border-slate-200,
        .content-theme .border-slate-300 {
            border-color: #253041 !important;
        }

        .content-theme input,
        .content-theme textarea,
        .content-theme select {
            background-color: rgba(14, 165, 233, 0.08) !important;
            color: #f8fafc !important;
            border-color: rgba(14, 165, 233, 0.28) !important;
        }

        .content-theme input::placeholder,
        .content-theme textarea::placeholder {
            color: #94a3b8 !important;
        }

        .content-theme input:focus,
        .content-theme textarea:focus,
        .content-theme select:focus {
            outline: none;
            border-color: rgba(14, 165, 233, 0.7) !important;
        }

        .content-theme button[class*="bg-blue"],
        .content-theme a[class*="bg-blue"] {
            background: rgba(14, 165, 233, 0.14) !important;
            border: 1px solid rgba(14, 165, 233, 0.45) !important;
            color: #7dd3fc !important;
        }

        .content-theme button[class*="bg-red"],
        .content-theme a[class*="bg-red"] {
            background: rgba(239, 68, 68, 0.14) !important;
            border: 1px solid rgba(239, 68, 68, 0.45) !important;
            color: #fda4af !important;
        }

        .content-theme button[class*="bg-gray"],
        .content-theme a[class*="bg-gray"] {
            background: rgba(148, 163, 184, 0.08) !important;
            border: 1px solid rgba(148, 163, 184, 0.32) !important;
            color: #cbd5e1 !important;
        }

        .content-theme .shadow,
        .content-theme .shadow-md,
        .content-theme .shadow-lg,
        .content-theme .shadow-xl {
            box-shadow: 0 8px 26px rgba(2, 6, 23, 0.55) !important;
        }

        .content-theme a[class*="text-blue"],
        .content-theme .text-blue-600,
        .content-theme .text-blue-700 {
            color: #67e8f9 !important;
        }

        .content-theme a:hover,
        .content-theme button:hover {
            filter: brightness(1.08);
        }

        .content-theme .bg-green-50,
        .content-theme .bg-green-100 {
            background-color: rgba(34, 197, 94, 0.12) !important;
        }

        .content-theme .bg-red-50,
        .content-theme .bg-red-100 {
            background-color: rgba(239, 68, 68, 0.12) !important;
        }

        .content-theme .bg-orange-50,
        .content-theme .bg-orange-100 {
            background-color: rgba(249, 115, 22, 0.12) !important;
        }

        .content-theme .bg-purple-50,
        .content-theme .bg-purple-100 {
            background-color: rgba(168, 85, 247, 0.12) !important;
        }

        .content-theme .bg-blue-50,
        .content-theme .bg-blue-100,
        .content-theme .bg-slate-50,
        .content-theme .bg-slate-100 {
            background-color: rgba(14, 165, 233, 0.12) !important;
        }

        /* Artistic motion and spacing rhythm tokens */
        @keyframes titleReveal {
            from {
                opacity: 0;
                transform: translateY(8px);
                letter-spacing: 0.02em;
            }
            to {
                opacity: 1;
                transform: translateY(0);
                letter-spacing: 0;
            }
        }

        @keyframes sectionFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .art-rhythm {
            --rhythm-gap: var(--space-8);
        }

        .content-theme .mb-6 { margin-bottom: var(--space-6) !important; }
        .content-theme .mb-8 { margin-bottom: var(--space-8) !important; }
        .content-theme .mb-10 { margin-bottom: var(--space-10) !important; }

        .content-theme .rounded-lg,
        .content-theme .rounded-xl,
        .content-theme .rounded-2xl {
            border-radius: var(--radius-art) !important;
        }

        .content-theme .art-rhythm h1:first-of-type {
            animation: titleReveal 520ms cubic-bezier(0.2, 0.7, 0.2, 1) both;
        }

        .content-theme .art-rhythm h2,
        .content-theme .art-rhythm h3 {
            animation: sectionFadeIn 420ms ease both;
        }

        .content-theme .art-rhythm h1,
        .content-theme .art-rhythm h2,
        .content-theme .art-rhythm h3 {
            text-wrap: balance;
        }
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
                <div class="border-t" style="border-color: #1e1e28; padding: 8px;" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="icon-button w-full flex items-center justify-center hover:bg-cyan-500/10">
                        <div class="w-6 h-6 rounded flex items-center justify-center accent-cyan text-xs font-bold bg-cyan-500/20">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="icon-label">{{ auth()->user()->name }}</span>
                    </button>
                    
                    <!-- Dropdown menu - Enhanced visibility -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-1 bottom-20 w-56 rounded-md z-50 overflow-hidden" style="background-color: #0d0d12; border: 2px solid #0ea5e9; box-shadow: 0 15px 35px rgba(14, 165, 233, 0.15), 0 10px 15px rgba(0,0,0,0.5);">
                        
                        <!-- Menu Header -->
                        <div class="px-4 py-3 border-b" style="border-color: #1e1e28; background: rgba(14, 165, 233, 0.05);">
                            <p class="text-xs uppercase tracking-wider text-cyan-400 font-semibold">Menu</p>
                        </div>
                        
                        <!-- Preferences Link -->
                        <a href="{{ route('profile.preferences') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-cyan-500/10 hover:text-cyan-400 transition border-b" style="border-color: #1e1e28;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Preferences</span>
                        </a>
                        
                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
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
                    
                    <!-- Right side: Notifications + User Menu -->
                    <div class="flex items-center gap-6">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-cyan-400 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- User menu dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-cyan-500/10 rounded transition">
                                <div class="text-right text-sm">
                                    <p class="font-medium text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition" :class="open && 'text-cyan-400 rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>
                            <!-- Dropdown Menu - Enhanced -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 class="absolute right-0 mt-3 w-56 rounded-md z-50 overflow-hidden" style="background-color: #0d0d12; border: 2px solid #0ea5e9; box-shadow: 0 15px 35px rgba(14, 165, 233, 0.15), 0 10px 15px rgba(0,0,0,0.5);">
                                
                                <!-- Menu Header -->
                                <div class="px-4 py-3 border-b" style="border-color: #1e1e28; background: rgba(14, 165, 233, 0.05);">
                                    <p class="text-xs uppercase tracking-wider text-cyan-400 font-semibold">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1 capitalize">{{ auth()->user()->role }}</p>
                                </div>
                                
                                <!-- Preferences Link -->
                                <a href="{{ route('profile.preferences') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-cyan-500/10 hover:text-cyan-400 transition border-b" style="border-color: #1e1e28;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Preferences</span>
                                </a>
                                
                                <!-- Logout Button -->
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- PAGE CONTENT - HIGH-DENSITY GRID -->
            <main class="flex-1 overflow-auto content-theme" style="background-color: var(--obsidian);">
                <div class="p-6 art-rhythm">
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


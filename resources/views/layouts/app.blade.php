<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Task Management System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://rsms.me/inter/inter.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <div class="flex h-screen bg-slate-50">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white shadow-lg flex flex-col">
            <!-- Logo -->
            <div class="px-6 py-8 border-b border-slate-800">
                <h1 class="text-2xl font-bold text-indigo-400">TaskFlow</h1>
                <p class="text-sm text-slate-400 mt-1">Productivity Hub</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.index') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 9l4-4"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Projects -->
                <a href="{{ route('projects.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('projects.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Projects
                </a>

                <!-- Tasks -->
                <a href="{{ route('tasks.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Tasks
                </a>

                <!-- Reports (Admin/PM Only) -->
                @if(auth()->user()->isAdmin() || auth()->user()->isProjectManager())
                <a href="{{ route('reports.tasks') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Reports
                </a>

                <a href="{{ route('analytics.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('analytics.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8c0 1.657-.895 3.146-2.224 3.944m-1.78-2.89C12.225 5.414 11.627 4 10.5 4m0 0C8.895 4 7.5 5.395 7.5 7.5 7.5 9.105 8.895 10.5 10.5 10.5zm0 0a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"></path>
                    </svg>
                    Analytics
                </a>
                @endif

                <!-- Users (Admin Only) -->
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.048M12 4.354a4 4 0 110 8.048M12 4.354a4 4 0 110 8.048M3.172 15.172a8 8 0 1116 0M9 10h.01M15 10h.01"></path>
                    </svg>
                    Users
                </a>
                @endif
            </nav>

            <!-- User Profile Section -->
            <div class="border-t border-slate-800 px-4 py-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-slate-800 rounded transition">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-sm border-b border-slate-200">
                <div class="px-8 py-4 flex items-center justify-between gap-4">
                    <h2 class="text-xl font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h2>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications Bell -->
                        <div class="relative" x-data="{ open: false, unreadCount: 0, notifications: [] }"
                             @click.away="open = false"
                             x-init="
                                 fetch('/notifications/unread-count').then(r => r.json()).then(d => unreadCount = d.count);
                                 setInterval(() => {
                                     fetch('/notifications/unread-count').then(r => r.json()).then(d => unreadCount = d.count);
                                 }, 30000);
                             ">
                            <button @click="open = !open" class="relative inline-flex items-center p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition duration-200 hover:scale-110 transform">
                                <svg class="w-6 h-6 transition duration-200" :class="open && 'text-indigo-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <template x-if="unreadCount > 0">
                                    <span class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse" x-text="unreadCount"></span>
                                </template>
                            </button>

                            <!-- Notifications Dropdown -->
                            <div x-show="open" 
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto border border-slate-100"
                                 @click.stop
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 x-init="
                                     function loadNotifications() {
                                         fetch('/notifications/recent').then(r => r.json()).then(d => notifications = d);
                                     }
                                     loadNotifications();
                                 ">
                                <!-- Header -->
                                <div class="sticky top-0 bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200 px-4 py-3 flex items-center justify-between rounded-t-lg">
                                    <h3 class="font-semibold text-slate-900">Notifications</h3>
                                    <template x-if="unreadCount > 0">
                                        <button @click="fetch('/notifications/mark-all-read', {method: 'PUT', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}}).then(() => {unreadCount = 0; loadNotifications();})"
                                                class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition duration-200 hover:underline">
                                            Mark all read
                                        </button>
                                    </template>
                                </div>

                                <!-- Notifications List -->
                                <template x-if="notifications.length > 0">
                                    <div>
                                        <template x-for="notification in notifications" :key="notification.id">
                                            <div class="px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition duration-150 cursor-pointer hover:shadow-sm transform hover:-translate-y-px"
                                                 :class="!notification.read && 'bg-indigo-50'"
                                                 @click="notification.data.action_url && (window.location.href = notification.data.action_url)">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-slate-900" x-text="notification.data.message"></p>
                                                        <p class="text-xs text-slate-500 mt-1" x-text="notification.created_at"></p>
                                                        <template x-if="notification.data.type === 'new_comment'">
                                                            <p class="text-xs text-slate-600 mt-1 italic" x-text="notification.data.comment_preview"></p>
                                                        </template>
                                                    </div>
                                                    <template x-if="!notification.read">
                                                        <div class="w-2 h-2 bg-indigo-600 rounded-full flex-shrink-0 mt-1 ml-2 animate-pulse"></div>
                                                    </template>
                                                </div>
                                                <template x-if="notification.data.action_url">
                                                    <a :href="notification.data.action_url" 
                                                       @click.stop
                                                       class="mt-2 inline-block text-xs text-indigo-600 hover:text-indigo-700 font-medium transition duration-200 hover:underline">
                                                        View →
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Empty State -->
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-8 text-center">
                                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <p class="text-sm text-slate-500">No notifications yet</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-slate-100 transition">
                                <span class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-t-lg">Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Settings</a>
                                <form method="POST" action="{{ route('logout') }}" class="border-t">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-100 rounded-b-lg">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-800 font-medium mb-2">There were errors:</p>
                            <ul class="list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-800 font-medium">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>

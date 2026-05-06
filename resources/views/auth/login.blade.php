<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://rsms.me/inter/inter.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .bg-gradient-animate {
            background-size: 200% 200%;
            animation: gradient 6s ease infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-950 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Animated Background Card -->
        <div class="absolute inset-0 max-w-md mx-auto rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 blur-2xl opacity-50"></div>
        
        <!-- Main Card -->
        <div class="relative bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl p-8">
            <!-- Top Accent -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-t-2xl"></div>

            <!-- Logo Section -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl mb-4 shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">TaskFlow</h1>
                <p class="text-slate-400 text-sm mt-2">Smart Task Management Platform</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg backdrop-blur-sm">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-400 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-slate-300">Username or Email</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-3 w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        <input 
                            type="text" 
                            name="email" 
                            id="email"
                            value="{{ old('email') }}"
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-200"
                            placeholder="admin"
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-slate-300">Password</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-3 w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required 
                            class="w-full pl-10 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-200"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full mt-6 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-3 rounded-lg transition duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-indigo-500/50"
                >
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Sign In
                    </span>
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 flex items-center">
                <div class="flex-1 border-t border-slate-700"></div>
                <span class="px-3 text-xs text-slate-500">Demo Accounts</span>
                <div class="flex-1 border-t border-slate-700"></div>
            </div>

            <!-- Quick Access Buttons -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button type="button" onclick="document.getElementById('email').value='admin'; document.getElementById('password').value='password';" class="p-2 text-xs font-medium text-slate-300 bg-slate-800/50 hover:bg-slate-700/50 border border-slate-700 rounded-lg transition">
                    <span class="block font-semibold">Admin</span>
                    <span class="text-slate-500 text-xs mt-1">admin</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='sarah'; document.getElementById('password').value='password';" class="p-2 text-xs font-medium text-slate-300 bg-slate-800/50 hover:bg-slate-700/50 border border-slate-700 rounded-lg transition">
                    <span class="block font-semibold">Manager</span>
                    <span class="text-slate-500 text-xs mt-1">sarah</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='alex'; document.getElementById('password').value='password';" class="p-2 text-xs font-medium text-slate-300 bg-slate-800/50 hover:bg-slate-700/50 border border-slate-700 rounded-lg transition">
                    <span class="block font-semibold">Member</span>
                    <span class="text-slate-500 text-xs mt-1">alex</span>
                </button>
            </div>

            <!-- Test Credentials Info -->
            <div class="p-4 bg-slate-800/30 border border-slate-700 rounded-lg">
                <p class="text-xs font-semibold text-slate-300 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
                    </svg>
                    Quick Login
                </p>
                <p class="text-xs text-slate-400">Click any role above or use:</p>
                <ul class="mt-2 space-y-1 text-xs text-slate-400">
                    <li><span class="text-indigo-400">• Admin:</span> admin / password</li>
                    <li><span class="text-purple-400">• PM:</span> sarah, michael, emma</li>
                    <li><span class="text-pink-400">• Team:</span> alex, jessica, david...</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-slate-500 text-sm">
            <p>TaskFlow © 2026 • All rights reserved</p>
        </div>
    </div>
</body>
</html>

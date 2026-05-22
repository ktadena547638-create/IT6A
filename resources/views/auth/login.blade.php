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
        <div class="relative bg-slate-900/90 backdrop-blur-xl border border-slate-700 rounded-2xl shadow-2xl p-8">
            <!-- Top Accent -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-t-2xl"></div>

            <!-- Logo Section -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl mb-4 shadow-lg transform hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-300 via-purple-300 to-pink-300 bg-clip-text text-transparent">TaskFlow</h1>
                <p class="text-slate-400 text-sm mt-2 font-medium">Intelligent Task Management System</p>
            </div>

            <!-- Error Messages - Enhanced -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/15 border border-red-500/40 rounded-lg backdrop-blur-sm shadow-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            @foreach ($errors->all() as $error)
                                <p class="text-red-300 text-sm font-medium">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-bold text-slate-200 uppercase tracking-wide">Username or Email</label>
                    <div class="relative group">
                        <svg class="absolute left-4 top-4 w-5 h-5 text-slate-400 group-focus-within:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        <input 
                            type="text" 
                            name="email" 
                            id="email"
                            value="{{ old('email') }}"
                            required 
                            autofocus
                            class="w-full pl-12 pr-4 py-3 bg-slate-800/60 border border-slate-600 focus:border-indigo-500 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition duration-200 font-medium"
                            placeholder="admin"
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-bold text-slate-200 uppercase tracking-wide">Password</label>
                    <div class="relative group">
                        <svg class="absolute left-4 top-4 w-5 h-5 text-slate-400 group-focus-within:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required 
                            class="w-full pl-12 pr-4 py-3 bg-slate-800/60 border border-slate-600 focus:border-indigo-500 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition duration-200 font-medium"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <!-- Submit Button - Enhanced -->
                <button 
                    type="submit" 
                    class="w-full mt-8 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 rounded-lg transition duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-indigo-500/50 uppercase tracking-wider text-sm"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Sign In Now
                    </span>
                </button>
            </form>

            <!-- Divider -->
            <div class="my-7 flex items-center">
                <div class="flex-1 border-t border-slate-700"></div>
                <span class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Quick Demo Access</span>
                <div class="flex-1 border-t border-slate-700"></div>
            </div>

            <!-- Quick Access Buttons - Enhanced -->
            <div class="grid grid-cols-3 gap-3 mb-6">
                <button type="button" onclick="document.getElementById('email').value='admin'; document.getElementById('password').value='password'; document.getElementById('email').focus();" class="group p-3 text-xs font-bold text-slate-200 bg-gradient-to-br from-indigo-900/40 to-indigo-800/20 hover:from-indigo-900/60 hover:to-indigo-800/40 border border-indigo-700/50 hover:border-indigo-600 rounded-lg transition duration-200 transform hover:scale-105 shadow-md">
                    <span class="block font-semibold text-indigo-300">👤 Admin</span>
                    <span class="text-slate-400 text-xs mt-1.5">admin</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='sarah'; document.getElementById('password').value='password'; document.getElementById('email').focus();" class="group p-3 text-xs font-bold text-slate-200 bg-gradient-to-br from-purple-900/40 to-purple-800/20 hover:from-purple-900/60 hover:to-purple-800/40 border border-purple-700/50 hover:border-purple-600 rounded-lg transition duration-200 transform hover:scale-105 shadow-md">
                    <span class="block font-semibold text-purple-300">📊 Manager</span>
                    <span class="text-slate-400 text-xs mt-1.5">sarah</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='alex'; document.getElementById('password').value='password'; document.getElementById('email').focus();" class="group p-3 text-xs font-bold text-slate-200 bg-gradient-to-br from-pink-900/40 to-pink-800/20 hover:from-pink-900/60 hover:to-pink-800/40 border border-pink-700/50 hover:border-pink-600 rounded-lg transition duration-200 transform hover:scale-105 shadow-md">
                    <span class="block font-semibold text-pink-300">👥 Member</span>
                    <span class="text-slate-400 text-xs mt-1.5">alex</span>
                </button>
            </div>

            <!-- Test Credentials Info - Enhanced -->
            <div class="p-4 bg-slate-800/40 border border-slate-700/50 rounded-lg">
                <p class="text-xs font-bold text-slate-300 mb-3 flex items-center uppercase tracking-wider">
                    <svg class="w-4 h-4 mr-2 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
                    </svg>
                    Test Credentials
                </p>
                <ul class="space-y-2 text-xs text-slate-400 font-medium">
                    <li class="flex items-center gap-2"><span class="text-indigo-400 font-bold">•</span><span class="text-indigo-400">Admin:</span> admin</li>
                    <li class="flex items-center gap-2"><span class="text-purple-400 font-bold">•</span><span class="text-purple-400">Project Manager:</span> sarah, michael, emma</li>
                    <li class="flex items-center gap-2"><span class="text-pink-400 font-bold">•</span><span class="text-pink-400">Team Member:</span> alex, jessica, david, etc.</li>
                    <li class="flex items-center gap-2"><span class="text-slate-500 font-bold">•</span><span>Password:</span> password</li>
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


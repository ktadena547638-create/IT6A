<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://rsms.me/inter/inter.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-indigo-600">TaskFlow</h1>
                <p class="text-slate-600 text-sm mt-2">Productivity Management System</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-800 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ old('email') }}"
                        required 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent"
                        placeholder="admin@test.com"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        required 
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Submit -->
                <button 
                    type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg transition mt-8"
                >
                    Sign In
                </button>
            </form>

            <!-- Test Credentials -->
            <div class="mt-8 p-4 bg-slate-50 rounded-lg border border-slate-200">
                <p class="text-xs font-semibold text-slate-700 mb-3">🧪 Test Credentials:</p>
                <div class="space-y-2 text-xs text-slate-600">
                    <p><span class="font-medium">Admin:</span> admin@test.com / password</p>
                    <p><span class="font-medium">PM:</span> pm1@test.com / password</p>
                    <p><span class="font-medium">Team:</span> team1@test.com / password</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

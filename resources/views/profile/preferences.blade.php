@extends('layouts.app')

@section('page-title', 'Theme Preferences')

@section('content')
<div class="max-w-2xl mx-auto px-8 py-12">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">Preferences</h1>

        <!-- Theme Selection -->
        <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">🎨 Appearance</h2>
            
            <form method="POST" action="{{ route('profile.update-theme') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <fieldset class="space-y-3">
                    <legend class="text-sm font-medium text-slate-700 dark:text-slate-300">Theme Preference</legend>
                    
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition {{ auth()->user()->theme_preference === 'light' ? 'border-indigo-600 bg-indigo-50 dark:bg-slate-700' : 'border-slate-200 dark:border-slate-700' }}">
                        <input type="radio" name="theme_preference" value="light" {{ auth()->user()->theme_preference === 'light' ? 'checked' : '' }} class="w-4 h-4">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-slate-900 dark:text-white">Light Mode</span>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Bright background with dark text</p>
                        </span>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition {{ auth()->user()->theme_preference === 'dark' ? 'border-indigo-600 bg-indigo-50 dark:bg-slate-700' : 'border-slate-200 dark:border-slate-700' }}">
                        <input type="radio" name="theme_preference" value="dark" {{ auth()->user()->theme_preference === 'dark' ? 'checked' : '' }} class="w-4 h-4">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-slate-900 dark:text-white">Dark Mode</span>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Dark background with light text, reduces eye strain</p>
                        </span>
                    </label>

                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition {{ auth()->user()->theme_preference === 'system' ? 'border-indigo-600 bg-indigo-50 dark:bg-slate-700' : 'border-slate-200 dark:border-slate-700' }}">
                        <input type="radio" name="theme_preference" value="system" {{ auth()->user()->theme_preference === 'system' ? 'checked' : '' }} class="w-4 h-4">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-slate-900 dark:text-white">System Default</span>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Use your device's settings</p>
                        </span>
                    </label>
                </fieldset>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>

        <!-- Accessibility Info -->
        <div class="border-t border-slate-200 dark:border-slate-700 mt-8 pt-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">♿ Accessibility</h2>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <p class="text-sm text-blue-900 dark:text-blue-200">
                    ✅ <strong>WCAG 2.1 AA Compliance:</strong> All status indicators use icons + text, colors alone do not convey information. 
                    High contrast ratios maintained across all themes for colorblind accessibility.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection


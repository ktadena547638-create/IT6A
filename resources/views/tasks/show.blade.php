@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <style>
        .task-view-card {
            background: #0d0d12;
            border: 1px solid #1f2430;
            transition: all 180ms ease;
        }
        .task-view-card:hover {
            border-color: rgba(14, 165, 233, 0.45);
            background: #0f1118;
        }
        .task-soft {
            background: rgba(14, 165, 233, 0.08);
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
    </style>

    {{-- Flash Messages --}}
    @if ($message = Session::get('success'))
        <div class="mb-6 p-4 rounded-lg border border-green-500/30 bg-green-500/10">
            <p class="text-green-300 font-medium">✓ {{ $message }}</p>
        </div>
    @endif
    
    @if ($message = Session::get('error'))
        <div class="mb-6 p-4 rounded-lg border border-red-500/30 bg-red-500/10">
            <p class="text-red-300 font-medium">✗ {{ $message }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">
        <div class="flex-1">
            <h1 class="text-4xl font-bold text-white">{{ $task->title }}</h1>
            <p class="mt-2 text-gray-300">{{ $task->description }}</p>
        </div>
        <div class="flex gap-2">
            @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}" class="px-5 py-2.5 rounded-lg border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 transition font-medium">
                    Edit
                </a>
            @elseif($task->assigned_user_id === auth()->id())
                {{-- Soldier's Oath: Team members can update status --}}
                <a href="{{ route('tasks.edit', $task) }}" class="px-5 py-2.5 rounded-lg border border-indigo-500/40 bg-indigo-500/10 text-indigo-300 hover:bg-indigo-500/20 transition font-medium">
                    Update Status
                </a>
            @endcan
            @can('delete', $task)
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" style="display:inline" 
                      onsubmit="return confirm('Delete this task?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 rounded-lg border border-red-500/40 bg-red-500/10 text-red-300 hover:bg-red-500/20 transition font-medium">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-3 gap-8">
        {{-- Left Column: Task Details --}}
        <div class="col-span-1">
            <div class="task-view-card rounded-lg p-6 space-y-6">
                {{-- Status --}}
                <div>
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Status</p>
                    @can('update', $task)
                        <x-inline-status-switcher :task="$task" />
                    @else
                        <x-status-badge :status="$task->status" />
                    @endcan
                </div>

                {{-- Priority --}}
                <div>
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Priority</p>
                    <span class="inline-flex items-center gap-1">
                        <x-priority-icon :priority="$task->priority" />
                        <span class="text-gray-100 capitalize">{{ $task->priority }}</span>
                    </span>
                </div>

                {{-- Assigned To --}}
                <div>
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Assigned To</p>
                    <p class="text-gray-100">{{ $task->assignedUser?->name ?? 'Unassigned' }}</p>
                </div>

                {{-- Project --}}
                <div>
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Project</p>
                    @isset($task->project)
                        <a href="{{ route('projects.show', $task->project) }}" class="text-cyan-300 hover:text-cyan-200">
                            {{ $task->project->name ?? 'Untitled Project' }}
                        </a>
                    @else
                        <p class="text-gray-400">No project assigned</p>
                    @endisset
                </div>

                {{-- Dates --}}
                <div>
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Created</p>
                    <p class="text-gray-100">{{ $task->created_at?->format('M d, Y') ?? 'Unknown' }}</p>
                </div>

                @if($task->due_date)
                    <div>
                        <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Due Date</p>
                        <p class="text-gray-100 {{ $task->due_date?->isPast() && $task->status !== 'completed' ? 'text-red-400 font-bold' : '' }}">
                            {{ $task->due_date?->format('M d, Y') ?? 'Unknown' }}
                        </p>
                    </div>
                @endif

                {{-- Created By --}}
                <div class="border-t border-[#222936] pt-6">
                    <p class="text-sm text-cyan-300 font-semibold uppercase tracking-wider mb-2">Created By</p>
                    <p class="text-gray-100">{{ $task->creator?->name ?? 'Unknown User' }}</p>
                </div>
            </div>
        </div>

        {{-- Right Column: Comments & Activity --}}
        <div class="col-span-2 space-y-6">
            {{-- Tabs Navigation --}}
            <div class="task-view-card rounded-lg" x-data="{ activeTab: 'comments' }">
                <div class="flex border-b border-[#222936]">
                    <button @click="activeTab = 'comments'" 
                            :class="activeTab === 'comments' ? 'text-cyan-300 border-b-2 border-cyan-400' : 'text-gray-400 hover:text-gray-200'"
                            class="px-6 py-4 font-medium text-sm transition">
                        Comments
                    </button>
                    <button @click="activeTab = 'documents'" 
                            :class="activeTab === 'documents' ? 'text-cyan-300 border-b-2 border-cyan-400' : 'text-gray-400 hover:text-gray-200'"
                            class="px-6 py-4 font-medium text-sm transition">
                        Documents
                    </button>
                    <button @click="activeTab = 'activity'" 
                            :class="activeTab === 'activity' ? 'text-cyan-300 border-b-2 border-cyan-400' : 'text-gray-400 hover:text-gray-200'"
                            class="px-6 py-4 font-medium text-sm transition">
                        Activity
                    </button>
                </div>

                {{-- Comments Tab --}}
                <div x-show="activeTab === 'comments'" class="p-6">
                    @if($task->comments->count() > 0)
                        <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                            @foreach($task->comments as $comment)
                                <div class="flex gap-4 pb-4 border-b border-[#222936] last:border-b-0">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-cyan-500/40 flex items-center justify-center text-xs font-bold text-white">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <p class="font-medium text-white">{{ $comment->user->name }}</p>
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-300 mt-1">{{ $comment->comment }}</p>
                                        @can('delete', $comment)
                                            <form method="POST" action="{{ route('tasks.comments.destroy', [$task, $comment]) }}" style="display:inline" 
                                                  onsubmit="return confirm('Delete this comment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 mt-2">
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Comment Form --}}
                    <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="border-t border-[#222936] pt-4">
                        @csrf
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-cyan-500/40 flex items-center justify-center text-xs font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <textarea name="comment" placeholder="Add a comment..." rows="3"
                                          class="w-full px-3 py-2 rounded-lg border-2 @error('comment') border-red-500 @else border-cyan-500/30 focus:border-cyan-400 @enderror"
                                          style="background-color: rgba(14, 165, 233, 0.08); color: #f8fafc;"
                                          required></textarea>
                                @error('comment')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="mt-2 px-4 py-2 border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 rounded-lg hover:bg-cyan-500/20 transition text-sm">
                                    Comment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Documents Tab --}}
                <div x-show="activeTab === 'documents'" x-transition class="p-6">
                    {{-- File Upload Form --}}
                    @can('update', $task)
                    <div class="mb-6 p-4 border-2 border-dashed border-cyan-500/30 rounded-lg hover:border-cyan-400 transition duration-200 cursor-pointer task-soft"
                         x-data="{ dragover: false, uploading: false }"
                         @dragover.prevent="dragover = true"
                         @dragleave="dragover = false"
                         @drop.prevent="dragover = false; document.getElementById('fileInput').files = $event.dataTransfer.files; uploading = true; document.getElementById('uploadForm').submit();"
                         :class="[dragover && 'border-cyan-400 bg-cyan-500/10', uploading && 'opacity-50 pointer-events-none']">
                        <form id="uploadForm" method="POST" action="{{ route('tasks.attachments.store', $task) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="fileInput" name="file" class="hidden" onchange="document.querySelector('[x-data*=uploading]').__x.uploading = true; document.getElementById('uploadForm').submit()">
                            <label for="fileInput" class="flex flex-col items-center justify-center py-12 cursor-pointer">
                                <div x-show="!uploading" class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-cyan-300 mb-2 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <p class="text-gray-200 font-medium">Drag files here or click to upload</p>
                                    <p class="text-sm text-gray-400">Max 50MB per file</p>
                                </div>
                                <div x-show="uploading" class="flex flex-col items-center">
                                    <svg class="animate-spin h-8 w-8 text-cyan-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <p class="text-gray-200 font-medium">Uploading...</p>
                                </div>
                            </label>
                        </form>
                    </div>
                    @endcan

                    {{-- Attachments List --}}
                    @if($task->attachments->count() > 0)
                        <div class="space-y-2">
                            @foreach($task->attachments as $attachment)
                                <div class="flex items-center justify-between p-3 task-soft rounded-lg hover:bg-cyan-500/10 transition duration-200">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        @php
                                            $fileColor = str_contains($attachment->mime_type ?? '', 'pdf') ? 'text-red-400' : (str_contains($attachment->mime_type ?? '', 'image') ? 'text-blue-400' : 'text-gray-400');
                                        @endphp
                                        <svg class="w-5 h-5 {{ $fileColor }} flex-shrink-0 transition duration-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-white truncate">{{ $attachment->original_filename }}</p>
                                            <p class="text-xs text-gray-400">{{ $attachment->human_file_size }} • Uploaded {{ $attachment->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                                        <a href="{{ route('tasks.attachments.download', $attachment) }}" class="p-2 text-cyan-300 hover:bg-cyan-500/20 rounded transition duration-200" title="Download">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </a>
                                        @can('update', $task)
                                            <form method="POST" action="{{ route('tasks.attachments.destroy', [$task, $attachment]) }}" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-400 hover:bg-red-500/20 rounded transition duration-200" title="Delete" 
                                                        onclick="return confirm('Delete this file?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-400">No documents yet</p>
                        </div>
                    @endif
                </div>

                {{-- Activity Tab --}}
                <div x-show="activeTab === 'activity'" class="p-6">
                    @if($task->activities && $task->activities->count() > 0)
                        <div class="space-y-4">
                            @foreach($task->activities->sortByDesc('created_at') as $activity)
                                <div class="flex gap-4 pb-4 border-l-2 border-cyan-500/40 pl-4 last:pb-0">
                                    <div class="relative -left-6 w-4 h-4 rounded-full bg-cyan-500 border-4 border-[#0d0d12] mt-1"></div>
                                    <div class="flex-1">
                                        <p class="font-medium text-white">{{ $activity->activity_type }}</p>
                                        <p class="text-sm text-gray-300">{{ $activity->description }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $activity->user->name ?? 'System' }} - {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-400">No activity yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


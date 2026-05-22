@extends('layouts.app')

@section('page-title', 'Kanban Board - ' . $project->name)

@section('content')
<div class="p-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Kanban Board</h1>
        <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">← Back to Project</a>
    </div>

    <!-- Kanban Container -->
    <div x-data="kanbanBoard()" class="grid grid-cols-1 md:grid-cols-3 gap-6 overflow-x-auto pb-6"
         @dragover.prevent="dragOver = true"
         @dragleave="dragOver = false"
         @drop.prevent="handleDrop">

        <!-- Pending Column -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-4 min-h-96">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-4 h-4 bg-slate-400 rounded-full"></div>
                <h2 class="font-semibold text-slate-900 dark:text-white">📋 Pending</h2>
                <span class="ml-auto bg-slate-200 dark:bg-slate-700 text-xs font-medium px-2 py-1 rounded" x-text="pendingTasks.length"></span>
            </div>

            <div class="space-y-3 min-h-96 kanban-column" data-status="pending" @dragover.prevent @drop="handleTaskDrop">
                <template x-for="task in pendingTasks" :key="task.id">
                    <div draggable="true" @dragstart="draggedTask = task" @dragend="draggedTask = null"
                         class="p-4 bg-slate-50 dark:bg-slate-700 border-l-4 border-slate-400 rounded-lg cursor-move hover:shadow-md transition group">
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <h3 class="font-medium text-sm text-slate-900 dark:text-white group-hover:text-indigo-600" x-text="task.title"></h3>
                            <span class="text-xs bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 px-2 py-1 rounded" x-text="task.priority"></span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3" x-text="task.description"></p>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 dark:text-slate-400">⏱️ <span x-text="`${task.estimated_hours}h`"></span></span>
                            <span class="text-slate-500 dark:text-slate-400" x-text="`👤 ${task.assigned_user}`"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-4 min-h-96">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-4 h-4 bg-yellow-400 rounded-full"></div>
                <h2 class="font-semibold text-slate-900 dark:text-white">⚡ In Progress</h2>
                <span class="ml-auto bg-yellow-200 dark:bg-yellow-900 text-xs font-medium px-2 py-1 rounded text-yellow-900 dark:text-yellow-200" x-text="inProgressTasks.length"></span>
            </div>

            <div class="space-y-3 min-h-96 kanban-column" data-status="in_progress" @dragover.prevent @drop="handleTaskDrop">
                <template x-for="task in inProgressTasks" :key="task.id">
                    <div draggable="true" @dragstart="draggedTask = task" @dragend="draggedTask = null"
                         class="p-4 bg-yellow-50 dark:bg-slate-700 border-l-4 border-yellow-400 rounded-lg cursor-move hover:shadow-md transition group">
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <h3 class="font-medium text-sm text-slate-900 dark:text-white group-hover:text-indigo-600" x-text="task.title"></h3>
                            <span class="text-xs bg-yellow-200 dark:bg-yellow-900 text-yellow-900 dark:text-yellow-200 px-2 py-1 rounded" x-text="task.priority"></span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3" x-text="task.description"></p>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 dark:text-slate-400">⏱️ <span x-text="`${task.estimated_hours}h`"></span></span>
                            <span class="text-slate-500 dark:text-slate-400" x-text="`👤 ${task.assigned_user}`"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Completed Column -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-4 min-h-96">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                <h2 class="font-semibold text-slate-900 dark:text-white">✅ Completed</h2>
                <span class="ml-auto bg-green-200 dark:bg-green-900 text-xs font-medium px-2 py-1 rounded text-green-900 dark:text-green-200" x-text="completedTasks.length"></span>
            </div>

            <div class="space-y-3 min-h-96 kanban-column" data-status="completed" @dragover.prevent @drop="handleTaskDrop">
                <template x-for="task in completedTasks" :key="task.id">
                    <div draggable="true" @dragstart="draggedTask = task" @dragend="draggedTask = null"
                         class="p-4 bg-green-50 dark:bg-slate-700 border-l-4 border-green-500 rounded-lg cursor-move hover:shadow-md transition group opacity-75">
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <h3 class="font-medium text-sm text-slate-900 dark:text-white line-through group-hover:text-indigo-600" x-text="task.title"></h3>
                            <span class="text-xs bg-green-200 dark:bg-green-900 text-green-900 dark:text-green-200 px-2 py-1 rounded" x-text="task.priority"></span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mb-3" x-text="task.description"></p>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 dark:text-slate-400">⏱️ <span x-text="`${task.estimated_hours}h`"></span></span>
                            <span class="text-slate-500 dark:text-slate-400" x-text="`👤 ${task.assigned_user}`"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function kanbanBoard() {
    return {
        tasks: @json($tasks),
        draggedTask: null,
        dragOver: false,

        get pendingTasks() {
            return this.tasks.filter(t => t.status === 'pending');
        },
        get inProgressTasks() {
            return this.tasks.filter(t => t.status === 'in_progress');
        },
        get completedTasks() {
            return this.tasks.filter(t => t.status === 'completed');
        },

        handleTaskDrop(event) {
            if (!this.draggedTask) return;

            const column = event.target.closest('.kanban-column');
            const newStatus = column.dataset.status;

            // Find task and update status
            const taskIndex = this.tasks.findIndex(t => t.id === this.draggedTask.id);
            if (taskIndex !== -1) {
                this.tasks[taskIndex].status = newStatus;
                
                // Debounced update (300ms)
                this.updateTaskStatus(this.draggedTask.id, newStatus);
            }

            this.draggedTask = null;
        },

        updateTaskStatus(taskId, status) {
            // Debounce to prevent rapid API calls
            clearTimeout(this.updateTimeout);
            this.updateTimeout = setTimeout(() => {
                fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status })
                })
                .then(r => r.json())
                .catch(e => console.error('Update failed:', e));
            }, 300);
        }
    }
}
</script>
@endsection


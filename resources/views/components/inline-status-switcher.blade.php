@props(['task'])

<div x-data="{ open: false }" class="relative inline-block">
    <button @click="open = !open" type="button" class="text-sm px-2 py-1 rounded hover:bg-gray-100 transition">
        <x-status-badge :status="$task->status" />
    </button>

    <div x-show="open" @click.away="open = false" class="absolute top-full left-0 mt-1 w-40 bg-white border border-gray-300 rounded-lg shadow-lg z-10">
        <button type="button" @click="open = false" 
                onclick="updateTaskStatus({{ $task->id }}, 'pending')"
                class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm {{ $task->status === 'pending' ? 'bg-blue-50' : '' }}">
            Pending
        </button>
        <button type="button" @click="open = false" 
                onclick="updateTaskStatus({{ $task->id }}, 'in_progress')"
                class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm {{ $task->status === 'in_progress' ? 'bg-blue-50' : '' }}">
            In Progress
        </button>
        <button type="button" @click="open = false" 
                onclick="updateTaskStatus({{ $task->id }}, 'completed')"
                class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm {{ $task->status === 'completed' ? 'bg-blue-50' : '' }}">
            Completed
        </button>
    </div>
</div>

<script>
function updateTaskStatus(taskId, status) {
    fetch(`/tasks/${taskId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update task status');
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class AttachmentController extends Controller
{
    /**
     * Store an attachment
     */
    public function store(Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        request()->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = request()->file('file');
        
        // Create task-specific directory
        $directory = "tasks/{$task->id}";
        
        // Store file with timestamp to avoid conflicts
        $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $path = $file->storeAs($directory, $filename, 'public');

        // Create attachment record
        TaskAttachment::create([
            'task_id' => $task->id,
            'uploaded_by' => auth()->id(),
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_path' => $path,
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'File uploaded successfully.');
    }

    /**
     * Download an attachment
     */
    public function download(TaskAttachment $attachment): Response
    {
        $task = $attachment->task;
        
        // Check if user can view this task (which includes downloading its attachments)
        Gate::authorize('view', $task);

        // Verify file exists
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_filename);
    }

    /**
     * Delete an attachment
     */
    public function destroy(Task $task, TaskAttachment $attachment): RedirectResponse
    {
        Gate::authorize('update', $task);

        // Verify attachment belongs to this task
        if ($attachment->task_id !== $task->id) {
            abort(404);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        // Delete database record
        $attachment->delete();

        // Clean up empty directory
        $directory = "tasks/{$task->id}";
        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);
            if (count($files) === 0) {
                Storage::disk('public')->deleteDirectory($directory);
            }
        }

        return redirect()->route('tasks.show', $task)->with('success', 'File deleted successfully.');
    }
}

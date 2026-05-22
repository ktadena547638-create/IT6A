<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Http\Requests\StoreTaskCommentRequest;
use Illuminate\Http\RedirectResponse;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class TaskCommentController extends Controller
{
    public function __construct()
    {
        // ✅ HARDENED: Apply rate limiting to prevent comment spam
        $this->middleware('throttle:30,1');
    }

    /**
     * Store a newly created comment on a task
     * ✅ HARDENED: Rate limited, wrapped with error handling, and validates task access
     */
    public function store(Task $task, StoreTaskCommentRequest $request): RedirectResponse
    {
        try {
            // ✅ FIXED: Verify user can access the task before creating comment
            Gate::authorize('view', $task);
            Gate::authorize('create', TaskComment::class);

            $task->comments()->create([
                'user_id' => auth()->id(),
                'comment' => $request->validated()['comment'],
            ]);

            return redirect()->back()->with('success', 'Comment added successfully.');
        } catch (Exception $e) {
            Log::error('Comment creation failed', [
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to add comment. Please try again.');
        }
    }

    /**
     * Remove the specified comment
     * ✅ HARDENED: Wrapped with error handling
     */
    public function destroy(Task $task, TaskComment $comment): RedirectResponse
    {
        try {
            Gate::authorize('delete', $comment);

            $comment->delete();

            return redirect()->back()->with('success', 'Comment deleted successfully.');
        } catch (Exception $e) {
            Log::error('Comment deletion failed', [
                'comment_id' => $comment->id,
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to delete comment. Please try again.');
        }
    }
}


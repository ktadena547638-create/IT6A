<?php

namespace App\Http\Requests;

trait TaskValidationRules
{
    /**
     * Get common task validation rules
     * ✅ Shared between Store and Update requests to eliminate duplication
     */
    protected function getCommonTaskRules(): array
    {
        return [
            'project_id' => 'required|integer|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_user_id' => 'nullable|integer|exists:users,id',
            'due_date' => 'required|date_format:Y-m-d|after_or_equal:today',
        ];
    }

    /**
     * Get common error messages
     * ✅ Shared between Store and Update requests
     */
    protected function getCommonMessages(): array
    {
        return [
            'project_id.required' => 'Project is required',
            'project_id.exists' => 'Selected project does not exist',
            'project_id.integer' => 'Project ID must be a valid number',
            'title.required' => 'Task title is required',
            'title.max' => 'Task title cannot exceed 255 characters',
            'priority.in' => 'Priority must be low, medium, high, or critical',
            'status.in' => 'Status must be pending, in_progress, or completed',
            'assigned_user_id.exists' => 'Selected user does not exist in the system',
            'assigned_user_id.integer' => 'User ID must be a valid number',
            'due_date.after' => 'Due date must be today or later',
            'due_date.date' => 'Due date must be a valid date',
            'due_date.required' => 'Due date is required',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\TaskComment;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create', TaskComment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'task_id' => 'required|exists:tasks,id',
            'comment' => 'required|string|min:3|max:1000',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'task_id.required' => 'Task is required',
            'task_id.exists' => 'Selected task does not exist',
            'comment.required' => 'Comment is required',
            'comment.min' => 'Comment must be at least 3 characters',
            'comment.max' => 'Comment cannot exceed 1000 characters',
        ];
    }
}


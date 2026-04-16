<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * ✅ HARDENED: Added due_date validation mapped from end_date
     * ✅ FIXED: Use Rule::unique() to properly ignore current project on update
     */
    public function rules(): array
    {
        $project = $this->route('project');
        $projectId = $project instanceof Project ? $project->id : $project;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('projects', 'name')->ignore($projectId),
            ],
            'description' => 'nullable|string|max:1000',
            'priority' => 'sometimes|required|in:low,medium,high,critical',
            'manager_id' => 'sometimes|required|exists:users,id',
            'status' => 'sometimes|required|in:planning,active,on_hold,completed,cancelled',
            'start_date' => 'sometimes|required|date|before_or_equal:end_date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date|after_or_equal:today',
            'due_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    /**
     * Get the validated data from the request.
     * Maps end_date to due_date for the model
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Map form field to model field
        if (is_array($validated) && isset($validated['end_date'])) {
            $validated['due_date'] = $validated['end_date'];
            unset($validated['end_date']);
        }
        
        return $validated;
    }

    /**
     * Get custom error messages.
     * ✅ HARDENED: Added due_date and date validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required',
            'name.unique' => 'A project with this name already exists',
            'name.max' => 'Project name cannot exceed 255 characters',
            'manager_id.required' => 'Project manager is required',
            'manager_id.exists' => 'Selected manager does not exist',
            'status.in' => 'Status must be planning, active, on_hold, completed, or cancelled',
            'priority.in' => 'Priority must be low, medium, high, or critical',
            'start_date.before' => 'Start date must be before end date',
            'start_date.date' => 'Start date must be a valid date',
            'end_date.after' => 'End date must be after start date',
            'end_date.date' => 'End date must be a valid date',
            'end_date.after_or_equal' => 'End date must be today or later',
            'due_date.date' => 'Due date must be a valid date',
            'due_date.after_or_equal' => 'Due date must be today or later',
        ];
    }
}

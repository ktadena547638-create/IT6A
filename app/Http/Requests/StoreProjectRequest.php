<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create', Project::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:projects',
            'description' => 'nullable|string|max:1000',
            'manager_id' => 'required|exists:users,id',
            'priority' => 'nullable|in:low,medium,high,critical',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }

    /**
     * Prepare the data for validation.
     * Map form field names to model field names
     */
    protected function prepareForValidation()
    {
        // No transformation needed at validation stage
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
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required',
            'name.unique' => 'A project with this name already exists',
            'name.max' => 'Project name cannot exceed 255 characters',
            'manager_id.required' => 'Project manager is required',
            'manager_id.exists' => 'Selected manager does not exist',
            'status.in' => 'Status must be planning, active, on_hold, or completed',
            'start_date.required' => 'Start date is required',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date',
            'end_date.required' => 'End date is required',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }
}


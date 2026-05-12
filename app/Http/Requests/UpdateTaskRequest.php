<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    use TaskValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     * ✅ FIXED: FormRequest authorization deferred to controller Gate::authorize()
     * This prevents double-checking and ensures Gate::before admin bypass works correctly
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by controller's Gate::authorize('update', $task)
    }

    /**
     * Get the validation rules that apply to the request.
     * ✅ FIXED: Uses common rules from trait to eliminate duplication
     * Make all fields optional for PATCH requests (EXCEPT those that must always be present)
     */
    public function rules(): array
    {
        $rules = $this->getCommonTaskRules();
        
        // For PATCH updates, make all fields optional but validate if provided
        $optionalRules = [];
        foreach ($rules as $key => $rule) {
            $optionalRules[$key] = 'sometimes|' . $rule;
        }
        
        return $optionalRules;
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return $this->getCommonMessages();
    }

    /**
     * Normalize common due_date formats before validation for updates.
     */
    protected function prepareForValidation(): void
    {
        $due = $this->input('due_date');
        if (! $due) {
            return;
        }

        $this->merge([
            'due_date' => $this->normalizeDueDateValue((string) $due),
        ]);
    }
}

<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    use TaskValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     * ✅ FIXED: FormRequest authorization deferred to controller Gate::authorize()
     * This prevents double-checking and ensures Gate::before admin bypass works correctly
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by controller's Gate::authorize('create', Task::class)
    }

    /**
     * Get the validation rules that apply to the request.
     * ✅ Uses common rules from trait to eliminate duplication
     */
    public function rules(): array
    {
        return $this->getCommonTaskRules();
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return $this->getCommonMessages();
    }
}

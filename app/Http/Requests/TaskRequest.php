<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $statuses = implode(',', array_column(TaskStatus::cases(), 'value'));
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'title' => 'sometimes|required|string|max:255',
                'status' => 'sometimes|in:' . $statuses,
            ];
        }
        return [
            'title' => 'required|string|max:255',
            'status' => 'in:' . $statuses,
        ];
    }
}

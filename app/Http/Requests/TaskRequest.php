<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'due_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:' . now()->format('Y-m-d'),
            'priority' => 'in:1,2,3,4',
        ];
    }
}

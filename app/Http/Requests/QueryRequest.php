<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueryRequest extends FormRequest
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
            'completed_date_from' => 'required_unless:completed_date_to,null|date|date_format:Y-m-d',
            'completed_date_to' => 'date|date_format:Y-m-d|after_or_equal:completed_date_from',
            'due_date_from' => 'required_unless:due_date_to,null|date|date_format:Y-m-d',
            'due_date_to' => 'date|date_format:Y-m-d|after_or_equal:due_date_from',
            'archive_date_from' => 'required_unless:archive_date_to,null|date|date_format:Y-m-d',
            'archive_date_to' => 'date|date_format:Y-m-d|after_or_equal:archive_date_from',
            'sort_level' => 'in:asc,desc'
        ];
    }
}

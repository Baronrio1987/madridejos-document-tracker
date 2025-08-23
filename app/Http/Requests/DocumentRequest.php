<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_type_id' => 'required|exists:document_types,id',
            'origin_department_id' => 'required|exists:departments,id',
            'priority' => 'required|in:low,normal,high,urgent',
            'date_received' => 'required|date',
            'target_completion_date' => 'nullable|date|after:date_received',
            'is_confidential' => 'boolean',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Document title is required.',
            'document_type_id.required' => 'Please select a document type.',
            'document_type_id.exists' => 'Selected document type is invalid.',
            'origin_department_id.required' => 'Please select the origin department.',
            'origin_department_id.exists' => 'Selected department is invalid.',
            'priority.required' => 'Please select document priority.',
            'priority.in' => 'Invalid priority level selected.',
            'date_received.required' => 'Date received is required.',
            'date_received.date' => 'Please provide a valid date.',
            'target_completion_date.after' => 'Target completion date must be after date received.',
        ];
    }
}

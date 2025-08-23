<?php
// app/Http/Requests/UserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserRequest extends FormRequest
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
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users')->ignore($userId)
            ],
            'department_id' => ['required', 'exists:departments,id'],
            'role' => ['required', Rule::in(['admin', 'department_head', 'encoder', 'viewer'])],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [
                $userId ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'password_confirmation' => [
                'nullable',
                'string',
                'same:password'
            ],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Full name is required.',
            'name.max' => 'Full name must not exceed 255 characters.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'email.max' => 'Email address must not exceed 255 characters.',
            
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique' => 'This employee ID is already taken.',
            'employee_id.max' => 'Employee ID must not exceed 50 characters.',
            
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'Selected department is invalid.',
            
            'role.required' => 'Please select a user role.',
            'role.in' => 'Selected role is invalid.',
            
            'position.max' => 'Position must not exceed 255 characters.',
            
            'phone.max' => 'Phone number must not exceed 20 characters.',
            
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            
            'password_confirmation.same' => 'Password confirmation does not match.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'employee_id' => 'employee ID',
            'department_id' => 'department',
            'is_active' => 'active status',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation logic can be added here
            
            // Check if trying to demote the last admin
            if ($this->has('role') && $this->role !== 'admin') {
                $userId = $this->route('user') ? $this->route('user')->id : null;
                
                if ($userId) {
                    $currentUser = User::find($userId);
                    if ($currentUser && $currentUser->role === 'admin') {
                        $adminCount = User::where('role', 'admin')
                                         ->where('is_active', true)
                                         ->where('id', '!=', $userId)
                                         ->count();
                        
                        if ($adminCount === 0) {
                            $validator->errors()->add('role', 'Cannot change role - at least one admin must remain in the system.');
                        }
                    }
                }
            }
            
            // Validate phone format if provided
            if ($this->has('phone') && $this->phone) {
                $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $this->phone);
                if (strlen($phone) < 7) {
                    $validator->errors()->add('phone', 'Please provide a valid phone number.');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone') && $this->phone) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\(\)\s]/', '', $this->phone)
            ]);
        }

        // Ensure employee_id is uppercase
        if ($this->has('employee_id')) {
            $this->merge([
                'employee_id' => strtoupper($this->employee_id)
            ]);
        }

        // Convert is_active to boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $memberId = $this->route('member') ? $this->route('member')->id : null;

        $passwordRules = ['nullable', 'string', 'min:8'];
        if ($this->isMethod('POST')) {
            $passwordRules = ['required', 'string', 'min:8'];
        }

        return [
            'member_number' => ['required', 'string', 'max:50', Rule::unique('members')->ignore($memberId)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('members')->ignore($memberId)],
            'password' => $passwordRules,
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}

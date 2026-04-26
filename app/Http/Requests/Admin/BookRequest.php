<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
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
        $bookId = $this->route('book') ? $this->route('book')->id : null;

        return [
            'book_code' => ['required', 'string', 'max:50', Rule::unique('books')->ignore($bookId)],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publish_year' => ['required', 'numeric', 'digits:4', 'min:1900', 'max:' . date('Y')],
            'stock' => ['required', 'numeric', 'min:0'],
            'image' =>['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], 
        ];
    }
}

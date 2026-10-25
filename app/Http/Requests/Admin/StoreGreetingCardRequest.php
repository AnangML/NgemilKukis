<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGreetingCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageProducts() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'theme_color' => ['required', 'string', 'in:amber,indigo,rose,emerald,pink,teal,violet,orange,stone'],
            'icon' => ['nullable', 'string', 'max:50'],
            'design_style' => ['required', 'string', 'max:100'],
            'default_message' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

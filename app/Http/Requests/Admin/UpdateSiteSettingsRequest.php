<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->canManageSettings() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
            'hero_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'site_logo_file' => ['nullable', 'file', 'mimes:svg,png,jpg,jpeg,webp,gif,ico,avif', 'max:10240'],
            'remove_site_logo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'site_logo_file.file' => 'File logo yang diunggah tidak valid.',
            'site_logo_file.mimes' => 'Format file logo harus berupa SVG, PNG, JPG, JPEG, WEBP, GIF, ICO, atau AVIF.',
            'site_logo_file.max' => 'Ukuran file logo maksimal 10 MB.',
            'hero_image_file.max' => 'Ukuran hero banner maksimal 5 MB.',
        ];
    }
}

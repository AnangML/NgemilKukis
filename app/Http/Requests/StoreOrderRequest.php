<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:25'],
            'customer_email' => ['nullable', 'email', 'max:100'],
            'customer_address' => ['required', 'string', 'max:500'],
            'customer_city' => ['nullable', 'string', 'max:100'],
            'shipping_method' => ['required', 'string', 'in:instant_grab,sameday_gojek,paxel_food,regular_jne'],
            'payment_method' => ['required', 'string', 'in:xendit_qris,xendit_va,xendit_card,whatsapp_direct'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'is_gift' => ['nullable', 'boolean'],
            'gift_recipient_name' => ['nullable', 'string', 'max:100'],
            'gift_sender_name' => ['nullable', 'string', 'max:100'],
            'gift_card_template_id' => ['nullable', 'exists:greeting_card_templates,id'],
            'gift_message' => ['nullable', 'string', 'max:600'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}

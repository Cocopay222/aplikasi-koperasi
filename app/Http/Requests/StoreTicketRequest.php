<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorisasi ditangani oleh middleware di route
    }

    public function rules(): array
    {
        return [
            'aplikasi_id' => ['required', 'exists:master_aplikasis,aplikasi_id'],
            'permasalahan' => ['required', 'string', 'min:10'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'lampiran' => ['nullable', 'array'], // File tidak terbatas
            'lampiran.*' => ['file', 'mimes:jpeg,png,jpg,mp4,pdf,doc,docx,xlsx,csv,pptx,ppsx,xlsm,docm,xlsb,zip,rar', 'max:5120'], // Max 5MB per file
        ];
    }
    
    public function messages(): array
    {
        return [
            'aplikasi_id.required' => __('messages.val_aplikasi_required'),
            'aplikasi_id.exists' => __('messages.val_aplikasi_exists'),
            'permasalahan.required' => __('messages.val_permasalahan_required'),
            'permasalahan.min' => __('messages.val_permasalahan_min'),
            'lampiran.max' => __('messages.val_lampiran_max'),
            'lampiran.mimes' => __('messages.val_lampiran_mimes'),
        ];
    }
}

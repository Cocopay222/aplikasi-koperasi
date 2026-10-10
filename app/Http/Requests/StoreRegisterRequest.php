<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role'          => ['required', 'string', 'in:Pelapor'],
            'posisi'        => ['required', 'string', 'in:Manager,Sekretaris,Bendahara,Pengawas,Admin,Akuntansi,IT'],
            'nama_instansi' => ['required', 'string', 'max:255'],
            'nama'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_hp'         => ['required', 'string', 'max:20'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'posisi.required'        => 'Posisi (Role) di Koperasi wajib dipilih.',
            'posisi.in'              => 'Pilihan Posisi tidak valid.',
            'nama_instansi.required' => 'Nama Koperasi / Instansi wajib diisi.',
            'nama_instansi.max'      => 'Nama Koperasi / Instansi maksimal 255 karakter.',
            'nama.required'          => 'Nama PIC wajib diisi.',
            'nama.max'               => 'Nama PIC maksimal 255 karakter.',
            'email.required'         => 'Email wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah terdaftar di sistem.',
            'no_hp.required'         => 'No. HP / WhatsApp wajib diisi.',
            'no_hp.max'              => 'No. HP / WhatsApp maksimal 20 karakter.',
            'password.required'      => 'Kata Sandi wajib diisi.',
            'password.min'           => 'Kata Sandi minimal 8 karakter.',
            'password.confirmed'     => 'Konfirmasi Kata Sandi tidak cocok.',
        ];
    }
}

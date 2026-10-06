<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-users') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[a-z0-9._-]+$/', Rule::unique('users', 'username')],
            'role' => ['required', Rule::in(['admin', 'super_admin'])],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, titik, garis bawah, dan strip.',
            'username.unique' => 'Username sudah digunakan.',
            'role.in' => 'Pilih peran yang tersedia.',
            'password.min' => 'Kata sandi harus berisi minimal 12 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }
}

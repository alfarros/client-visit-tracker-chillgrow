<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pengguna = $this->route('user');

        return $pengguna !== null && ($this->user()?->can('update-user-profile', $pengguna) ?? false);
    }

    public function rules(): array
    {
        $pengguna = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[a-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($pengguna)],
            'role' => [Rule::prohibitedIf(fn (): bool => ! $this->user()?->can('manage-users')), 'sometimes', 'required', Rule::in(['admin', 'super_admin'])],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, titik, garis bawah, dan strip.',
            'username.unique' => 'Username sudah digunakan.',
            'role.prohibited' => 'Admin biasa tidak dapat mengubah peran pengguna.',
            'role.in' => 'Pilih peran yang tersedia.',
            'password.min' => 'Kata sandi harus berisi minimal 12 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }
}

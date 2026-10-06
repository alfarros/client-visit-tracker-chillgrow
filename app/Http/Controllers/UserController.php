<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-users');

        $penggunas = User::query()->orderBy('name')->paginate(15);

        return view('users.index', compact('penggunas'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        User::create($request->validated());

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        Gate::authorize('update-user-profile', $user);

        return view('users.edit', ['pengguna' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update-user-profile', $user);

        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        $tujuan = $request->user()->is($user)
            ? route('users.edit', $user)
            : route('users.index');

        return redirect($tujuan)->with('success', 'Profil pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}

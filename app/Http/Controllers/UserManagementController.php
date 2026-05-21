<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = (string) $request->query('role', '');

        $users = User::query()
            ->with('roles')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role !== '', fn ($query) => $query->role($role))
            ->latest()
            ->get();

        return view('users.index', [
            'users' => $users,
            'roles' => Role::pluck('name'),
            'search' => $search,
            'selectedRole' => $role,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {

        // dd(Role::pluck('name'));
        return view('users.create', [
            'roles' => Role::pluck('name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load('roles');

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::pluck('name'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User removed successfully.');
    }
}

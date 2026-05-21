@php
    $user = Auth::user() ?? null;
    $editing = isset($user);
@endphp
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700" for="name">{{ __('Name') }}</label>
        <input class="form-input" id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm font-medium text-slate-700" for="email">{{ __('Email') }}</label>
        <input class="form-input" id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm font-medium text-slate-700" for="phone">{{ __('Phone') }}</label>
        <input class="form-input" id="phone" name="phone" type="text" value="{{ old('phone', $user->phone ?? '') }}">
        @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm font-medium text-slate-700" for="department">{{ __('Department') }}</label>
        <input class="form-input" id="department" name="department" type="text" value="{{ old('department', $user->department ?? '') }}">
        @error('department') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm font-medium text-slate-700" for="role">{{ __('Role') }}</label>
        <select class="form-select" id="role" name="role" required>
            @foreach($roles as $role)
                <option value="{{ $role }}" @selected(old('role', $user->roles->first()->name ?? '') === $role)>{{ ucwords(str_replace('_', ' ', $role)) }}</option>
            @endforeach
        </select>
        @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-teal-700 focus:ring-teal-500"
                @checked(old('is_active', $user->is_active ?? true))>
            {{ __('Active account') }}
        </label>
    </div>

    <div>
        <label class="text-sm font-medium text-slate-700" for="password">{{ $editing ? __('New Password (optional)') : __('Password') }}</label>
        <input class="form-input" id="password" name="password" type="password" {{ $editing ? '' : 'required' }}>
        @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="text-sm font-medium text-slate-700" for="password_confirmation">{{ __('Confirm Password') }}</label>
        <input class="form-input" id="password_confirmation" name="password_confirmation" type="password" {{ $editing ? '' : 'required' }}>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="btn-primary">{{ $editing ? __('Update User') : __('Create User') }}</button>
    <a href="{{ route('users.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
</div>

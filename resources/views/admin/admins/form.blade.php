@extends('admin.layout')

@section('title', isset($admin) ? 'Edit Admin' : 'Add Admin')

@section('content')

<div class="flex items-center gap-3 mb-8">
    <a href="/admin/admins" class="text-sm text-ink/50 hover:text-draft">← Admin Users</a>
    <span class="text-ink/20">/</span>
    <h1 class="font-display text-2xl font-semibold text-ink">
        {{ isset($admin) ? 'Edit: '.$admin->name : 'Add Admin' }}
    </h1>
</div>

<form method="POST"
      action="{{ isset($admin) ? '/admin/admins/'.$admin->id : '/admin/admins' }}"
      class="space-y-6 max-w-2xl">
    @csrf
    @if(isset($admin)) @method('PUT') @endif

    @if($errors->any())
        <div class="bg-clay/10 border border-clay/30 text-clay text-sm px-4 py-4 rounded-sm">
            <p class="font-semibold mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-5">
        <h2 class="font-display font-semibold text-ink">Account Details</h2>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Full Name *</label>
            <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
            <p class="text-xs text-ink/40 mt-2">This is the login username.</p>
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Role *</label>
            <select name="role" class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                <option value="secondary" {{ old('role', $admin->role ?? 'secondary') === 'secondary' ? 'selected' : '' }}>
                    Staff — manage plans, inquiries, blog, projects
                </option>
                <option value="primary" {{ old('role', $admin->role ?? '') === 'primary' ? 'selected' : '' }}>
                    Primary — everything, plus admin accounts &amp; settings
                </option>
            </select>
        </div>
    </div>

    <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-5">
        <h2 class="font-display font-semibold text-ink">
            {{ isset($admin) ? 'Change Password' : 'Password' }}
        </h2>

        @if(isset($admin))
            <p class="text-sm text-ink/50">Leave both fields blank to keep the current password.</p>
        @endif

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">
                Password {{ isset($admin) ? '' : '*' }}
            </label>
            <input type="password" name="password" autocomplete="new-password"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
            <p class="text-xs text-ink/40 mt-2">Minimum 8 characters.</p>
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">
                Confirm Password {{ isset($admin) ? '' : '*' }}
            </label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>
    </div>

    <button type="submit" class="bg-ink text-paper font-medium px-8 py-3 rounded-sm hover:bg-ink/90 transition-colors">
        {{ isset($admin) ? 'Save Changes' : 'Create Admin' }}
    </button>
</form>

@endsection

@extends('admin.layout')

@section('title', isset($admin) ? 'Edit Admin' : 'Add Admin')

@section('content')

<div class="flex items-center gap-3 mb-8">
    <a href="/admin/admins" class="text-sm text-ink/70 hover:text-draft">← Admin Users</a>
    <span class="text-ink/70">/</span>
    <h1 class="title-3d font-display text-2xl font-semibold text-ink">
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
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Full Name *</label>
            <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
            <p class="text-xs text-ink/70 mt-2">This is the login username.</p>
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Role *</label>
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

    <div class="bg-draft/5 border border-draft/30 rounded-sm p-5 text-sm text-ink/80">
        @if(isset($admin))
            <p class="font-semibold text-ink mb-1">Passwords</p>
            <p>
                You can't set another admin's password. If they've forgotten it, use
                <strong>Reset password</strong> below — they'll be emailed a temporary
                password and asked to choose their own when they sign in.
            </p>
        @else
            <p class="font-semibold text-ink mb-1">How the password works</p>
            <p>
                A random temporary password is generated and emailed to this address. On their
                first sign-in they must choose their own password, and the temporary one stops
                working. Nobody — including you — ever knows their password.
            </p>
        @endif
    </div>

    <button type="submit" class="bg-ink text-paper font-medium px-8 py-3 rounded-sm hover:bg-ink/90 transition-colors">
        {{ isset($admin) ? 'Save Changes' : 'Create Admin' }}
    </button>
</form>

@if(isset($admin) && session('admin_id') !== $admin->id)
    <form method="POST" action="/admin/admins/{{ $admin->id }}/reset-password" class="max-w-2xl mt-8 pt-6 border-t border-ink/10"
          onsubmit="return confirm('Email {{ $admin->name }} a new temporary password? Their current password will stop working immediately.')">
        @csrf
        <h2 class="font-display font-semibold text-ink mb-1">Reset password</h2>
        <p class="text-sm text-ink/70 mb-4">
            Their current password stops working straight away. If they're signed in now, they'll be
            asked to set a new one on their next click.
        </p>
        <button class="border border-clay/50 text-clay text-sm font-medium px-5 py-2.5 rounded-sm hover:bg-clay hover:text-white transition-colors">
            Email a temporary password
        </button>
    </form>
@endif

@endsection

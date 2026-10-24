@extends('admin.layout')

@section('title', 'Change Password')

@section('content')

<div class="mb-8">
    <h1 class="font-display text-2xl font-semibold text-ink">
        {{ $forced ? 'Set your password' : 'Change password' }}
    </h1>
</div>

@if($forced)
    <div class="bg-clay/10 border border-clay/40 text-ink text-sm px-5 py-4 rounded-sm mb-6 max-w-xl">
        <p class="font-semibold mb-1">You're signed in with a temporary password.</p>
        <p class="text-ink/80">
            Choose your own password to continue. The temporary password you received by email
            will stop working as soon as you do.
        </p>
    </div>
@endif

<form method="POST" action="/admin/password" class="space-y-6 max-w-xl">
    @csrf @method('PUT')

    @if($errors->any())
        <div class="bg-clay/10 border border-clay/40 text-clay text-sm px-4 py-4 rounded-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-ink/15 rounded-sm p-6 space-y-5">
        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">
                {{ $forced ? 'Temporary password (from your email)' : 'Current password' }} *
            </label>
            <input type="password" name="current_password" autocomplete="current-password"
                class="w-full border border-ink/25 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">New password *</label>
            <input type="password" name="password" autocomplete="new-password"
                class="w-full border border-ink/25 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
            <p class="text-xs text-ink/70 mt-2">
                At least 10 characters, with upper and lower case letters, a number and a symbol.
                Passwords found in known data breaches are refused.
            </p>
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Confirm new password *</label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                class="w-full border border-ink/25 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>
    </div>

    <button type="submit" class="bg-ink text-paper font-medium px-8 py-3 rounded-sm hover:bg-ink/90 transition-colors">
        {{ $forced ? 'Set password and continue' : 'Change password' }}
    </button>
</form>

@endsection

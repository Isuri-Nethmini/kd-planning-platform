@extends('layouts.app')

@section('title', 'Client Sign In')

@section('content')
<div class="max-w-md mx-auto px-5 py-16">
    <p class="font-mono text-xs uppercase tracking-[0.2em] text-clay mb-2">Client Portal</p>
    <h1 class="font-display text-3xl font-semibold text-ink mb-3">Sign in</h1>
    <p class="text-ink/75 text-sm mb-8 leading-relaxed">
        Follow your home's construction progress and upload payment receipts.
        Accounts are created by KD Planning &amp; Design for confirmed clients —
        your sign-in details were sent to you by email.
    </p>

    @if(session('success'))
        <div class="bg-moss/10 border border-moss/40 text-ink text-sm px-4 py-3 rounded-sm mb-5">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-clay/10 border border-clay/40 text-ink text-sm px-4 py-3 rounded-sm mb-5">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-clay/10 border border-clay/40 text-clay text-sm px-4 py-3 rounded-sm mb-5">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/portal/login" class="bg-white border border-ink/15 rounded-sm p-6 space-y-5">
        @csrf
        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required
                class="w-full border border-ink/25 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>
        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Password</label>
            <input type="password" name="password" autocomplete="current-password" required
                class="w-full border border-ink/25 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>
        <button class="btn-3d w-full bg-ink text-paper font-medium py-3 rounded-sm hover:bg-ink/90">Sign in</button>
    </form>

    <p class="text-xs text-ink/70 mt-6">
        Not a client yet? <a href="/inquire" class="text-draft hover:underline">Send us an inquiry</a>.
        Forgotten your password? Contact us and we'll send you a new temporary one.
    </p>
</div>
@endsection

@extends('layouts.app')

{{-- Wraps every signed-in portal page: client bar, flash messages, content. --}}
@section('content')
<div class="bg-ink text-paper">
    <div class="max-w-6xl mx-auto px-5 py-3 flex flex-wrap items-center justify-between gap-3 text-sm">
        <p class="font-mono text-xs uppercase tracking-[0.18em] text-draft">Client Portal</p>
        <div class="flex items-center gap-5">
            <span class="text-paper/80">{{ auth('customer')->user()->name }}</span>
            <a href="/portal" class="hover:text-draft transition-colors">My project</a>
            <a href="/portal/password" class="hover:text-draft transition-colors">Change password</a>
            <form method="POST" action="/portal/logout">
                @csrf
                <button class="hover:text-draft transition-colors">Sign out</button>
            </form>
        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto px-5 py-10">
    @if(session('success'))
        <div class="bg-moss/10 border border-moss/40 text-ink text-sm px-4 py-3 rounded-sm mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-clay/10 border border-clay/40 text-ink text-sm px-4 py-3 rounded-sm mb-6">{{ session('error') }}</div>
    @endif

    @yield('portal')
</div>
@endsection

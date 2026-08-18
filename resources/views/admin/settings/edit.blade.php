@extends('admin.layout')

@section('title', 'Settings')

@section('content')

<div class="mb-8">
    <h1 class="font-display text-2xl font-semibold text-ink">Settings</h1>
    <p class="text-ink/50 text-sm mt-1">Contact details used across the public site.</p>
</div>

<form method="POST" action="/admin/settings" class="space-y-6 max-w-xl">
    @csrf @method('PUT')

    @if($errors->any())
        <div class="bg-clay/10 border border-clay/30 text-clay text-sm px-4 py-4 rounded-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-5">
        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">WhatsApp Number *</label>
            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}"
                placeholder="+94 71 726 1930"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
            <p class="text-xs text-ink/40 mt-2">Used by the floating chat button and the "Ask on WhatsApp" links.</p>
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Notification Email *</label>
            <input type="email" name="notification_email" value="{{ old('notification_email', $settings['notification_email']) }}"
                placeholder="kdplanning@gmail.com"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
            <p class="text-xs text-ink/40 mt-2">Every new inquiry submitted on the site is emailed here.</p>
        </div>
    </div>

    <button type="submit" class="bg-ink text-paper font-medium px-8 py-3 rounded-sm hover:bg-ink/90 transition-colors">
        Save Settings
    </button>
</form>

@endsection

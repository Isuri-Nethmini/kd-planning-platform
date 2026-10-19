@extends('admin.layout')

@section('title', 'New Client')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="/admin/clients" class="text-sm text-ink/70 hover:text-draft">← Clients</a>
    <span class="text-ink/30">/</span>
    <h1 class="title-3d font-display text-2xl font-semibold text-ink">New client</h1>
</div>

@if($inquiry)
    <div class="bg-draft/10 border border-draft/40 rounded-sm px-5 py-4 text-sm mb-6 max-w-3xl">
        Creating a client from <a href="/admin/inquiries/{{ $inquiry->id }}" class="text-draft font-medium hover:underline">{{ $inquiry->name }}'s inquiry</a>.
        Their details are filled in below — check them before saving.
    </div>
@endif

@if($errors->any())
    <div class="bg-clay/10 border border-clay/40 text-clay text-sm px-4 py-4 rounded-sm mb-6 max-w-3xl">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="/admin/clients" class="space-y-6 max-w-3xl">
    @csrf
    @if($inquiry)<input type="hidden" name="inquiry_id" value="{{ $inquiry->id }}">@endif

    <div class="bg-white border border-ink/15 rounded-sm p-6 space-y-5">
        <h2 class="font-display font-semibold text-ink">Client</h2>
        <div class="grid sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Full name *</label>
                <input type="text" name="name" value="{{ old('name', $inquiry->name ?? '') }}" required
                    class="w-full border border-ink/25 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:border-draft">
            </div>
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Email (their sign-in) *</label>
                <input type="email" name="email" value="{{ old('email', $inquiry->email ?? '') }}" required
                    class="w-full border border-ink/25 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:border-draft">
            </div>
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $inquiry->phone ?? '') }}"
                    class="w-full border border-ink/25 rounded-sm px-4 py-2.5 text-sm focus:outline-none focus:border-draft">
            </div>
        </div>
    </div>

    <div class="bg-white border border-ink/15 rounded-sm p-6 space-y-5">
        <h2 class="font-display font-semibold text-ink">Project</h2>
        @include('admin.clients._project-fields', [
            'project'      => null,
            'defaultTitle' => $inquiry?->housePlan ? $inquiry->housePlan->name . ' — ' . ($inquiry->name ?? '') : '',
            'defaultPlan'  => $inquiry?->house_plan_id,
            'defaultValue' => $inquiry?->quoted_amount,
        ])
        <p class="text-xs text-ink/70">Six standard construction stages are added automatically. You can rename them or add more afterwards.</p>
    </div>

    <div class="bg-draft/5 border border-draft/30 rounded-sm p-5 text-sm text-ink/80">
        <p class="font-semibold text-ink mb-1">How the client signs in</p>
        <p>A temporary password is generated and emailed to the client. On first sign-in they must choose their own, and the temporary one stops working.</p>
    </div>

    <button class="bg-ink text-paper font-medium px-8 py-3 rounded-sm hover:bg-ink/90 transition-colors">Create client and project</button>
</form>
@endsection

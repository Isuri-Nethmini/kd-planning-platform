@extends('admin.layout')

@section('title', $client->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="/admin/clients" class="text-sm text-ink/70 hover:text-draft">← Clients</a>
    <span class="text-ink/30">/</span>
    <h1 class="title-3d font-display text-2xl font-semibold text-ink">{{ $client->name }}</h1>
</div>

@if($errors->any())
    <div class="bg-clay/10 border border-clay/40 text-clay text-sm px-4 py-4 rounded-sm mb-6">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <section>
            <h2 class="font-display font-semibold text-ink mb-3">Projects</h2>
            @foreach($client->projects as $project)
                <a href="/admin/client-projects/{{ $project->id }}" class="block bg-white border border-ink/15 rounded-sm p-5 mb-3 hover:border-draft transition-colors">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-display font-semibold text-ink">{{ $project->title }}</p>
                            <p class="text-sm text-ink/70">{{ $project->site_location }} · {{ $project->status_label }}</p>
                        </div>
                        <p class="font-display text-xl font-bold text-draft">{{ $project->progress }}%</p>
                    </div>
                </a>
            @endforeach
        </section>

        <details class="bg-white border border-ink/15 rounded-sm p-5">
            <summary class="cursor-pointer font-display font-semibold text-ink">Add another project</summary>
            <form method="POST" action="/admin/clients/{{ $client->id }}/projects" class="mt-5 space-y-5">
                @csrf
                @include('admin.clients._project-fields', ['project' => null])
                <button class="bg-ink text-paper text-sm font-medium px-6 py-2.5 rounded-sm">Add project</button>
            </form>
        </details>
    </div>

    <div class="space-y-6">
        <form method="POST" action="/admin/clients/{{ $client->id }}" class="bg-white border border-ink/15 rounded-sm p-5 space-y-4">
            @csrf @method('PUT')
            <h2 class="font-display font-semibold text-ink">Client details</h2>
            @foreach(['name' => 'Name', 'email' => 'Email (sign-in)', 'phone' => 'Phone'] as $field => $label)
                <div>
                    <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">{{ $label }}</label>
                    <input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" value="{{ old($field, $client->$field) }}"
                        class="w-full border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                </div>
            @endforeach
            <button class="w-full bg-ink text-paper text-sm font-medium py-2.5 rounded-sm">Save</button>
        </form>

        <div class="bg-white border border-ink/15 rounded-sm p-5 text-sm space-y-2">
            <p><span class="text-ink/70">Last sign-in:</span> {{ $client->last_login_at?->format('d M Y, H:i') ?? 'Never' }}</p>
            <p><span class="text-ink/70">Password:</span> {{ $client->must_change_password ? 'Temporary — not yet changed' : 'Set by client' }}</p>
            @if($client->inquiry)
                <p><span class="text-ink/70">From inquiry:</span> <a href="/admin/inquiries/{{ $client->inquiry->id }}" class="text-draft hover:underline">#{{ $client->inquiry->id }}</a></p>
            @endif
        </div>

        <form method="POST" action="/admin/clients/{{ $client->id }}/reset-password"
              onsubmit="return confirm('Email {{ $client->name }} a new temporary password? Their current password stops working immediately.')">
            @csrf
            <button class="w-full border border-clay/50 text-clay text-sm font-medium py-2.5 rounded-sm hover:bg-clay hover:text-white transition-colors">
                Email a temporary password
            </button>
        </form>

        @if(session('admin_role') === 'primary')
            <form method="POST" action="/admin/clients/{{ $client->id }}"
                  onsubmit="return confirm('Delete {{ $client->name }}, their projects, receipts and site photos permanently?')">
                @csrf @method('DELETE')
                <button class="w-full text-xs text-clay hover:underline py-2">Delete client and all their records</button>
            </form>
        @endif
    </div>
</div>
@endsection

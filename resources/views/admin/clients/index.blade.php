@extends('admin.layout')

@section('title', 'Clients')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-2">
    <h1 class="title-3d font-display text-2xl font-semibold text-ink">Clients</h1>
    <a href="/admin/clients/create" class="bg-ink text-paper text-sm font-medium px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">+ New client</a>
</div>
<p class="text-sm text-ink/70 mb-6">
    Confirmed clients with access to the client portal. The quickest way to add one is from a
    <a href="/admin/inquiries?status=converted" class="text-draft hover:underline">converted inquiry</a>.
</p>

<form method="GET" class="flex gap-2 mb-5 max-w-md">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name or email…"
        class="flex-1 border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
    <button class="bg-ink text-paper text-sm px-4 py-2 rounded-sm">Search</button>
</form>

<div class="bg-white border border-ink/15 rounded-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 border-b border-ink/15">
            <tr>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">Client</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">Project</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">Progress</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70 hidden lg:table-cell">Last sign-in</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                @php $project = $client->projects->first(); @endphp
                <tr class="border-b border-ink/10 last:border-0 hover:bg-ink/5">
                    <td class="px-4 py-3">
                        <a href="/admin/clients/{{ $client->id }}" class="font-medium text-ink hover:text-draft">{{ $client->name }}</a>
                        <p class="font-mono text-xs text-ink/70">{{ $client->email }}</p>
                        @if($client->must_change_password)
                            <span class="font-mono text-[11px] uppercase text-clay">Not signed in yet</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-ink/80">
                        @if($project)
                            <a href="/admin/client-projects/{{ $project->id }}" class="hover:text-draft">{{ $project->title }}</a>
                            @if($client->projects_count > 1)<span class="text-ink/60"> +{{ $client->projects_count - 1 }} more</span>@endif
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($project)
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-ink/10 rounded-sm overflow-hidden"><div class="h-full bg-draft" style="width: {{ $project->progress }}%"></div></div>
                                <span class="font-mono text-xs text-ink/70">{{ $project->progress }}%</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-ink/70 hidden lg:table-cell">{{ $client->last_login_at?->format('d M Y, H:i') ?? 'Never' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-ink/70">No clients yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($clients->hasPages())<div class="mt-6">{{ $clients->links() }}</div>@endif
@endsection

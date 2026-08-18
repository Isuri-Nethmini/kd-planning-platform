@extends('admin.layout')

@section('title', 'Admin Users')

@section('content')

<div class="flex items-center justify-between mb-2">
    <h1 class="font-display text-2xl font-semibold text-ink">Admin Users</h1>
    <a href="/admin/admins/create" class="inline-flex items-center gap-2 bg-ink text-paper text-sm font-medium px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">
        + Add Admin
    </a>
</div>
<p class="text-ink/50 text-sm mb-8">
    Primary admins can manage accounts. Secondary (staff) admins can manage site content only.
</p>

<div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 border-b border-ink/10">
            <tr>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Name</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden md:table-cell">Email</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Role</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden lg:table-cell">Last Login</th>
                <th class="text-right px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
                <tr class="border-b border-ink/5 last:border-0">
                    <td class="px-4 py-4">
                        <p class="font-medium text-ink">
                            {{ $admin->name }}
                            @if(session('admin_id') === $admin->id)
                                <span class="font-mono text-[10px] text-draft ml-1">(you)</span>
                            @endif
                        </p>
                        <p class="font-mono text-xs text-ink/40 md:hidden">{{ $admin->email }}</p>
                    </td>
                    <td class="px-4 py-4 text-ink/60 hidden md:table-cell">{{ $admin->email }}</td>
                    <td class="px-4 py-4">
                        <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm
                            {{ $admin->role === 'primary' ? 'bg-clay/10 text-clay' : 'bg-ink/5 text-ink/50' }}">
                            {{ $admin->role === 'primary' ? 'Primary' : 'Staff' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 font-mono text-xs text-ink/40 hidden lg:table-cell">
                        {{ $admin->last_login_at?->format('d M Y, H:i') ?? 'Never' }}
                    </td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="/admin/admins/{{ $admin->id }}/edit" class="text-xs text-ink hover:underline">Edit</a>
                            @if(session('admin_id') !== $admin->id)
                                <form method="POST" action="/admin/admins/{{ $admin->id }}" onsubmit="return confirm('Remove this admin account?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-clay hover:underline">Remove</button>
                                </form>
                            @else
                                <span class="text-xs text-ink/25">Remove</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6 bg-draft/5 border border-draft/20 rounded-sm p-4 text-sm text-ink/70">
    <p class="font-medium text-ink mb-1">Safeguards in place</p>
    <ul class="list-disc list-inside space-y-1 text-ink/60">
        <li>You cannot delete your own account while logged in.</li>
        <li>The last remaining primary admin cannot be deleted or demoted.</li>
        <li>Only primary admins can reach this page.</li>
    </ul>
</div>

@endsection

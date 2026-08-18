<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — KD Planning</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Work+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper font-body">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-56 bg-ink text-paper flex flex-col shrink-0">
        <div class="p-5 border-b border-paper/10">
            <p class="font-display font-bold text-paper">KD <span class="text-clay">Admin</span></p>
            <p class="font-mono text-xs text-paper/40 mt-0.5">{{ session('admin_name') }}</p>
            <p class="font-mono text-[10px] text-draft/70 uppercase tracking-wider mt-0.5">
                {{ session('admin_role') === 'primary' ? 'Primary Admin' : 'Staff Admin' }}
            </p>
        </div>

        <nav class="flex-1 p-4 space-y-1 text-sm">
            <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/dashboard') ? 'bg-paper/10' : '' }}">
                <span>📊</span> Dashboard
            </a>
            <a href="/admin/plans" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/plans*') ? 'bg-paper/10' : '' }}">
                <span>🏠</span> House Plans
            </a>
            <a href="/admin/inquiries" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/inquiries*') ? 'bg-paper/10' : '' }}">
                <span>📬</span> Inquiries
                @php $newInquiries = \App\Models\Inquiry::where('status', 'new')->count(); @endphp
                @if($newInquiries > 0)
                    <span class="ml-auto font-mono text-[10px] bg-clay text-white px-1.5 py-0.5 rounded-sm">{{ $newInquiries }}</span>
                @endif
            </a>
            <a href="/admin/projects" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/projects*') ? 'bg-paper/10' : '' }}">
                <span>🏗️</span> Completed Work
            </a>
            <a href="/admin/blog" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/blog*') ? 'bg-paper/10' : '' }}">
                <span>📝</span> Blog
            </a>
            <a href="/admin/testimonials" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/testimonials*') ? 'bg-paper/10' : '' }}">
                <span>⭐</span> Testimonials
            </a>

            <div class="pt-3 mt-3 border-t border-paper/10 space-y-1">
                <a href="/admin/analytics" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/analytics*') ? 'bg-paper/10' : '' }}">
                    <span>📈</span> Analytics
                </a>
                <a href="/admin/settings" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/settings*') ? 'bg-paper/10' : '' }}">
                    <span>⚙️</span> Settings
                </a>
                @if(session('admin_role') === 'primary')
                    <a href="/admin/admins" class="flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-paper/10 transition-colors {{ request()->is('admin/admins*') ? 'bg-paper/10' : '' }}">
                        <span>👥</span> Admin Users
                    </a>
                @endif
            </div>
        </nav>

        <div class="p-4 border-t border-paper/10 space-y-2">
            <a href="/" target="_blank" class="block text-xs text-paper/40 hover:text-paper transition-colors">← View website</a>
            <form method="POST" action="/admin/logout">
                @csrf
                <button type="submit" class="text-xs text-paper/40 hover:text-clay transition-colors">Log out</button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="flex-1 overflow-auto">
        <div class="max-w-5xl mx-auto px-8 py-8">

            @if(session('success'))
                <div class="mb-6 bg-moss/10 border border-moss/30 text-moss text-sm px-4 py-3 rounded-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-clay/10 border border-clay/30 text-clay text-sm px-4 py-3 rounded-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

</div>

</body>
</html>

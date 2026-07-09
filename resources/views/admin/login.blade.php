<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — KD Planning</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink font-body min-h-screen flex items-center justify-center">
<div class="w-full max-w-sm mx-auto px-5">
    <div class="text-center mb-8">
        <p class="font-display text-2xl font-bold text-paper">KD <span class="text-clay">Admin</span></p>
        <p class="font-mono text-xs text-paper/40 mt-1 uppercase tracking-widest">Planning & Design</p>
    </div>

    <div class="bg-paper rounded-sm p-8">
        <h1 class="font-display font-semibold text-ink text-lg mb-6">Sign in</h1>

        @if($errors->any())
            <div class="bg-clay/10 border border-clay/30 text-clay text-sm px-4 py-3 rounded-sm mb-5">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/admin/login" class="space-y-4">
            @csrf
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Email</label>
                <input
                    type="email" name="email" value="{{ old('email') }}"
                    class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft"
                    placeholder="admin@example.com" autofocus
                >
            </div>
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Password</label>
                <input
                    type="password" name="password"
                    class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft"
                    placeholder="••••••••"
                >
            </div>
            <button type="submit" class="w-full bg-ink text-paper font-medium py-3 rounded-sm hover:bg-ink/90 transition-colors mt-2">
                Sign In
            </button>
        </form>
    </div>
</div>
</body>
</html>

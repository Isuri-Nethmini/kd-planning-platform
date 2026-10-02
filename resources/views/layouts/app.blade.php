<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KD Planning & Design') | House Plans &amp; Construction</title>
    <meta name="description" content="@yield('meta_description', 'Browse house plans and request a free construction quote from KD Planning & Design, Minuwangoda, Sri Lanka.')">

    {{-- Favicons, generated from the client's vector logo --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#1C2733">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Work+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper text-charcoal font-body">

    {{-- x-data drives the mobile menu. Below the md breakpoint the desktop
         nav is hidden, so without this, phone visitors had no navigation. --}}
    <header x-data="{ open: false }" @keydown.escape.window="open = false"
            class="sticky top-0 z-40 bg-paper/95 backdrop-blur border-b border-ink/10">
        <div class="max-w-6xl mx-auto px-5 flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="flex items-center" aria-label="KD Planning &amp; Design — home">
                <img src="{{ asset('media/logo.png') }}" alt="KD Planning &amp; Design"
                     class="h-10 w-auto" width="520" height="225">
            </a>

            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-ink/80">
                <a href="/" class="hover:text-draft transition-colors">Home</a>
                <a href="/plans" class="hover:text-draft transition-colors">House Plans</a>
                <a href="/completed-projects" class="hover:text-draft transition-colors">Completed Work</a>
                <a href="/about" class="hover:text-draft transition-colors">About</a>
                <a href="/blog" class="hover:text-draft transition-colors">Blog</a>
                <a href="/#testimonials" class="hover:text-draft transition-colors">Testimonials</a>
                <a href="/portal" class="hover:text-draft transition-colors">Client Login</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="/inquire" class="inline-flex items-center rounded-sm bg-ink text-paper text-sm font-medium px-4 py-2 hover:bg-ink/90 transition-colors">
                    Get an Estimate
                </a>
                <button type="button" class="md:hidden p-2 -mr-2 text-ink" @click="open = !open"
                        :aria-expanded="open.toString()" aria-controls="mobile-menu" aria-label="Menu">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        </div>

        <nav id="mobile-menu" x-show="open" x-cloak x-transition.opacity @click.outside="open = false"
             class="md:hidden border-t border-ink/10 bg-paper">
            <div class="max-w-6xl mx-auto px-5 py-3 flex flex-col text-base font-medium text-ink">
                <a href="/" class="py-2.5 border-b border-ink/10">Home</a>
                <a href="/plans" class="py-2.5 border-b border-ink/10">House Plans</a>
                <a href="/completed-projects" class="py-2.5 border-b border-ink/10">Completed Work</a>
                <a href="/about" class="py-2.5 border-b border-ink/10">About</a>
                <a href="/blog" class="py-2.5 border-b border-ink/10">Blog</a>
                <a href="/#testimonials" class="py-2.5 border-b border-ink/10" @click="open = false">Testimonials</a>
                <a href="/portal" class="py-2.5">Client Login</a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-ink text-paper/80 mt-24">
        <div class="max-w-6xl mx-auto px-5 py-12 grid gap-10 md:grid-cols-3 text-sm">
            <div>
                <img src="{{ asset('media/logo.png') }}" alt="KD Planning &amp; Design"
                     class="h-11 w-auto mb-4 brightness-0 invert opacity-90" width="520" height="225">
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-clay mb-3">We Design, We Build</p>
                <p class="text-paper/75 leading-relaxed">A design firm and construction company in Sri Lanka, specialising in architectural design and construction works.</p>
            </div>
            <div>
                <p class="uppercase tracking-wider text-xs text-draft mb-3">Navigate</p>
                <ul class="space-y-2 text-paper/75">
                    <li><a href="/plans" class="hover:text-paper">House Plans</a></li>
                    <li><a href="/completed-projects" class="hover:text-paper">Completed Work</a></li>
                    <li><a href="/blog" class="hover:text-paper">Blog</a></li>
                </ul>
            </div>
            <div>
                <p class="uppercase tracking-wider text-xs text-draft mb-3">Contact</p>
                <ul class="space-y-2 text-paper/75">
                    <li>kdplanning@gmail.com</li>
                    <li>+94 71 726 1930</li>
                    <li>Minuwangoda, Sri Lanka</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-paper/10 py-5 text-center text-xs text-paper/75">
            © {{ date('Y') }} KD Planning &amp; Design. All rights reserved.
        </div>
    </footer>

    <x-whatsapp-button />

</body>
</html>

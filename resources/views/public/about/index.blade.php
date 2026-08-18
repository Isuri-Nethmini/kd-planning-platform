@extends('layouts.app')

@section('title', 'About Us')
@section('meta_description', 'KD Planning & Design is a design firm and construction company in Sri Lanka specialising in architectural design and construction works.')

@section('content')

{{-- Content on this page is taken from the client's own "About us section"
     and "Contact page" documents, supplied August 2026. --}}

<section class="bp-grid bg-ink text-paper relative overflow-hidden">
    <div class="max-w-4xl mx-auto px-5 py-20 relative z-10 text-center">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-draft mb-4">About Us</p>
        <blockquote class="font-display text-xl md:text-2xl font-medium leading-snug text-paper">
            &ldquo;Our approach aligns with our clients' objectives, transform the architectural design into a transcending reality&rdquo;
        </blockquote>
        <p class="font-mono text-xs uppercase tracking-[0.3em] text-clay mt-8">We Design, We Build</p>
    </div>
</section>

<section class="max-w-3xl mx-auto px-5 py-16">
    <div class="article-body">
        <p>
            KD Planning &amp; Design centre is a design firm and construction company in Sri Lanka, made up of
            highly creative and talented engineers. We specialise in architectural design — house plans — and
            construction works. We have acquired extensive professional expertise designing simple, advanced,
            complex and iconic houses and buildings, regardless of size, context or scope.
        </p>
        <p>
            Each of our projects provides a new beginning and is the result of an in-depth and meticulous design
            process. Our ambition is simple: to develop the most efficient and dynamic buildings and modern home
            designs — residential, commercial or recreational, including restaurants and villas.
        </p>
        <p>
            We listen to our clients at the start of the process, understanding their needs and aspirations for
            their house design, without limiting ourselves to the brief. We aim to produce design-led solutions
            tailor made to their context — solutions that could not be expressed in words alone.
        </p>
    </div>

    <div class="grid sm:grid-cols-2 gap-6 my-12">
        <div class="bg-white border border-ink/10 rounded-sm p-6">
            <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-clay mb-2">Long Term Outlook</p>
            <p class="text-sm text-ink/70 leading-relaxed">
                We help clients manage land as an asset that increases in value over many years.
            </p>
        </div>
        <div class="bg-white border border-ink/10 rounded-sm p-6">
            <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-clay mb-2">Interdisciplinary Practice</p>
            <p class="text-sm text-ink/70 leading-relaxed">
                We combine planning, architecture, landscape architecture and interior design into a single
                profession — designing complete environments.
            </p>
        </div>
    </div>

    <div class="article-body">
        <p>
            Our construction work continues under the supervision of professionally qualified engineers and
            experienced staff attached to the company. Our service runs from design level through to construction
            completion, with continuous supervision and advice throughout.
        </p>
    </div>
</section>

{{-- Director --}}
<section class="bg-white border-y border-ink/10">
    <div class="max-w-3xl mx-auto px-5 py-14">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-clay mb-6">Leadership</p>
        <div class="sm:flex items-start gap-8">
            <div class="flex-1">
                <h2 class="font-display text-2xl font-semibold text-ink">Dhanushka C. Soysa</h2>
                <p class="text-draft font-medium mb-4">Civil Engineer &middot; Director</p>
                <ul class="space-y-1.5 text-sm text-ink/70">
                    <li>BEng (Hons) Civil Engineering with Structural</li>
                    <li>National Diploma in Engineering Sciences (Building &amp; Structural)</li>
                    <li>Construction Dip. City &amp; Guilds</li>
                    <li>AMIIESL</li>
                    <li>MIET</li>
                </ul>
            </div>
            <div class="mt-6 sm:mt-0 sm:w-56 shrink-0">
                <div class="bg-paper border border-ink/10 rounded-sm p-5">
                    <p class="font-mono text-[10px] uppercase tracking-wider text-ink/40 mb-1">Direct Line</p>
                    <a href="tel:+94717261930" class="font-display font-semibold text-ink hover:text-draft transition-colors">
                        +94 71 726 1930
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-ink text-paper">
    <div class="max-w-3xl mx-auto px-5 py-16 text-center">
        <h2 class="font-display text-2xl font-semibold mb-3">
            Call us today
        </h2>
        <p class="text-paper/70 mb-8 max-w-xl mx-auto leading-relaxed">
            See how we can start designing and building your dream home with advanced value engineering principles.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="/plans" class="inline-flex items-center rounded-sm bg-draft text-ink font-medium px-7 py-3 hover:bg-draft/90 transition-colors">
                Browse House Plans
            </a>
            <a href="/inquire" class="inline-flex items-center rounded-sm border border-paper/25 text-paper font-medium px-7 py-3 hover:bg-paper/10 transition-colors">
                Get an Estimate
            </a>
        </div>
    </div>
</section>

@endsection

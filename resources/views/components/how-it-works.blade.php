@props(['compact' => false])

{{--
    Explains the offline purchase journey to the buyer.

    The site is a catalogue and lead-capture tool, not a shop — there is no
    checkout by design. Without this section a visitor sees a price and a
    button and has no idea what actually happens next, which is exactly the
    gap that made "why request a quote if the price is shown?" a fair question.

    NOTE: step 4 describes payment and delivery in general terms. Confirm the
    exact process with the client (advance %, collection vs delivery) and
    tighten the wording once known.
--}}

@php
    $steps = [
        [
            'n'     => '01',
            'title' => 'Browse the catalogue',
            'body'  => 'Filter by bedrooms, storeys and style until you find a design that fits your land and your family. No account needed.',
        ],
        [
            'n'     => '02',
            'title' => 'Send an inquiry',
            'body'  => 'Tell us your land size, budget and timeline — through the form or straight to WhatsApp. It takes about a minute.',
        ],
        [
            'n'     => '03',
            'title' => 'We call you back',
            'body'  => 'Usually within 24 hours. We talk through the design, any changes you want, and prepare a construction estimate for your specific site.',
        ],
        [
            'n'     => '04',
            'title' => 'Confirm and build',
            'body'  => 'Once you are happy with the estimate, we finalise the paperwork and hand over the drawings — then construction can begin.',
        ],
    ];
@endphp

<section class="{{ $compact ? 'py-14' : 'py-20' }} bg-paper border-t border-ink/10">
    <div class="max-w-6xl mx-auto px-5">
        <div class="mb-10 {{ $compact ? '' : 'text-center' }}">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-clay mb-2">How It Works</p>
            <h2 class="font-display text-2xl md:text-3xl font-semibold text-ink">
                From browsing to building
            </h2>
            <p class="text-ink/60 mt-3 max-w-2xl {{ $compact ? '' : 'mx-auto' }} leading-relaxed">
                There is no online checkout — house plans are a conversation, not a shopping cart.
                Here is what happens after you get in touch.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($steps as $step)
                <div class="relative bg-white border border-ink/10 rounded-sm p-6">
                    <span class="font-mono text-[11px] tracking-widest text-draft">{{ $step['n'] }}</span>
                    <h3 class="font-display font-semibold text-ink mt-3 mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-ink/60 leading-relaxed">{{ $step['body'] }}</p>

                    {{-- Connector tick between cards, blueprint-dimension style --}}
                    @unless ($loop->last)
                        <span class="hidden lg:block absolute top-1/2 -right-3 w-6 h-px bg-clay/30" aria-hidden="true"></span>
                    @endunless
                </div>
            @endforeach
        </div>

        <div class="mt-10 flex flex-wrap gap-4 {{ $compact ? '' : 'justify-center' }}">
            <a href="/plans" class="inline-flex items-center rounded-sm bg-ink text-paper font-medium px-6 py-3 hover:bg-ink/90 transition-colors">
                Browse Plans
            </a>
            <a href="/inquire" class="inline-flex items-center rounded-sm border border-ink/20 text-ink font-medium px-6 py-3 hover:bg-ink/5 transition-colors">
                Ask a Question
            </a>
        </div>
    </div>
</section>

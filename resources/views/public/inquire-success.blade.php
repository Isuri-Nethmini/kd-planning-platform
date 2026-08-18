@extends('layouts.app')

@section('title', 'Inquiry Submitted')

@section('content')
<div class="max-w-lg mx-auto px-5 py-28 text-center">
    <div class="text-5xl mb-6">✅</div>
    <h1 class="font-display text-2xl font-semibold text-ink mb-3">Inquiry Received!</h1>
    <p class="text-ink/70 mb-8 leading-relaxed">
        Thank you for reaching out. We've received your inquiry and will get back to you within 24 hours.
    </p>

    <div class="bg-white border border-ink/10 rounded-sm p-6 text-left mb-8">
        <p class="font-mono text-[10px] uppercase tracking-wider text-clay mb-3">What happens next</p>
        <ol class="space-y-3 text-sm text-ink/70">
            <li class="flex gap-3">
                <span class="font-mono text-draft shrink-0">01</span>
                <span>We review your requirements and the plan you're interested in.</span>
            </li>
            <li class="flex gap-3">
                <span class="font-mono text-draft shrink-0">02</span>
                <span>We call or WhatsApp you — usually the same day, always within 24 hours.</span>
            </li>
            <li class="flex gap-3">
                <span class="font-mono text-draft shrink-0">03</span>
                <span>We discuss your land and any changes you'd like, then prepare a construction estimate.</span>
            </li>
        </ol>
        <p class="text-xs text-ink/40 mt-4 pt-4 border-t border-ink/5">
            In a hurry? Message us directly on WhatsApp using the button in the corner.
        </p>
    </div>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="/plans" class="inline-flex items-center justify-center rounded-sm bg-ink text-paper font-medium px-6 py-3 hover:bg-ink/90 transition-colors">
            Continue Browsing Plans
        </a>
        <a href="/" class="inline-flex items-center justify-center rounded-sm border border-ink/20 text-ink font-medium px-6 py-3 hover:bg-ink/5 transition-colors">
            Back to Home
        </a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Inquiry Submitted')

@section('content')
<div class="max-w-lg mx-auto px-5 py-28 text-center">
    <div class="text-5xl mb-6">✅</div>
    <h1 class="font-display text-2xl font-semibold text-ink mb-3">Inquiry Received!</h1>
    <p class="text-ink/70 mb-8 leading-relaxed">
        Thank you for reaching out. We've received your inquiry and will get back to you within 24 hours.
    </p>
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

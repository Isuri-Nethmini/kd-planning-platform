@extends('admin.layout')

@section('title', 'Inquiry from '.$inquiry->name)

@section('content')

<div class="flex items-center gap-3 mb-8">
    <a href="/admin/inquiries" class="text-sm text-ink/50 hover:text-draft">← Inquiries</a>
    <span class="text-ink/20">/</span>
    <h1 class="font-display text-2xl font-semibold text-ink">{{ $inquiry->name }}</h1>
    <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm {{ $inquiry->status_class }}">
        {{ $inquiry->status_label }}
    </span>
</div>

<div class="grid lg:grid-cols-3 gap-8">

    {{-- Message --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white border border-ink/10 rounded-sm p-6">
            <h2 class="font-display font-semibold text-ink mb-4">Message</h2>
            <p class="text-ink/80 leading-relaxed whitespace-pre-line">{{ $inquiry->message }}</p>
        </div>

        @if($inquiry->housePlan)
            <div class="bg-white border border-ink/10 rounded-sm p-6">
                <h2 class="font-display font-semibold text-ink mb-4">Plan of Interest</h2>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-16 rounded-sm overflow-hidden bg-ink/5 shrink-0">
                        <x-image-frame :src="$inquiry->housePlan->primaryImage?->url" :alt="$inquiry->housePlan->name" :zoom="false" note="" />
                    </div>
                    <div>
                        <p class="font-display font-semibold text-ink">{{ $inquiry->housePlan->name }}</p>
                        <p class="font-mono text-xs text-ink/40">
                            PLAN-{{ str_pad($inquiry->housePlan->id, 4, '0', STR_PAD_LEFT) }}
                            &nbsp;·&nbsp; Rs. {{ number_format($inquiry->housePlan->price) }}
                        </p>
                        <a href="/plans/{{ $inquiry->housePlan->id }}" target="_blank" class="text-xs text-draft hover:underline">View on site →</a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Contact + actions --}}
    <div class="space-y-5">
        <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-4">
            <h2 class="font-display font-semibold text-ink">Contact</h2>

            <div>
                <p class="font-mono text-[10px] uppercase tracking-wider text-ink/40 mb-1">Email</p>
                <a href="mailto:{{ $inquiry->email }}" class="text-sm text-draft hover:underline break-all">{{ $inquiry->email }}</a>
            </div>
            <div>
                <p class="font-mono text-[10px] uppercase tracking-wider text-ink/40 mb-1">Phone</p>
                <a href="tel:{{ $inquiry->phone }}" class="text-sm text-draft hover:underline">{{ $inquiry->phone }}</a>
            </div>
            <div>
                <p class="font-mono text-[10px] uppercase tracking-wider text-ink/40 mb-1">Received</p>
                <p class="text-sm text-ink">{{ $inquiry->created_at->format('d M Y, H:i') }}</p>
            </div>

            <a href="https://wa.me/{{ preg_replace('/\D/', '', $inquiry->phone) }}" target="_blank" rel="noopener"
               class="block w-full text-center border border-[#25D366] text-[#25D366] text-sm font-medium py-2.5 rounded-sm hover:bg-[#25D366] hover:text-white transition-colors">
                Reply on WhatsApp
            </a>
        </div>

        <div class="bg-white border border-ink/10 rounded-sm p-6">
            <h2 class="font-display font-semibold text-ink mb-1">Sales Pipeline</h2>
            <p class="text-xs text-ink/40 mb-4">Track this lead from first contact through to a completed sale.</p>

            <form method="POST" action="/admin/inquiries/{{ $inquiry->id }}/status" class="space-y-4">
                @csrf @method('PATCH')

                <div>
                    <label class="font-mono text-[10px] uppercase tracking-wider text-ink/40 block mb-1">Stage</label>
                    <select name="status" class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                        @foreach(\App\Models\Inquiry::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $inquiry->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-mono text-[10px] uppercase tracking-wider text-ink/40 block mb-1">Construction Estimate (Rs.)</label>
                    <input type="number" step="0.01" min="0" name="quoted_amount"
                        value="{{ old('quoted_amount', $inquiry->quoted_amount) }}"
                        placeholder="e.g. 8500000"
                        class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                    <p class="text-xs text-ink/40 mt-1">The figure you quoted this client for the build.</p>
                </div>

                <div>
                    <label class="font-mono text-[10px] uppercase tracking-wider text-ink/40 block mb-1">Follow-up Notes</label>
                    <textarea name="admin_notes" rows="5"
                        placeholder="What was discussed, agreed price, land details, next call date…"
                        class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft resize-none">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
                    <p class="text-xs text-ink/40 mt-1">Internal only — never shown to the client.</p>
                </div>

                <button class="w-full bg-ink text-paper text-sm font-medium py-2.5 rounded-sm hover:bg-ink/90 transition-colors">
                    Save
                </button>
            </form>

            @if($inquiry->responded_at)
                <p class="font-mono text-[10px] uppercase tracking-wider text-ink/30 mt-4 pt-4 border-t border-ink/5">
                    First responded {{ $inquiry->responded_at->diffForHumans() }}
                </p>
            @endif
        </div>

        <form method="POST" action="/admin/inquiries/{{ $inquiry->id }}" onsubmit="return confirm('Delete this inquiry permanently?')">
            @csrf @method('DELETE')
            <button class="w-full border border-clay/30 text-clay text-sm py-2.5 rounded-sm hover:bg-clay hover:text-white transition-colors">
                Delete Inquiry
            </button>
        </form>
    </div>
</div>

@endsection

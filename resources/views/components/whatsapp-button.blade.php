@php
    $whatsapp = \App\Models\SystemSetting::get('whatsapp_number', '+94717261930');
    $digits   = preg_replace('/\D/', '', $whatsapp);
    $message  = urlencode('Hi, I found your website and would like to ask about a house plan.');
@endphp

<a
    href="https://wa.me/{{ $digits }}?text={{ $message }}"
    target="_blank"
    rel="noopener"
    class="fixed bottom-5 right-5 z-50 inline-flex items-center justify-center w-14 h-14 rounded-full bg-[#25D366] shadow-lg shadow-black/20 hover:scale-105 transition-transform"
    aria-label="Chat with us on WhatsApp"
>
    {{-- Official WhatsApp glyph (handset inside the speech bubble) --}}
    <svg viewBox="0 0 32 32" width="30" height="30" fill="#FFFFFF" aria-hidden="true">
        <path d="M16.004 0h-.008C7.174 0 .002 7.174.002 16c0 3.5 1.128 6.744 3.046 9.376L1.05 31.28l6.104-1.952A15.9 15.9 0 0 0 16.004 32C24.83 32 32 24.826 32 16S24.83 0 16.004 0Zm9.31 22.594c-.386 1.09-1.918 1.994-3.14 2.258-.836.178-1.928.32-5.604-1.204-4.702-1.948-7.73-6.726-7.966-7.036-.226-.31-1.9-2.53-1.9-4.826 0-2.296 1.166-3.424 1.636-3.904.386-.394.102-.634 1.362-.634.288 0 .546.014.778.026.47.02.706.048 1.016.79.386.93 1.326 3.226 1.438 3.462.114.236.228.556.068.866-.15.32-.282.462-.518.734-.236.272-.46.48-.696.772-.216.254-.46.526-.188.996.272.46 1.212 1.994 2.594 3.226 1.784 1.588 3.23 2.094 3.748 2.31.386.16.846.122 1.128-.178.358-.386.8-1.026 1.25-1.656.32-.452.724-.508 1.148-.348.432.15 2.718 1.28 3.188 1.514.47.236.78.348.894.546.112.198.112 1.132-.274 2.222Z"/>
    </svg>
</a>

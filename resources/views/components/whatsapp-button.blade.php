@php
    $whatsapp = \App\Models\SystemSetting::get('whatsapp_number', '+94717261930');
    $digits = preg_replace('/\D/', '', $whatsapp);
    $message = urlencode('Hi, I found your website and would like to ask about a house plan.');
@endphp

<a
    href="https://wa.me/{{ $digits }}?text={{ $message }}"
    target="_blank"
    rel="noopener"
    class="fixed bottom-5 right-5 z-50 inline-flex items-center justify-center w-14 h-14 rounded-full bg-[#25D366] text-white shadow-lg shadow-black/20 hover:scale-105 transition-transform"
    aria-label="Chat with us on WhatsApp"
>
    <svg viewBox="0 0 24 24" width="26" height="26" fill="white" aria-hidden="true">
        <path d="M12 3C7.03 3 3 6.66 3 11.1c0 2.42 1.18 4.6 3.06 6.1L5 21l4.27-1.4A10.6 10.6 0 0 0 12 19.2c4.97 0 9-3.66 9-8.1S16.97 3 12 3Z"/>
    </svg>
</a>

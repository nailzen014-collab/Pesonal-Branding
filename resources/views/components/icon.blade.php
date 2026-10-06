@props(['name' => 'sparkles'])

{{--
    Ikon SVG inline (Heroicons outline) agar tidak perlu font icon eksternal
    (loading lebih cepat dan warna mengikuti tema).
--}}
@php
    $icons = [
        'sparkles' => '<path d="M12 3l1.9 4.6L18.5 9.5 13.9 11.4 12 16l-1.9-4.6L5.5 9.5l4.6-1.9L12 3z"/><path d="M18 15l.9 2.1L21 18l-2.1.9L18 21l-.9-2.1L15 18l2.1-.9L18 15z"/>',
        'code' => '<path d="M16 18l6-6-6-6"/><path d="M8 6l-6 6 6 6"/>',
        'layers' => '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5"/>',
        'database' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
        'tools' => '<path d="M14.7 6.3a4 4 0 0 0 5 5l-9 9a2.8 2.8 0 0 1-4-4l9-9z"/><path d="M14.7 6.3l3-3 3 3-3 3"/>',
        'rocket' => '<path d="M12 3c3.5 1.5 6 5 6 9l-3 3H9l-3-3c0-4 2.5-7.5 6-9z"/><circle cx="12" cy="10" r="2"/><path d="M9 15l-2 6 5-3 5 3-2-6"/>',
        'github' => '<path d="M9 19c-4 1.3-4-2.2-6-2.8m12 5.3v-3.4c0-1 .1-1.4-.5-2 2.3-.3 4.5-1.2 4.5-5a3.9 3.9 0 0 0-1.1-2.7 3.6 3.6 0 0 0-.1-2.7s-.9-.3-3 1.1a10.3 10.3 0 0 0-5.4 0C7.4 4 6.5 4.3 6.5 4.3a3.6 3.6 0 0 0-.1 2.7A3.9 3.9 0 0 0 5.3 9.7c0 3.8 2.2 4.7 4.5 5-.6.6-.6 1.2-.5 2v3.6"/>',
        'star' => '<path d="M12 3l2.8 5.7 6.2.9-4.5 4.4 1 6.2-5.5-2.9-5.5 2.9 1-6.2L3 9.6l6.2-.9L12 3z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'phone' => '<path d="M22 16.9v2.1a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 3.3 2 2 0 0 1 4.1 1h2.1a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L7.2 8.8a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2.1z"/>',
        'whatsapp' => '<path d="M20.5 3.5A10 10 0 0 0 3.9 16.1L3 21l5-1a10 10 0 1 0 12.5-16.5z"/><path d="M8.6 7.6c.2-.5.4-.5.6-.5h.5c.2 0 .4 0 .6.5l.7 1.7c.1.2 0 .4-.1.6l-.4.5c-.1.2-.2.4 0 .6a8 8 0 0 0 3.5 3c.3.1.5 0 .6-.1l.6-.7c.2-.2.3-.3.6-.2l1.6.8c.3.1.4.3.4.5a2 2 0 0 1-1.4 1.8 3.6 3.6 0 0 1-2.7-.3 11 11 0 0 1-4.7-4.6 3 3 0 0 1-.3-2.3c.2-.7.6-1.3 1.1-1.9z"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
        'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10.5V17M8 7.5v.01M12 17v-3.5a2 2 0 0 1 4 0V17"/>',
        'link' => '<path d="M10 13a5 5 0 0 0 7.5.5l3-3A5 5 0 0 0 13.5 3.5L11.8 5.2"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3A5 5 0 0 0 10.5 20.5l1.7-1.7"/>',
        'external' => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/>',
        'download' => '<path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M5 21h14"/>',
        'arrow-right' => '<path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>',
        'check' => '<path d="M4 12.5l5 5L20 6.5"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
        'award' => '<circle cx="12" cy="9" r="5"/><path d="M8.5 13.5L7 22l5-2.5L17 22l-1.5-8.5"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/>',
        'school' => '<path d="M12 4L2 9l10 5 10-5-10-5z"/><path d="M6 11.5V16c0 1.7 2.7 3 6 3s6-1.3 6-3v-4.5"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 5.2a3.5 3.5 0 0 1 0 6.6"/><path d="M17.5 14.2A6.5 6.5 0 0 1 21.5 20"/>',
        'folder' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2.5h8a2 2 0 0 1 2 2V17a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/>',
        'cog' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M19.1 4.9L17 7M7 17l-2.1 2.1"/>',
        'inbox' => '<path d="M3 12h5l2 3h4l2-3h5"/><path d="M4.5 5h15L21 12v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5L4.5 5z"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/>',
        'refresh' => '<path d="M20 11a8 8 0 1 0-2.3 6.3"/><path d="M20 4v6h-6"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'trash' => '<path d="M4 7h16"/><path d="M9 7V5h6v2"/><path d="M6 7l1 13h10l1-13"/>',
        'edit' => '<path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16v4z"/>',
        'eye' => '<path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6z"/><circle cx="12" cy="12" r="2.5"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'close' => '<path d="M6 6l12 12M18 6L6 18"/>',
        'chevron-down' => '<path d="M6 9l6 6 6-6"/>',
        'shield' => '<path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
    ];

    $path = $icons[$name] ?? $icons['sparkles'];
@endphp

<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
    {!! $path !!}
</svg>
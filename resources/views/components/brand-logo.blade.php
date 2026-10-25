@props([
    'logoUrl' => null,
    'alt' => 'NgemilKukis Logo',
])

@if(!empty($logoUrl))
    <img src="{{ $logoUrl }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'object-contain']) }}>
@else
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => 'shrink-0']) }}>
        <defs>
            <radialGradient id="cookieGradient" cx="38%" cy="32%" r="65%">
                <stop offset="0%" stop-color="#e8b88a"/>
                <stop offset="60%" stop-color="#b87d4b"/>
                <stop offset="100%" stop-color="#8a5327"/>
            </radialGradient>
            <linearGradient id="chocolateGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#4a2818"/>
                <stop offset="100%" stop-color="#231006"/>
            </linearGradient>
            <filter id="cookieShadow" x="-10%" y="-10%" width="120%" height="120%">
                <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="#2d180c" flood-opacity="0.18"/>
            </filter>
        </defs>
        
        <!-- Organic Artisanal Cookie Shape -->
        <path d="M50 8C68 7 88 18 92 38C95 52 89 69 77 82C64 94 43 95 27 88C12 81 5 65 8 48C10 30 30 9 50 8Z" 
              fill="url(#cookieGradient)" 
              filter="url(#cookieShadow)"/>
              
        <!-- Golden Baked Texture Highlights -->
        <circle cx="28" cy="26" r="1.5" fill="#fdf4ec" opacity="0.6"/>
        <circle cx="68" cy="74" r="1.5" fill="#fdf4ec" opacity="0.6"/>
        <circle cx="48" cy="85" r="1.2" fill="#6d4630" opacity="0.3"/>
        <circle cx="16" cy="54" r="1.2" fill="#6d4630" opacity="0.3"/>

        <!-- Rich Belgian Chocolate Chunks -->
        <path d="M30 25C34 23 40 26 39 31C38 35 32 37 28 35C24 33 26 27 30 25Z" fill="url(#chocolateGradient)"/>
        <path d="M48 42C56 39 63 45 61 53C59 60 50 63 44 58C39 53 41 45 48 42Z" fill="url(#chocolateGradient)"/>
        <path d="M66 22C72 20 77 24 76 29C74 34 68 36 64 33C60 30 62 23 66 22Z" fill="url(#chocolateGradient)"/>
        <path d="M26 60C32 58 37 62 36 68C34 73 28 75 24 72C20 68 22 62 26 60Z" fill="url(#chocolateGradient)"/>
        <path d="M64 64C70 62 76 66 74 72C72 77 66 79 62 76C58 72 60 66 64 64Z" fill="url(#chocolateGradient)"/>
    </svg>
@endif

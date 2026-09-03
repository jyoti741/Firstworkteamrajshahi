@props(['class' => 'w-4 h-4'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="nagadPicGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#F97316" />
            <stop offset="60%" stop-color="#EA580C" />
            <stop offset="100%" stop-color="#DC2626" />
        </linearGradient>
    </defs>
    <!-- Orange/Red Circular Badge matching the reference picture -->
    <circle cx="16" cy="16" r="16" fill="url(#nagadPicGrad)" />
    <!-- White Flame Swirl in Center -->
    <path d="M16 6.5C16 6.5 12.5 11 12.5 14.5C12.5 16.5 14 18 16 18C17.8 18 19 16.5 19 14.5C19 12.5 18 10.5 18 10.5C18 10.5 22 13 22 17C22 20.8 19 23.5 15.5 23.5C11.5 23.5 8.5 20 8.5 15.5C8.5 10 13.5 6 16 6.5Z" fill="#FFFFFF" />
    <circle cx="15.5" cy="15.5" r="2.2" fill="#EA580C" />
</svg>

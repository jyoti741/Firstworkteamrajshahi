@props(['class' => 'w-4 h-4'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
    <!-- Pink / Magenta Rounded Badge matching the reference picture -->
    <rect width="32" height="32" rx="7" fill="#E2136E" />
    <!-- Crisp White bKash Origami Bird -->
    <path d="M16 6.5L8.5 15.5L14.5 17L16 6.5Z" fill="#FFFFFF" />
    <path d="M16 6.5L25 14.5L18.5 16.2L16 6.5Z" fill="#FFFFFF" fill-opacity="0.92" />
    <path d="M8.5 15.5L6 21.5L14.5 17L8.5 15.5Z" fill="#FFFFFF" fill-opacity="0.85" />
    <path d="M14.5 17L12 26.5L18.5 20.5L14.5 17Z" fill="#FFFFFF" />
    <path d="M18.5 16.2L18.5 20.5L27 20L18.5 16.2Z" fill="#FFFFFF" fill-opacity="0.88" />
    <path d="M18.5 20.5L17.5 26L22.5 23L18.5 20.5Z" fill="#FFFFFF" fill-opacity="0.92" />
</svg>

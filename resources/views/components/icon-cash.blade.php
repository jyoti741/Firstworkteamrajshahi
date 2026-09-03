@props(['class' => 'w-4 h-4'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
    <!-- Green Banknote matching the reference picture -->
    <rect width="32" height="20" rx="4" fill="#22C55E" />
    <rect x="2" y="2" width="28" height="16" rx="2.5" stroke="#FFFFFF" stroke-opacity="0.5" stroke-width="1.2" />
    <circle cx="16" cy="10" r="4" fill="#FFFFFF" />
    <circle cx="16" cy="10" r="2.2" fill="#16A34A" />
    <circle cx="6" cy="10" r="1.2" fill="#FFFFFF" fill-opacity="0.8" />
    <circle cx="26" cy="10" r="1.2" fill="#FFFFFF" fill-opacity="0.8" />
</svg>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
<meta name="theme-color" content="#09090b" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="apple-mobile-web-app-title" content="CartFlow" />

<title>
    {{ filled($title ?? null) ? $title.' - '.(\App\Models\CartSetting::cartName()) : (\App\Models\CartSetting::cartName()) }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/manifest.json">

<!-- Google Fonts: Noto Sans Bengali & Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Mobile-first touch & ergonomic styling */
    html {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .touch-press {
        user-select: none;
        -webkit-tap-highlight-color: transparent;
        transition: transform 0.08s ease, filter 0.08s ease;
    }
    .touch-press:active {
        transform: scale(0.95);
        filter: brightness(0.92);
    }
    /* Safe area padding for mobile notches */
    .safe-bottom {
        padding-bottom: env(safe-area-inset-bottom, 1rem);
    }
    /* Custom thin scrollbar for smartphone preview */
    .mobile-screen-scroll::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    .mobile-screen-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 4px;
    }
</style>

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

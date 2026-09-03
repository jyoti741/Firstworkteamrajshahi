@php
    $isAuthRoute = request()->is('login*') 
        || request()->is('register*') 
        || request()->is('forgot-password*') 
        || request()->is('reset-password*') 
        || request()->is('two-factor*') 
        || request()->is('verify-email*') 
        || request()->is('confirm-password*');
@endphp

<meta charset="utf-8" />
<meta name="viewport"
    content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
<meta name="theme-color" content="{{ $isAuthRoute ? '#09090b' : '#FBF7F0' }}" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="apple-mobile-web-app-title" content="CartFlow" />

<title>
    {{ filled($title ?? null) ? $title . ' - ' . (\App\Models\CartSetting::cartName()) : (\App\Models\CartSetting::cartName()) }}
</title>

<!-- Favicons & App Icons -->
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=2">
<link rel="manifest" href="{{ asset('manifest.json') }}?v=2">

<!-- Google Fonts: Noto Sans Bengali & Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function (err) {
                console.log('SW registration error:', err);
            });
        });
    }
</script>

<script>
    (function () {
        @if($isAuthRoute)
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('bright');
            try { localStorage.setItem('flux.appearance', 'dark'); } catch (e) {}
            return;
        @endif

        var theme = localStorage.getItem('cartflow_theme') || localStorage.getItem('cartflow_admin_theme') || 'bright';
        try { localStorage.setItem('flux.appearance', theme === 'dark' ? 'dark' : 'light'); } catch (e) {}
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('bright');
        } else {
            document.documentElement.classList.add('bright');
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

<style>
    /* ==========================================================================
       BRIGHT / CREAM THEME (Default)
       ========================================================================== */
    :root,
    html.bright {
        --color-cream-canvas: #FBF7F0;
        --color-cream-surface: #FFFFFF;
        --color-cream-tile: #F8F3EA;
        --color-cream-border: #EFE7DE;
        --color-espresso-primary: #2B1E16;
        --color-espresso-body: #554338;
        --color-espresso-muted: #8D7B70;
        --color-brand-orange: #F26522;
        --color-brand-orange-hover: #E05310;
        --color-mint-bg: #EAF7EE;
        --color-mint-text: #1E8E3E;
        --color-mint-border: #CDEED5;
    }

    body {
        background-color: #FBF7F0;
        color: #2B1E16;
        font-family: 'Plus Jakarta Sans', 'Noto Sans Bengali', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Direct Theme Classes (Bright) */
    .bg-canvas {
        background-color: #FBF7F0 !important;
    }

    .bg-card {
        background-color: #FFFFFF !important;
    }

    .bg-tile {
        background-color: #F8F3EA !important;
    }

    .border-theme {
        border-color: #EFE7DE !important;
    }

    .text-espresso {
        color: #2B1E16 !important;
    }

    .text-espresso-muted {
        color: #8D7B70 !important;
    }

    .text-orange-main {
        color: #F26522 !important;
    }

    .bg-orange-main {
        background-color: #F26522 !important;
    }

    /* Tailwind arbitrary classes mapped for Bright Theme */
    .bg-\[\#FBF7F0\],
    .bg-\[\#FAF5EF\] {
        background-color: #FBF7F0 !important;
    }

    .bg-\[\#F8F3EA\],
    .bg-\[\#FAF6F0\] {
        background-color: #F8F3EA !important;
    }

    .bg-\[\#EFE7DE\],
    .bg-\[\#F0E6DD\] {
        background-color: #EFE7DE !important;
    }

    .bg-\[\#F26522\],
    .bg-\[\#FF7700\] {
        background-color: #F26522 !important;
    }

    .bg-\[\#FFF7ED\] {
        background-color: #FFF7ED !important;
    }

    .bg-\[\#FFF0E6\],
    .bg-\[\#FDF4EB\] {
        background-color: #FFF0E6 !important;
    }

    .bg-\[\#EAF7EE\],
    .bg-\[\#ECFDF5\] {
        background-color: #EAF7EE !important;
    }

    .bg-\[\#FEF2F2\] {
        background-color: #FEF2F2 !important;
    }

    .bg-\[\#FDF8F3\] {
        background-color: #FDF8F3 !important;
    }

    .bg-\[\#1E8E3E\],
    .bg-\[\#15803D\] {
        background-color: #1E8E3E !important;
    }

    .bg-\[\#BE185D\] {
        background-color: #BE185D !important;
    }

    .bg-\[\#C2410C\] {
        background-color: #C2410C !important;
    }

    .text-\[\#2B1E16\],
    .text-\[\#2D231C\] {
        color: #2B1E16 !important;
    }

    .text-\[\#554338\],
    .text-\[\#5C4E43\] {
        color: #554338 !important;
    }

    .text-\[\#8D7B70\],
    .text-\[\#88776B\] {
        color: #8D7B70 !important;
    }

    .text-\[\#F26522\],
    .text-\[\#FF7700\] {
        color: #F26522 !important;
    }

    .text-\[\#1E8E3E\],
    .text-\[\#15803D\] {
        color: #1E8E3E !important;
    }

    .text-\[\#D96B27\] {
        color: #D96B27 !important;
    }

    .text-\[\#DC2626\] {
        color: #DC2626 !important;
    }

    .text-\[\#BE185D\] {
        color: #BE185D !important;
    }

    .text-\[\#C2410C\] {
        color: #C2410C !important;
    }

    .border-\[\#EFE7DE\],
    .border-\[\#F0E6DD\] {
        border-color: #EFE7DE !important;
    }

    .border-\[\#F2C49B\],
    .border-\[\#FED7AA\],
    .border-\[\#FAD7C0\] {
        border-color: #FED7AA !important;
    }

    .border-\[\#CDEED5\],
    .border-\[\#A7F3D0\] {
        border-color: #CDEED5 !important;
    }

    .border-\[\#FECACA\] {
        border-color: #FECACA !important;
    }

    /* ==========================================================================
       DARK THEME (Zinc / Obsidian)
       ========================================================================== */
    html.dark {
        --color-cream-canvas: #09090b;
        --color-cream-surface: #18181b;
        --color-cream-tile: #121215;
        --color-cream-border: #27272a;
        --color-espresso-primary: #f4f4f5;
        --color-espresso-body: #d4d4d8;
        --color-espresso-muted: #a1a1aa;
    }

    html.dark body {
        background-color: #09090b !important;
        color: #f4f4f5 !important;
    }

    /* Direct Theme Classes (Dark) */
    html.dark .bg-canvas {
        background-color: #09090b !important;
    }

    html.dark .bg-card {
        background-color: #18181b !important;
        color: #f4f4f5 !important;
    }

    html.dark .bg-tile {
        background-color: #121215 !important;
    }

    html.dark .border-theme {
        border-color: #27272a !important;
    }

    html.dark .text-espresso {
        color: #f4f4f5 !important;
    }

    html.dark .text-espresso-muted {
        color: #a1a1aa !important;
    }

    /* Tailwind mapped overrides for Dark Theme */
    html.dark .bg-\[\#FBF7F0\],
    html.dark .bg-\[\#FAF5EF\] {
        background-color: #09090b !important;
    }

    html.dark .bg-white {
        background-color: #18181b !important;
        color: #f4f4f5 !important;
    }

    html.dark .bg-\[\#F8F3EA\],
    html.dark .bg-\[\#FAF6F0\] {
        background-color: #121215 !important;
    }

    html.dark .bg-\[\#FFF0E6\],
    html.dark .bg-\[\#FDF4EB\],
    html.dark .bg-\[\#FFF7ED\] {
        background-color: rgba(242, 101, 34, 0.15) !important;
    }

    html.dark .bg-\[\#EAF7EE\],
    html.dark .bg-\[\#ECFDF5\] {
        background-color: rgba(16, 185, 129, 0.15) !important;
    }

    html.dark .bg-\[\#FEF2F2\] {
        background-color: rgba(220, 38, 38, 0.15) !important;
    }

    html.dark .bg-\[\#FDF8F3\] {
        background-color: rgba(242, 101, 34, 0.12) !important;
    }

    html.dark .border-\[\#EFE7DE\],
    html.dark .border-\[\#F0E6DD\] {
        border-color: #27272a !important;
    }

    html.dark .border-\[\#FED7AA\],
    html.dark .border-\[\#FAD7C0\],
    html.dark .border-\[\#F2C49B\] {
        border-color: rgba(242, 101, 34, 0.35) !important;
    }

    html.dark .border-\[\#CDEED5\],
    html.dark .border-\[\#A7F3D0\] {
        border-color: rgba(16, 185, 129, 0.3) !important;
    }

    html.dark .border-\[\#FECACA\] {
        border-color: rgba(220, 38, 38, 0.3) !important;
    }

    html.dark .text-\[\#2B1E16\],
    html.dark .text-\[\#2D231C\] {
        color: #f4f4f5 !important;
    }

    html.dark .text-\[\#554338\],
    html.dark .text-\[\#5C4E43\] {
        color: #d4d4d8 !important;
    }

    html.dark .text-\[\#8D7B70\],
    html.dark .text-\[\#88776B\] {
        color: #a1a1aa !important;
    }

    html.dark .hover\:bg-\[\#F8F3EA\]:hover {
        background-color: #27272a !important;
    }

    html.dark .hover\:text-\[\#2B1E16\]:hover {
        color: #ffffff !important;
    }

    html.dark input,
    html.dark select,
    html.dark textarea {
        background-color: #121215 !important;
        color: #f4f4f5 !important;
        border-color: #27272a !important;
    }

    html.dark table thead {
        background-color: #121215 !important;
        color: #a1a1aa !important;
        border-color: #27272a !important;
    }

    html.dark table tbody tr {
        border-color: #27272a !important;
    }

    html.dark table tbody tr:hover {
        background-color: rgba(39, 39, 42, 0.5) !important;
    }

    html.dark .divide-\[\#EFE7DE\]> :not([hidden])~ :not([hidden]) {
        border-color: #27272a !important;
    }

    html.dark aside {
        background-color: #121215 !important;
        border-color: #27272a !important;
    }

    html.dark header {
        background-color: rgba(18, 18, 21, 0.95) !important;
        border-color: #27272a !important;
    }

    html.dark nav {
        border-color: #27272a !important;
    }

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
        transform: scale(0.96);
        filter: brightness(0.95);
    }

    /* Safe area padding for mobile notches */
    .safe-bottom {
        padding-bottom: env(safe-area-inset-bottom, 1rem);
    }

    /* Custom thin scrollbar */
    .mobile-screen-scroll::-webkit-scrollbar,
    .scrollbar-none::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
</style>

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
<script>
    if (window.Flux) {
        @if($isAuthRoute)
            window.Flux.applyAppearance('dark');
        @else
            var activeTheme = localStorage.getItem('cartflow_theme') || localStorage.getItem('cartflow_admin_theme') || 'bright';
            window.Flux.applyAppearance(activeTheme === 'dark' ? 'dark' : 'light');
        @endif
    }
</script>
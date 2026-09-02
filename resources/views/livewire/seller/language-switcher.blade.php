<div class="inline-flex items-center bg-zinc-800/90 p-1 rounded-xl border border-zinc-700/60 shadow-inner">
    <button type="button"
            wire:click="switchLanguage('bn')"
            class="px-2.5 sm:px-3 py-1 rounded-lg text-xs font-bold transition-all touch-press cursor-pointer flex items-center gap-1 {{ $currentLocale === 'bn' ? 'bg-amber-500 text-zinc-950 font-black shadow-md' : 'text-zinc-400 hover:text-zinc-200' }}"
            title="বাংলা ভাষায় পরিবর্তন করুন">
        <span>🇧🇩</span>
        <span>বাংলা</span>
    </button>
    <button type="button"
            wire:click="switchLanguage('en')"
            class="px-2.5 sm:px-3 py-1 rounded-lg text-xs font-bold transition-all touch-press cursor-pointer flex items-center gap-1 {{ $currentLocale === 'en' ? 'bg-amber-500 text-zinc-950 font-black shadow-md' : 'text-zinc-400 hover:text-zinc-200' }}"
            title="Switch to English">
        <span>🇬🇧</span>
        <span>English</span>
    </button>
</div>

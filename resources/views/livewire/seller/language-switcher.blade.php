<div class="grid grid-cols-2 gap-2 bg-[#F8F3EA] p-1 rounded-2xl border border-[#EFE7DE]">
    <!-- Bangla Option -->
    <button type="button" wire:click="switchLanguage('bn')" wire:loading.attr="disabled"
        class="py-2 px-2.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer flex items-center justify-center gap-1.5 {{ $currentLocale === 'bn' ? 'bg-[#F26522] text-white font-black shadow-2xs' : 'text-[#554338] hover:text-[#2B1E16]' }}"
        title="বাংলা">
        <span>🇧🇩</span>
        <span>বাংলা</span>
    </button>

    <!-- English Option -->
    <button type="button" wire:click="switchLanguage('en')" wire:loading.attr="disabled"
        class="py-2 px-2.5 rounded-xl text-xs font-bold transition-all touch-press cursor-pointer flex items-center justify-center gap-1.5 {{ $currentLocale === 'en' ? 'bg-[#F26522] text-white font-black shadow-2xs' : 'text-[#554338] hover:text-[#2B1E16]' }}"
        title="English">
        <span>🇬🇧</span>
        <span>English</span>
    </button>
</div>
<div
    x-data="{ locale: null }"
    x-init="
        locale = localStorage.getItem('locale') || '{{ app()->getLocale() }}';
    "
    class="fi-theme-switcher flex items-center gap-1 me-2"
>
    <template x-for="(lang, code) in {
        en: { label: ' EN', flag: '🇺🇸 ' },
        uk: { label: ' UA', flag: '🇺🇦 ' },
        ru: { label: ' RU', flag: '' },

    }" :key="code">
        <button
            type="button"
            class="fi-theme-switcher-btn text-sm flex items-center justify-center gap-1 px-1 py-1 rounded-lg border border-transparent transition"
            :class="{
                'bg-primary-500/10 text-primary-600 dark:text-primary-400 border-primary-500/30': locale === code,
                'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800': locale !== code
            }"
            x-on:click="
                locale = code;
                localStorage.setItem('locale', code);
                window.location.href = `/lang/${code}`;
            "
            {{-- x-tooltip="{
                content: lang.label,
                theme: $store.theme,
            }" --}}
        >
            <span class="text-base" x-text="lang.flag"></span>
            <span class="font-medium" x-text="lang.label"></span>
        </button>
    </template>
</div>

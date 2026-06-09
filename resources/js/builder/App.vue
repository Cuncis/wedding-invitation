<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useBuilderStore } from './store';
import ThemePicker from './components/ThemePicker.vue';
import AddonPanel from './components/AddonPanel.vue';
import AnimationPicker from './components/AnimationPicker.vue';
import SectionEditor from './components/SectionEditor.vue';
import ColorPicker from './components/ColorPicker.vue';
import TypographyPicker from './components/TypographyPicker.vue';
import PriceBreakdown from './components/PriceBreakdown.vue';
import AddonSettingsPanel from './components/AddonSettingsPanel.vue';
import PreviewFrame from './components/PreviewFrame.vue';
import IconArt from './components/icons/IconArt.vue';
import IconDocument from './components/icons/IconDocument.vue';
import IconFont from './components/icons/IconFont.vue';
import IconMagicWand from './components/icons/IconMagicWand.vue';
import IconPuzzlePiece from './components/icons/IconPuzzlePiece.vue';
import IconWaterDrop from './components/icons/IconWaterDrop.vue';

const props = defineProps({
    invitation: { type: Object, required: true },
    themes: { type: Array, required: true },
    addons: { type: Array, required: true },
    animationPacks: { type: Array, required: true },
    previewUrl: { type: String, required: true },
    checkoutUrl: { type: String, required: true },
});

const store = useBuilderStore();

const previewFrame = ref(null);

function postColorsToPreview(colors) {
    const iframe = previewFrame.value?.iframeEl;
    if (iframe?.contentWindow) {
        iframe.contentWindow.postMessage(
            { type: 'preview:colors', colors },
            '*',
        );
    }
}

function postTypographyToPreview(typography) {
    const iframe = previewFrame.value?.iframeEl;
    if (iframe?.contentWindow) {
        iframe.contentWindow.postMessage(
            { type: 'preview:typography', typography },
            '*',
        );
    }
}

function onPreviewLoad() {
    postColorsToPreview(store.config.colors);
    postTypographyToPreview(store.config.typography);
}

watch(
    () => store.config.colors,
    (colors) => postColorsToPreview(colors),
    { deep: true },
);

watch(
    () => store.config.typography,
    (typography) => postTypographyToPreview(typography),
    { deep: true },
);

watch(
    () => [store.config.content?.couple?.groom_photo, store.config.content?.couple?.bride_photo],
    ([groomPhoto, bridePhoto]) => {
        const iframe = previewFrame.value?.iframeEl;
        if (iframe?.contentWindow) {
            iframe.contentWindow.postMessage(
                { type: 'preview:couplePhotos', groomPhoto, bridePhoto },
                '*',
            );
        }
    },
);

const invitationTitle = computed(() => {
    const groom = props.invitation.groom_name;
    const bride = props.invitation.bride_name;
    if (groom && bride) return `${groom} & ${bride}`;
    if (groom) return `${groom} & Pasangan`;
    if (bride) return `Pasangan & ${bride}`;
    return 'Undangan Baru';
});

onMounted(() => {
    document.title = `Builder - ${invitationTitle.value}`;
});

const tabs = [
    { key: 'theme', label: 'Tema', icon: 'theme' },
    { key: 'content', label: 'Konten', icon: 'content' },
    { key: 'addon', label: 'Fitur', icon: 'addon' },
    { key: 'animation', label: 'Animasi', icon: 'animation' },
    { key: 'colors', label: 'Warna', icon: 'colors' },
    { key: 'fonts', label: 'Font', icon: 'fonts' },
];
const activeTab = ref('theme');

const SETTINGS_KEYS = ['music_player', 'photo_gallery', 'maps', 'countdown', 'digital_gift', 'love_story', 'live_stream'];

const hasAddonSettings = computed(() => {
    if (activeTab.value !== 'addon') return false;
    const selectedIds = store.config.addon_ids ?? [];
    return props.addons
        .filter(a => selectedIds.includes(a.id))
        .some(a => SETTINGS_KEYS.includes(a.key));
});

const saveStatus = computed(() => {
    if (store.isSaving) return { label: 'Menyimpan...', color: 'text-amber-600' };
    if (store.isDirty) return { label: 'Belum tersimpan', color: 'text-rose-600' };
    if (store.lastSavedAt) return { label: 'Tersimpan otomatis', color: 'text-emerald-600' };
    return { label: 'Siap diedit', color: 'text-slate-500' };
});
</script>

<template>
    <div class="h-screen flex flex-col bg-slate-100">
        <!-- Top bar -->
        <header class="flex items-center justify-between px-4 py-2.5 bg-white border-b border-slate-200 shrink-0">
            <div class="flex items-center gap-3">
                <a href="/dashboard" class="btn btn-ghost btn-sm btn-circle text-slate-500"
                    title="Kembali ke Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-sm font-semibold text-slate-900 leading-tight">
                        {{ invitationTitle }}
                    </h1>
                    <p :class="['text-xs leading-tight', saveStatus.color]">{{ saveStatus.label }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a :href="previewUrl" target="_blank" class="btn btn-sm btn-ghost btn-outline border-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Preview
                </a>
                <a :href="checkoutUrl" class="btn btn-sm btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    Checkout
                </a>
            </div>
        </header>

        <!-- 3-column body -->
        <div class="flex-1 grid grid-cols-12 min-h-0">
            <!-- Left: tabs + active editor -->
            <aside class="col-span-4 flex flex-col bg-white border-r border-slate-200 min-h-0">
                <nav class="flex border-b border-slate-200 overflow-x-auto">
                    <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="[
                        'flex flex-col items-center justify-center flex-1 min-w-20 px-3 py-3 text-xs font-medium transition border-b-2 gap-1',
                        activeTab === tab.key
                            ? 'border-rose-500 text-rose-600 bg-rose-50/50'
                            : 'border-transparent text-slate-500 hover:text-slate-800',
                    ]">
                        <span class="w-6 h-6 block">
                            <IconArt v-if="tab.key === 'theme'" class="w-full h-full" />
                            <IconDocument v-if="tab.key === 'content'" class="w-full h-full" />
                            <IconPuzzlePiece v-if="tab.key === 'addon'" class="w-full h-full" />
                            <IconMagicWand v-if="tab.key === 'animation'" class="w-full h-full" />
                            <IconWaterDrop v-if="tab.key === 'colors'" class="w-full h-full" />
                            <IconFont v-if="tab.key === 'fonts'" class="w-full h-full" />
                        </span>
                        {{ tab.label }}
                    </button>
                </nav>

                <div class="flex-1 overflow-y-auto p-4">
                    <ThemePicker v-if="activeTab === 'theme'" :themes="themes" />
                    <SectionEditor v-if="activeTab === 'content'" />
                    <AddonPanel v-if="activeTab === 'addon'" :addons="addons" />
                    <AnimationPicker v-if="activeTab === 'animation'" :packs="animationPacks" />
                    <ColorPicker v-if="activeTab === 'colors'" />
                    <TypographyPicker v-if="activeTab === 'fonts'" />
                </div>
            </aside>

            <!-- Center: preview iframe -->
            <main class="col-span-5 flex flex-col bg-slate-200 p-4 min-h-0">
                <PreviewFrame ref="previewFrame" :src="previewUrl" :reload-key="store.previewKey"
                    @load="onPreviewLoad" />
            </main>

            <!-- Right: addon settings OR pricing -->
            <aside class="col-span-3 bg-white border-l border-slate-200 flex flex-col min-h-0">
                <AddonSettingsPanel v-if="hasAddonSettings" :addons="addons" :checkout-url="checkoutUrl" />
                <div v-else class="flex-1 overflow-y-auto">
                    <PriceBreakdown :checkout-url="checkoutUrl" />
                </div>
            </aside>
        </div>
    </div>
</template>

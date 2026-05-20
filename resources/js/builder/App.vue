<script setup>
import { ref, computed } from 'vue';
import { useBuilderStore } from './store';
import ThemePicker from './components/ThemePicker.vue';
import AddonPanel from './components/AddonPanel.vue';
import AnimationPicker from './components/AnimationPicker.vue';
import SectionEditor from './components/SectionEditor.vue';
import ColorPicker from './components/ColorPicker.vue';
import TypographyPicker from './components/TypographyPicker.vue';
import PriceBreakdown from './components/PriceBreakdown.vue';
import PreviewFrame from './components/PreviewFrame.vue';
import IconArt from './components/icons/IconArt.vue';
import IconDocument from './components/icons/IconDocument.vue';
import IconFont from './components/icons/IconFont.vue';
import IconMagicWand from './components/icons/IconMagicWand.vue';
import IconPuzzlePiece from './components/icons/IconPuzzlePiece.vue';
import IconWaterDrop from './components/icons/IconWaterDrop.vue';

const props = defineProps({
    invitation:     { type: Object, required: true },
    themes:         { type: Array,  required: true },
    addons:         { type: Array,  required: true },
    animationPacks: { type: Array,  required: true },
    previewUrl:     { type: String, required: true },
    checkoutUrl:    { type: String, required: true },
});

const store = useBuilderStore();

const tabs = [
    { key: 'theme',     label: 'Tema',    icon: 'theme' },
    { key: 'content',   label: 'Konten',  icon: 'content' },
    { key: 'addon',     label: 'Fitur',   icon: 'addon' },
    { key: 'animation', label: 'Animasi', icon: 'animation' },
    { key: 'colors',    label: 'Warna',   icon: 'colors' },
    { key: 'fonts',     label: 'Font',    icon: 'fonts' },
];
const activeTab = ref('theme');

const saveStatus = computed(() => {
    if (store.isSaving) return { label: 'Menyimpan...', color: 'text-amber-600' };
    if (store.isDirty)  return { label: 'Belum tersimpan', color: 'text-rose-600' };
    if (store.lastSavedAt) return { label: 'Tersimpan otomatis', color: 'text-emerald-600' };
    return { label: 'Siap diedit', color: 'text-slate-500' };
});
</script>

<template>
    <div class="h-screen flex flex-col bg-slate-100">
        <!-- Top bar -->
        <header class="flex items-center justify-between px-6 py-3 bg-white border-b border-slate-200">
            <div class="flex items-center gap-4">
                <a href="/dashboard" class="text-slate-500 hover:text-slate-700">←</a>
                <div>
                    <h1 class="text-base font-semibold text-slate-900">
                        {{ invitation.groom_name || '???' }} &amp; {{ invitation.bride_name || '???' }}
                    </h1>
                    <p :class="['text-xs', saveStatus.color]">{{ saveStatus.label }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a :href="previewUrl" target="_blank"
                    class="px-3 py-1.5 text-sm text-slate-600 hover:text-slate-900 border border-slate-300 rounded">
                    Preview Baru
                </a>
                <a :href="checkoutUrl"
                    class="px-4 py-1.5 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded">
                    Lanjut ke Checkout
                </a>
            </div>
        </header>

        <!-- 3-column body -->
        <div class="flex-1 grid grid-cols-12 min-h-0">
            <!-- Left: tabs + active editor -->
            <aside class="col-span-4 flex flex-col bg-white border-r border-slate-200 min-h-0">
                <nav class="flex border-b border-slate-200 overflow-x-auto">
                    <button v-for="tab in tabs" :key="tab.key"
                        @click="activeTab = tab.key"
                        :class="[
                            'flex flex-col items-center justify-center flex-1 min-w-[80px] px-3 py-3 text-xs font-medium transition border-b-2 gap-1',
                            activeTab === tab.key
                                ? 'border-rose-500 text-rose-600 bg-rose-50/50'
                                : 'border-transparent text-slate-500 hover:text-slate-800',
                        ]">
                        <span class="w-6 h-6 block">
                            <IconArt          v-if="tab.key === 'theme'"     class="w-full h-full" />
                            <IconDocument     v-if="tab.key === 'content'"   class="w-full h-full" />
                            <IconPuzzlePiece  v-if="tab.key === 'addon'"     class="w-full h-full" />
                            <IconMagicWand    v-if="tab.key === 'animation'" class="w-full h-full" />
                            <IconWaterDrop    v-if="tab.key === 'colors'"    class="w-full h-full" />
                            <IconFont         v-if="tab.key === 'fonts'"     class="w-full h-full" />
                        </span>
                        {{ tab.label }}
                    </button>
                </nav>

                <div class="flex-1 overflow-y-auto p-4">
                    <ThemePicker      v-if="activeTab === 'theme'"     :themes="themes" />
                    <SectionEditor    v-if="activeTab === 'content'"   />
                    <AddonPanel       v-if="activeTab === 'addon'"     :addons="addons" />
                    <AnimationPicker  v-if="activeTab === 'animation'" :packs="animationPacks" />
                    <ColorPicker      v-if="activeTab === 'colors'"    />
                    <TypographyPicker v-if="activeTab === 'fonts'"     />
                </div>
            </aside>

            <!-- Center: preview iframe -->
            <main class="col-span-5 flex flex-col bg-slate-200 p-4 min-h-0">
                <PreviewFrame :src="previewUrl" :reload-key="store.previewKey" />
            </main>

            <!-- Right: pricing -->
            <aside class="col-span-3 bg-white border-l border-slate-200 overflow-y-auto">
                <PriceBreakdown :checkout-url="checkoutUrl" />
            </aside>
        </div>
    </div>
</template>

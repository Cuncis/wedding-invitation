<script setup>
import { useBuilderStore } from '../store';

defineProps({
    themes: { type: Array, required: true },
});

const store = useBuilderStore();

const formatRupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

function selectTheme(theme) {
    store.config.theme_id = theme.id;
    if (theme.default_colors) {
        store.config.colors = { ...store.config.colors, ...theme.default_colors };
    }
    if (theme.default_fonts) {
        store.config.typography = { ...store.config.typography, ...theme.default_fonts };
    }
}
</script>

<template>
    <div>
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Pilih Tema</h2>
        <p class="text-xs text-slate-500 mb-4">Tema menentukan layout dan harga dasar undangan.</p>

        <div v-if="themes.length === 0" class="text-sm text-slate-500 italic">
            Belum ada tema aktif.
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button v-for="theme in themes" :key="theme.id"
                type="button"
                @click="selectTheme(theme)"
                :class="[
                    'text-left rounded-lg border-2 overflow-hidden transition',
                    store.config.theme_id === theme.id
                        ? 'border-rose-500 ring-2 ring-rose-200'
                        : 'border-slate-200 hover:border-slate-400',
                ]">
                <div class="aspect-[3/4] bg-slate-100 flex items-center justify-center">
                    <img v-if="theme.preview_image" :src="theme.preview_image" :alt="theme.name"
                        class="w-full h-full object-cover">
                    <span v-else class="text-3xl">🎨</span>
                </div>
                <div class="p-2">
                    <p class="text-sm font-semibold text-slate-900 truncate">{{ theme.name }}</p>
                    <p class="text-xs text-rose-600 font-medium">{{ formatRupiah(theme.base_price) }}</p>
                </div>
            </button>
        </div>
    </div>
</template>

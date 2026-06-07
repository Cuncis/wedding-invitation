<script setup>
import { useBuilderStore } from '../store';

const store = useBuilderStore();

const fields = [
    { key: 'primary',   label: 'Primer',    hint: 'Headings & aksen utama' },
    { key: 'secondary', label: 'Sekunder',  hint: 'Latar belakang lembut' },
    { key: 'accent',    label: 'Aksen',     hint: 'Tombol & ornamen detail' },
    { key: 'text',      label: 'Teks',      hint: 'Warna huruf konten' },
];
</script>

<template>
    <div>
        <h2 class="text-sm font-semibold text-slate-900 mb-1">Skema Warna</h2>
        <p class="text-xs text-slate-500 mb-4">Klik kotak warna untuk memilih, atau ketik kode hex.</p>
        <div class="space-y-2">
            <div v-for="field in fields" :key="field.key"
                class="flex items-center gap-3 p-3 rounded-lg bg-base-100 border border-base-200">

                <!-- Styled swatch — transparent native picker layered on top -->
                <label class="relative shrink-0 cursor-pointer">
                    <span
                        class="block w-10 h-10 rounded-lg border-2 border-base-300 shadow-sm transition-transform hover:scale-105"
                        :style="{ background: store.config.colors[field.key] }">
                    </span>
                    <input
                        type="color"
                        v-model="store.config.colors[field.key]"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        tabindex="-1">
                </label>

                <!-- Label -->
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-base-content leading-tight">{{ field.label }}</p>
                    <p class="text-xs text-base-content/50 leading-tight mt-0.5">{{ field.hint }}</p>
                </div>

                <!-- Hex input — uses proper DaisyUI class so border renders -->
                <input
                    type="text"
                    v-model="store.config.colors[field.key]"
                    class="input input-xs input-bordered font-mono w-24 text-center tracking-wide"
                    maxlength="7"
                    spellcheck="false"
                    placeholder="#000000">
            </div>
        </div>
    </div>
</template>

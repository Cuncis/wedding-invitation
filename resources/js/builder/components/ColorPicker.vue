<script setup>
import { useBuilderStore } from '../store';

const store = useBuilderStore();

const fields = [
    { key: 'primary',   label: 'Primer',    hint: 'Warna utama' },
    { key: 'secondary', label: 'Sekunder',  hint: 'Latar lembut' },
    { key: 'accent',    label: 'Aksen',     hint: 'Tombol & detail' },
    { key: 'text',      label: 'Teks',      hint: 'Warna huruf' },
];
</script>

<template>
    <div>
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Skema Warna</h2>
        <div class="space-y-3">
            <div v-for="field in fields" :key="field.key"
                class="flex items-center gap-3 p-2 rounded-lg border border-slate-200">
                <input type="color"
                    v-model="store.config.colors[field.key]"
                    class="h-12 w-12 rounded cursor-pointer border-0 p-0">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-900">{{ field.label }}</p>
                    <p class="text-xs text-slate-500">{{ field.hint }}</p>
                </div>
                <input type="text"
                    v-model="store.config.colors[field.key]"
                    class="w-24 rounded border-slate-300 text-xs font-mono">
            </div>
        </div>
    </div>
</template>

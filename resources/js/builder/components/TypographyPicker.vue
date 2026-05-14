<script setup>
import { useBuilderStore } from '../store';

const store = useBuilderStore();

const headingFonts = [
    'Playfair Display', 'Cormorant Garamond', 'Cinzel', 'Great Vibes',
    'Marcellus', 'Italiana', 'DM Serif Display', 'Lora',
];
const bodyFonts = [
    'Lato', 'Open Sans', 'Roboto', 'Inter',
    'Source Sans 3', 'Nunito', 'Poppins', 'Manrope',
];
</script>

<template>
    <div class="space-y-4">
        <h2 class="text-sm font-semibold text-slate-900">Tipografi</h2>

        <label class="block">
            <span class="text-xs font-medium text-slate-600">Font Judul</span>
            <select v-model="store.config.typography.heading"
                class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                <option v-for="font in headingFonts" :key="font" :value="font">{{ font }}</option>
            </select>
            <p class="mt-2 text-xl text-slate-900" :style="{ fontFamily: store.config.typography.heading }">
                The quick brown fox 🦊
            </p>
        </label>

        <label class="block">
            <span class="text-xs font-medium text-slate-600">Font Isi</span>
            <select v-model="store.config.typography.body"
                class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                <option v-for="font in bodyFonts" :key="font" :value="font">{{ font }}</option>
            </select>
            <p class="mt-2 text-sm text-slate-700" :style="{ fontFamily: store.config.typography.body }">
                Merupakan suatu kehormatan apabila Bapak/Ibu/Saudara/i berkenan hadir.
            </p>
        </label>
    </div>
</template>

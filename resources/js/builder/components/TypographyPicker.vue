<script setup>
import { ref, onMounted } from 'vue';
import { useBuilderStore } from '../store';

const store = useBuilderStore();
const emit = defineEmits(['fontChanged']);

const headingFonts = [
    'Playfair Display', 'Cormorant Garamond', 'Cinzel', 'Great Vibes',
    'Marcellus', 'Italiana', 'DM Serif Display', 'Lora', 'Bodoni Moda',
];
const bodyFonts = [
    'Lato', 'Open Sans', 'Roboto', 'Inter',
    'Source Sans 3', 'Nunito', 'Poppins', 'Manrope', 'Montserrat', 'Google Sans',
];

const headingOpen = ref(false);
const bodyOpen = ref(false);

function loadFont(family) {
    if (!family) return;
    const id = `gf-${family.replace(/\s+/g, '-')}`;
    if (document.getElementById(id)) return;
    const link = document.createElement('link');
    link.id = id;
    link.rel = 'stylesheet';
    link.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(family)}:wght@400;600;700&display=swap`;
    document.head.appendChild(link);
}

onMounted(() => {
    [...headingFonts, ...bodyFonts].forEach(loadFont);
});

function pickHeading(font) {
    store.config.typography.heading = font;
    headingOpen.value = false;
    emit('fontChanged');
}

function pickBody(font) {
    store.config.typography.body = font;
    bodyOpen.value = false;
    emit('fontChanged');
}


</script>

<template>
    <div>
        <h2 class="text-sm font-semibold text-base-content mb-1">Tipografi</h2>
        <p class="text-xs text-base-content/50 mb-4">Pilih font untuk judul dan isi undangan.</p>

        <div class="space-y-2">
            <!-- Font Judul -->
            <div class="p-3 rounded-xl border-2 border-base-300 bg-base-100 space-y-2">
                <div>
                    <p class="text-xs font-semibold text-base-content leading-tight">Font Judul</p>
                    <p class="text-xs text-base-content/50 leading-tight mt-0.5">Headings & nama pasangan</p>
                </div>

                <!-- Custom font picker -->
                <div class="relative">
                    <button type="button" @click="headingOpen = !headingOpen" @blur="headingOpen = false"
                        class="flex items-center justify-between w-full px-3 py-1.5 rounded-lg border border-base-300 bg-base-100 text-sm hover:border-primary/60 transition">
                        <span :style="{ fontFamily: store.config.typography.heading }">
                            {{ store.config.typography.heading }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 text-base-content/40 shrink-0 ml-2 transition-transform"
                            :class="{ 'rotate-180': headingOpen }" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <ul v-if="headingOpen" @mousedown.prevent
                        class="absolute z-50 mt-1 w-full rounded-xl border border-base-200 bg-base-100 shadow-lg overflow-hidden max-h-52 overflow-y-auto">
                        <li v-for="font in headingFonts" :key="font">
                            <button type="button" @click="pickHeading(font)" :class="[
                                'w-full text-left px-3 py-2 text-sm transition hover:bg-primary/10',
                                store.config.typography.heading === font
                                    ? 'bg-primary/10 text-primary font-semibold'
                                    : 'text-base-content',
                            ]" :style="{ fontFamily: font }">
                                {{ font }}
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Live preview text -->
                <p class="text-xl text-base-content transition-all duration-300"
                    :style="{ fontFamily: store.config.typography.heading }">
                    The quick brown fox 🦊
                </p>
            </div>

            <!-- Font Isi -->
            <div class="p-3 rounded-xl border-2 border-base-300 bg-base-100 space-y-2">
                <div>
                    <p class="text-xs font-semibold text-base-content leading-tight">Font Isi</p>
                    <p class="text-xs text-base-content/50 leading-tight mt-0.5">Paragraf & detail acara</p>
                </div>

                <!-- Custom font picker -->
                <div class="relative">
                    <button type="button" @click="bodyOpen = !bodyOpen" @blur="bodyOpen = false"
                        class="flex items-center justify-between w-full px-3 py-1.5 rounded-lg border border-base-300 bg-base-100 text-sm hover:border-primary/60 transition">
                        <span :style="{ fontFamily: store.config.typography.body }">
                            {{ store.config.typography.body }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 text-base-content/40 shrink-0 ml-2 transition-transform"
                            :class="{ 'rotate-180': bodyOpen }" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <ul v-if="bodyOpen" @mousedown.prevent
                        class="absolute z-50 mt-1 w-full rounded-xl border border-base-200 bg-base-100 shadow-lg overflow-hidden max-h-52 overflow-y-auto">
                        <li v-for="font in bodyFonts" :key="font">
                            <button type="button" @click="pickBody(font)" :class="[
                                'w-full text-left px-3 py-2 text-sm transition hover:bg-primary/10',
                                store.config.typography.body === font
                                    ? 'bg-primary/10 text-primary font-semibold'
                                    : 'text-base-content',
                            ]" :style="{ fontFamily: font }">
                                {{ font }}
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Live preview text -->
                <p class="text-sm text-base-content/80 transition-all duration-300"
                    :style="{ fontFamily: store.config.typography.body }">
                    Merupakan suatu kehormatan apabila Bapak/Ibu/Saudara/i berkenan hadir.
                </p>
            </div>
        </div>
    </div>
</template>

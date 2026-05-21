<script setup>
import { ref, computed } from 'vue';
import { useBuilderStore } from '../../store';
import IconMaps from '../icons/IconMaps.vue';

const store = useBuilderStore();

const localMaps = ref({
    address: '',
    embed_url: '',
    show_marker: true,
});

const mapsAddress = computed({
    get: () => (store.config.maps?.address) ?? localMaps.value.address,
    set: (v) => {
        localMaps.value.address = v;
        store.config.maps = { ...localMaps.value };
    },
});

const mapsEmbedUrl = computed({
    get: () => (store.config.maps?.embed_url) ?? localMaps.value.embed_url,
    set: (v) => {
        localMaps.value.embed_url = v;
        store.config.maps = { ...localMaps.value };
    },
});

const mapsShowMarker = computed({
    get: () => (store.config.maps?.show_marker) ?? localMaps.value.show_marker,
    set: (v) => {
        localMaps.value.show_marker = v;
        store.config.maps = { ...localMaps.value };
    },
});

// Legacy accessor for compatibility
const maps = computed({
    get: () => {
        const stored = store.config.maps;
        if (stored && typeof stored === 'object' && !Array.isArray(stored)) {
            return stored;
        }
        return localMaps.value;
    },
    set: (v) => {
        if (v === null || v === undefined) {
            localMaps.value = { address: '', embed_url: '', show_marker: true };
        } else if (typeof v === 'object') {
            localMaps.value = { ...localMaps.value, ...v };
        }
        store.config.maps = { ...localMaps.value };
    },
});

const embedUrl = computed(() => {
    const address = mapsAddress.value;
    const url = mapsEmbedUrl.value;

    // If we have an address, use it to create a search embed URL
    if (address) {
        return `https://maps.google.com/maps?q=${encodeURIComponent(address)}&t=&z=17&ie=UTF8&iwloc=&output=embed`;
    }

    // If we have a Google Maps URL, try to convert it to embed format
    if (url) {
        // If it's already an embed URL, return as-is
        if (url.includes('maps.google.com/maps?q=') || url.includes('maps.google.com/embed?')) {
            return url;
        }

        // Handle Google Maps short URLs (maps.app.goo.gl/...) - can't expand, so use URL as search
        if (url.includes('maps.app.goo.gl')) {
            return `https://maps.google.com/maps?q=${encodeURIComponent(url)}&t=&z=17&ie=UTF8&iwloc=&output=embed`;
        }

        // If it's a regular Google Maps URL, try to extract query param
        try {
            const urlObj = new URL(url);
            if (urlObj.searchParams.has('q')) {
                return `https://maps.google.com/maps?q=${encodeURIComponent(urlObj.searchParams.get('q'))}&t=&z=17&ie=UTF8&iwloc=&output=embed`;
            }
        } catch {}

        // Fallback: use the URL as a search query
        return `https://maps.google.com/maps?q=${encodeURIComponent(url)}&t=&z=17&ie=UTF8&iwloc=&output=embed`;
    }

    return null;
});

const hasEmbed = computed(() => !!embedUrl.value);
</script>

<template>
    <div class="mt-4 p-4 rounded-lg border-2 border-accent-300 bg-accent-50/40">
        <div class="flex items-center gap-2 mb-3">
            <IconMaps class="w-5 h-5 text-rose-500" />
            <h3 class="text-sm font-bold text-slate-900">Pengaturan Google Maps</h3>
        </div>
        <p class="text-xs text-slate-600 mb-4">
            Tempelkan tautan Google Maps untuk menampilkan peta di undangan.
        </p>

        <!-- Address -->
        <div class="mb-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat / Nama Tempat</label>
            <input v-model="mapsAddress" type="text"
                placeholder="Nama Venue, Kota"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md">
        </div>

        <!-- Google Maps URL / Embed URL -->
        <div class="mb-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Link Google Maps</label>
            <input v-model="mapsEmbedUrl" type="url"
                placeholder="https://www.google.com/maps/place/..."
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md font-mono">
            <p class="text-xs text-slate-500 mt-1">
                Tempelkan tautan share atau embed dari Google Maps
            </p>
        </div>

        <!-- Map Preview -->
        <div v-if="hasEmbed" class="mt-3 rounded-md overflow-hidden border border-slate-200">
            <iframe
                :src="embedUrl"
                width="100%"
                height="200"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        <p v-else class="text-xs text-slate-400 mt-2 italic">
            Masukkan link Google Maps untuk melihat pratinjau peta
        </p>

        <!-- Options -->
        <div class="mt-4 space-y-2">
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" v-model="mapsShowMarker" class="accent-rose-500" :checked="mapsShowMarker !== false">
                Tampilkan marker di peta
            </label>
        </div>
    </div>
</template>
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
    <div class="rounded-xl border-2 border-primary/20 bg-primary/3 p-4 space-y-3">
        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-primary/15">
            <span class="inline-flex w-8 h-8 rounded-lg bg-primary/10 items-center justify-center shrink-0">
                <IconMaps class="w-4 h-4 text-primary" />
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-base-content leading-tight">Google Maps</p>
                <p class="text-xs text-base-content/50">Tampilkan peta lokasi acara</p>
            </div>
            <span class="badge badge-xs badge-primary badge-outline shrink-0">Pengaturan</span>
        </div>

        <!-- Address -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Alamat / Nama Tempat</span></div>
            <input v-model="mapsAddress" type="text"
                placeholder="Nama Venue, Kota"
                class="input input-sm input-bordered w-full">
        </label>

        <!-- Google Maps URL -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Link Google Maps</span></div>
            <input v-model="mapsEmbedUrl" type="url"
                placeholder="https://www.google.com/maps/place/..."
                class="input input-sm input-bordered w-full font-mono">
            <div class="label py-0.5">
                <span class="label-text-alt text-base-content/40">Tempel tautan share atau embed dari Google Maps</span>
            </div>
        </label>

        <!-- Map Preview -->
        <div v-if="hasEmbed" class="rounded-lg overflow-hidden border border-base-300">
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
        <p v-else class="text-xs text-base-content/40 italic">
            Masukkan link Google Maps untuk melihat pratinjau peta
        </p>

        <!-- Options -->
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" v-model="mapsShowMarker" class="checkbox checkbox-xs checkbox-primary" :checked="mapsShowMarker !== false">
            <span class="text-xs text-base-content">Tampilkan marker di peta</span>
        </label>
    </div>
</template>
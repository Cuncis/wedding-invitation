<script setup>
import { ref, computed } from 'vue';
import { useBuilderStore } from '../../store';
import IconGallery from '../icons/IconGallery.vue';

const store = useBuilderStore();

const isUploading = ref(false);
const uploadError = ref(null);

const gallery = computed({
    get: () => store.config.content?.gallery ?? { photos: [] },
    set: (v) => {
        store.config.content = { ...store.config.content, gallery: v };
    },
});

const photos = computed({
    get: () => gallery.value.photos ?? [],
    set: (v) => { gallery.value = { ...gallery.value, photos: v }; },
});

const columns = computed({
    get: () => gallery.value.columns ?? 3,
    set: (v) => { gallery.value = { ...gallery.value, columns: v }; },
});

const lightbox = computed({
    get: () => gallery.value.lightbox ?? true,
    set: (v) => { gallery.value = { ...gallery.value, lightbox: v }; },
});

function removePhoto(index) {
    const updated = [...photos.value];
    updated.splice(index, 1);
    photos.value = updated;
}

async function handleFiles(files) {
    const imageFiles = Array.from(files).filter(f => f.type.startsWith('image/'));
    if (!imageFiles.length) return;

    isUploading.value = true;
    uploadError.value = null;

    try {
        const newUrls = [];
        for (const file of imageFiles) {
            const formData = new FormData();
            formData.append('photo', file);

            const response = await fetch(`/builder/${store.invitationId}/gallery/upload`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    // Note: Do NOT set Content-Type for FormData - browser sets it automatically
                },
                body: formData,
            });

            if (!response.ok) {
                let errMsg = 'Upload failed';
                try {
                    const err = await response.json();
                    errMsg = err.message || err.error || errMsg;
                } catch {
                    // response wasn't JSON, try to get text
                    const text = await response.text();
                    console.error('Upload failed response:', text.substring(0, 200));
                    errMsg = response.status + ' ' + response.statusText;
                }
                throw new Error(errMsg);
            }

            const data = await response.json();
            newUrls.push(data.url);
        }

        photos.value = [...photos.value, ...newUrls];
    } catch (e) {
        uploadError.value = e.message;
    } finally {
        isUploading.value = false;
    }
}

function onDrop(e) {
    e.preventDefault();
    handleFiles(e.dataTransfer.files);
}

function onFileInput(e) {
    handleFiles(e.target.files);
    e.target.value = '';
}
</script>

<template>
    <div class="rounded-xl border-2 border-primary/20 bg-primary/3 p-4 space-y-3">
        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-primary/15">
            <span class="inline-flex w-8 h-8 rounded-lg bg-primary/10 items-center justify-center shrink-0">
                <IconGallery class="w-4 h-4 text-primary" />
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-base-content leading-tight">Galeri Foto</p>
                <p class="text-xs text-base-content/50">Unggah foto untuk galeri undangan</p>
            </div>
            <span class="badge badge-xs badge-primary badge-outline shrink-0">Pengaturan</span>
        </div>

        <!-- Layout options -->
        <div class="grid grid-cols-2 gap-3">
            <label class="form-control">
                <div class="label py-0.5"><span class="label-text text-xs">Jumlah Kolom</span></div>
                <select v-model="columns" class="select select-sm select-bordered w-full">
                    <option :value="2">2 Kolom</option>
                    <option :value="3">3 Kolom</option>
                    <option :value="4">4 Kolom</option>
                </select>
            </label>
            <label class="form-control">
                <div class="label py-0.5"><span class="label-text text-xs">Lightbox</span></div>
                <label class="flex items-center gap-2 cursor-pointer mt-1.5">
                    <input type="checkbox" v-model="lightbox" id="lightbox-toggle" class="checkbox checkbox-xs checkbox-primary">
                    <span class="text-xs text-base-content">Aktifkan lightbox</span>
                </label>
            </label>
        </div>

        <!-- Upload area -->
        <div
            @drop="onDrop"
            @dragover.prevent
            @click="$refs.fileInput.click()"
            class="border-2 border-dashed border-primary/30 rounded-xl p-5 text-center cursor-pointer hover:border-primary/60 hover:bg-primary/5 transition">
            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                multiple
                @change="onFileInput"
                class="hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary/50 mx-auto mb-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
            </svg>
            <p class="text-xs text-base-content/70">
                <span class="font-semibold text-primary">Klik atau drag foto</span> untuk upload
            </p>
            <p class="text-xs text-base-content/40 mt-0.5">JPG, PNG, WebP · Maks 10MB per foto</p>
        </div>

        <div v-if="uploadError" role="alert" class="alert alert-error py-2 text-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ uploadError }}</span>
        </div>

        <!-- Uploading indicator -->
        <div v-if="isUploading" class="flex items-center gap-2 text-xs text-base-content/60">
            <span class="loading loading-spinner loading-xs"></span>
            Mengunggah dan mengompres gambar...
        </div>

        <!-- Photo thumbnails -->
        <template v-if="photos.length > 0">
            <p class="text-xs text-base-content/50">{{ photos.length }} foto tersimpan</p>
            <div class="grid grid-cols-4 gap-2">
                <div v-for="(url, i) in photos" :key="i" class="relative group">
                    <img :src="url" class="w-full aspect-square object-cover rounded-lg border border-base-300">
                    <button
                        @click="removePhoto(i)"
                        type="button"
                        class="absolute top-0.5 right-0.5 btn btn-xs btn-circle btn-error opacity-0 group-hover:opacity-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>
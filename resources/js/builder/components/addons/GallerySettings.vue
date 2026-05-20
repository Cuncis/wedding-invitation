<script setup>
import { ref, computed } from 'vue';
import { useBuilderStore } from '../../store';

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
    <div class="mt-4 p-4 rounded-lg border-2 border-accent-300 bg-accent-50/40">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-lg">🖼️</span>
            <h3 class="text-sm font-bold text-slate-900">Pengaturan Galeri Foto</h3>
        </div>
        <p class="text-xs text-slate-600 mb-4">
            Unggah foto-foto untuk galeri. Gambar akan dikompres otomatis.
        </p>

        <!-- Layout options -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Kolom</label>
                <select v-model="columns"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md bg-white">
                    <option :value="2">2 Kolom</option>
                    <option :value="3">3 Kolom</option>
                    <option :value="4">4 Kolom</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tampilan</label>
                <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" v-model="lightbox" id="lightbox-toggle" class="accent-accent-500">
                    <label for="lightbox-toggle" class="text-sm text-slate-700">Aktifkan lightbox</label>
                </div>
            </div>
        </div>

        <!-- Upload area -->
        <div
            @drop="onDrop"
            @dragover.prevent
            @click="$refs.fileInput.click()"
            class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center cursor-pointer hover:border-rose-400 hover:bg-rose-50/20 transition">
            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                multiple
                @change="onFileInput"
                class="hidden">
            <p class="text-sm text-slate-600">
                <span class="text-rose-500 font-semibold">Klik atau drag foto</span> untuk upload
            </p>
            <p class="text-xs text-slate-400 mt-1">JPG, PNG, WebP • Maks 10MB per foto</p>
        </div>

        <p v-if="uploadError" class="text-xs text-rose-600 mt-2">{{ uploadError }}</p>

        <!-- Uploading indicator -->
        <div v-if="isUploading" class="flex items-center gap-2 mt-3 text-sm text-slate-600">
            <span class="animate-spin">⏳</span> Mengunggah dan mengompres gambar...
        </div>

        <!-- Photo count -->
        <p v-if="photos.length > 0" class="text-xs text-slate-500 mt-3">
            {{ photos.length }} foto tersimpan
        </p>

        <!-- Photo thumbnails -->
        <div v-if="photos.length > 0" class="mt-3 grid grid-cols-4 gap-2">
            <div v-for="(url, i) in photos" :key="i" class="relative group">
                <img :src="url" class="w-full h-16 object-cover rounded border border-slate-200">
                <button
                    @click="removePhoto(i)"
                    class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition">
                    ×
                </button>
            </div>
        </div>
    </div>
</template>
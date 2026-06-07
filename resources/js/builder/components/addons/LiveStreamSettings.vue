<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '../../store';

const store = useBuilderStore();

const ls = computed(() => store.config.content.live_stream);

function extractYoutubeId(raw) {
    if (!raw || typeof raw !== 'string') return null;
    const trimmed = raw.trim();
    if (/^[A-Za-z0-9_-]{11}$/.test(trimmed)) return trimmed;
    const patterns = [
        /youtu\.be\/([A-Za-z0-9_-]{11})/i,
        /[?&]v=([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/(?:embed|shorts|v|live)\/([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/live\/([A-Za-z0-9_-]{11})/i,
    ];
    for (const p of patterns) {
        const m = trimmed.match(p);
        if (m) return m[1];
    }
    return null;
}

const videoId = computed(() => {
    if (ls.value.provider !== 'youtube') return null;
    return extractYoutubeId(ls.value.url);
});

const urlStatus = computed(() => {
    if (!ls.value.url) return null;
    if (ls.value.provider === 'youtube') {
        return videoId.value
            ? { ok: true,  msg: `Terdeteksi: ${videoId.value}` }
            : { ok: false, msg: 'URL YouTube tidak dikenali. Coba salin dari address bar atau tombol Share.' };
    }
    return ls.value.url
        ? { ok: true, msg: 'Link kustom akan ditampilkan sebagai tombol.' }
        : null;
});

const thumbnailUrl = computed(() =>
    videoId.value ? `https://i.ytimg.com/vi/${videoId.value}/hqdefault.jpg` : null,
);
</script>

<template>
    <div class="rounded-xl border-2 border-primary/20 bg-primary/3 p-4 space-y-3">
        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-primary/15">
            <span class="inline-flex w-8 h-8 rounded-lg bg-primary/10 items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                    <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-base-content leading-tight">Live Streaming</p>
                <p class="text-xs text-base-content/50">YouTube Live atau link kustom</p>
            </div>
            <span class="badge badge-xs badge-primary badge-outline shrink-0">Pengaturan</span>
        </div>

        <!-- Heading & Description -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Judul Seksi</span></div>
            <input v-model="ls.heading" type="text" placeholder="Saksikan Secara Online"
                class="input input-sm input-bordered w-full">
        </label>
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Deskripsi</span></div>
            <textarea v-model="ls.description" rows="3" placeholder="Bagi tamu yang tidak dapat hadir..."
                class="textarea textarea-sm textarea-bordered w-full resize-none"></textarea>
        </label>

        <div class="divider my-0 text-xs text-base-content/40">Sumber Streaming</div>

        <!-- Provider -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Platform</span></div>
            <select v-model="ls.provider" class="select select-sm select-bordered w-full">
                <option value="youtube">YouTube Live</option>
                <option value="custom">Link Kustom (Zoom, Meet, dll.)</option>
            </select>
        </label>

        <!-- URL -->
        <label class="form-control w-full">
            <div class="label py-0.5">
                <span class="label-text text-xs">{{ ls.provider === 'youtube' ? 'URL YouTube Live' : 'URL Live Streaming' }}</span>
            </div>
            <input v-model="ls.url" type="url"
                :placeholder="ls.provider === 'youtube' ? 'https://youtu.be/xxxxx' : 'https://zoom.us/j/xxxxx'"
                class="input input-sm input-bordered w-full font-mono">
            <div v-if="urlStatus" class="label py-0.5">
                <span :class="['label-text-alt', urlStatus.ok ? 'text-success' : 'text-error']">{{ urlStatus.msg }}</span>
            </div>
        </label>

        <!-- YouTube thumbnail preview -->
        <div v-if="thumbnailUrl" class="rounded-lg overflow-hidden border border-base-300">
            <img :src="thumbnailUrl" alt="Preview" class="w-full h-28 object-cover">
            <p class="text-xs text-center text-base-content/40 py-1">Thumbnail video</p>
        </div>

        <div class="divider my-0 text-xs text-base-content/40">Jadwal</div>

        <!-- Schedule -->
        <div class="grid grid-cols-2 gap-3">
            <label class="form-control">
                <div class="label py-0.5"><span class="label-text text-xs">Tanggal Tayang</span></div>
                <input v-model="ls.start_date" type="date" class="input input-sm input-bordered w-full">
            </label>
            <label class="form-control">
                <div class="label py-0.5"><span class="label-text text-xs">Jam Mulai</span></div>
                <input v-model="ls.start_time" type="time" class="input input-sm input-bordered w-full">
            </label>
        </div>

        <!-- Button label (custom only) -->
        <label v-if="ls.provider === 'custom'" class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Label Tombol</span></div>
            <input v-model="ls.button_label" type="text" placeholder="Tonton Live Streaming"
                class="input input-sm input-bordered w-full">
        </label>
    </div>
</template>

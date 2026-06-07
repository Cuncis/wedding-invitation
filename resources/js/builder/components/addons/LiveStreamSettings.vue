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
    <div class="rounded-xl border-2 border-rose-200 bg-rose-50/30 p-4 space-y-4">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-base">📺</span>
            <h3 class="text-sm font-semibold text-slate-800">Live Streaming</h3>
        </div>

        <!-- Heading & Description -->
        <label class="block">
            <span class="text-xs font-medium text-slate-600">Judul Seksi</span>
            <input v-model="ls.heading" type="text"
                placeholder="Saksikan Secara Online"
                class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
        </label>
        <label class="block">
            <span class="text-xs font-medium text-slate-600">Deskripsi</span>
            <textarea v-model="ls.description" rows="3"
                placeholder="Bagi tamu yang tidak dapat hadir..."
                class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
        </label>

        <hr class="border-slate-200">

        <!-- Provider -->
        <label class="block">
            <span class="text-xs font-medium text-slate-600">Platform</span>
            <select v-model="ls.provider"
                class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                <option value="youtube">YouTube Live</option>
                <option value="custom">Link Kustom (Zoom, Meet, dll.)</option>
            </select>
        </label>

        <!-- URL -->
        <label class="block">
            <span class="text-xs font-medium text-slate-600">
                {{ ls.provider === 'youtube' ? 'URL YouTube Live' : 'URL Live Streaming' }}
            </span>
            <input v-model="ls.url" type="url"
                :placeholder="ls.provider === 'youtube' ? 'https://youtu.be/xxxxx atau https://youtube.com/live/xxxxx' : 'https://zoom.us/j/xxxxx'"
                class="mt-1 w-full rounded border-slate-300 text-sm font-mono focus:border-rose-500 focus:ring-rose-500">
            <p v-if="urlStatus"
                :class="['text-xs mt-1', urlStatus.ok ? 'text-emerald-600' : 'text-rose-500']">
                {{ urlStatus.msg }}
            </p>
        </label>

        <!-- YouTube thumbnail preview -->
        <div v-if="thumbnailUrl" class="rounded-lg overflow-hidden border border-slate-200">
            <img :src="thumbnailUrl" alt="Preview" class="w-full h-28 object-cover">
            <p class="text-xs text-center text-slate-400 py-1">Thumbnail video</p>
        </div>

        <hr class="border-slate-200">

        <!-- Schedule -->
        <div class="grid grid-cols-2 gap-3">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Tanggal Tayang</span>
                <input v-model="ls.start_date" type="date"
                    class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Jam Mulai</span>
                <input v-model="ls.start_time" type="time"
                    class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
            </label>
        </div>

        <!-- Button label (custom only) -->
        <label v-if="ls.provider === 'custom'" class="block">
            <span class="text-xs font-medium text-slate-600">Label Tombol</span>
            <input v-model="ls.button_label" type="text"
                placeholder="Tonton Live Streaming"
                class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
        </label>
    </div>
</template>

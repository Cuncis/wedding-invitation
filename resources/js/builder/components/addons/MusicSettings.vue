<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '../../store';

const store = useBuilderStore();

// Mirror of the server-side MusicService::extractYoutubeId — extracts a video
// id from any common URL shape so we can show a live preview thumbnail and
// validation feedback while the user is typing.
function extractYoutubeId(raw) {
    if (!raw || typeof raw !== 'string') return null;
    const trimmed = raw.trim();
    // Bare 11-char id
    if (/^[A-Za-z0-9_-]{11}$/.test(trimmed)) return trimmed;
    const patterns = [
        /youtu\.be\/([A-Za-z0-9_-]{11})/i,
        /[?&]v=([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/(?:embed|shorts|v)\/([A-Za-z0-9_-]{11})/i,
    ];
    for (const p of patterns) {
        const m = trimmed.match(p);
        if (m) return m[1];
    }
    return null;
}

// Two-way binding helper — keeps store.config.music.* reactive.
const music = computed({
    get: () => store.config.music ?? {},
    set: (v) => { store.config.music = v; },
});

const videoIdPreview = computed(() => {
    if (music.value.provider !== 'youtube') return null;
    return extractYoutubeId(music.value.url) || music.value.video_id || null;
});

const thumbnailUrl = computed(() =>
    videoIdPreview.value
        ? `https://i.ytimg.com/vi/${videoIdPreview.value}/hqdefault.jpg`
        : null,
);

const urlStatus = computed(() => {
    if (!music.value.url) return null;
    if (music.value.provider === 'youtube') {
        return videoIdPreview.value
            ? { ok: true,  msg: `Terdeteksi: ${videoIdPreview.value}` }
            : { ok: false, msg: 'URL YouTube tidak dikenali — coba salin link dari address bar atau tombol Share.' };
    }
    return null;
});
</script>

<template>
    <div class="mt-4 p-4 rounded-lg border-2 border-accent-300 bg-accent-50/40">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-lg">🎵</span>
            <h3 class="text-sm font-bold text-slate-900">Pengaturan Music Player</h3>
        </div>
        <p class="text-xs text-slate-600 mb-4">
            Tambahkan musik latar untuk undangan. Tempel URL YouTube apa saja —
            kami otomatis ekstrak video-nya.
        </p>

        <!-- Provider -->
        <label class="block text-xs font-semibold text-slate-700 mb-1">Sumber Musik</label>
        <select v-model="music.provider"
            class="w-full mb-3 px-3 py-2 text-sm border border-slate-300 rounded-md bg-white">
            <option value="youtube">YouTube</option>
            <option value="spotify"    disabled>Spotify (segera)</option>
            <option value="soundcloud" disabled>SoundCloud (segera)</option>
            <option value="upload"     disabled>Upload MP3 (segera)</option>
        </select>

        <!-- URL -->
        <label class="block text-xs font-semibold text-slate-700 mb-1">
            URL YouTube
        </label>
        <input v-model="music.url"
            type="url"
            placeholder="https://youtu.be/dQw4w9WgXcQ"
            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md font-mono">
        <p v-if="urlStatus"
            :class="['text-xs mt-1', urlStatus.ok ? 'text-emerald-600' : 'text-rose-600']">
            {{ urlStatus.msg }}
        </p>
        <p v-else class="text-xs text-slate-500 mt-1">
            Format yang didukung: <code>youtu.be/...</code>, <code>youtube.com/watch?v=...</code>,
            <code>youtube.com/embed/...</code>, atau ID 11-karakter langsung.
        </p>

        <!-- Thumbnail preview -->
        <div v-if="thumbnailUrl" class="mt-3 rounded-md overflow-hidden border border-slate-200 bg-slate-100">
            <img :src="thumbnailUrl" alt="Video preview" class="w-full h-32 object-cover">
        </div>

        <!-- Title / Artist -->
        <div class="grid grid-cols-2 gap-2 mt-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Lagu</label>
                <input v-model="music.title" type="text" placeholder="Perfect"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Artis</label>
                <input v-model="music.artist" type="text" placeholder="Ed Sheeran"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md">
            </div>
        </div>

        <!-- Options -->
        <div class="mt-4 space-y-2">
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" v-model="music.autoplay" class="accent-accent-500">
                Putar otomatis setelah tamu menekan tombol "Buka Undangan"
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" v-model="music.loop" class="accent-accent-500">
                Loop (ulang terus menerus)
            </label>
        </div>

        <!-- Start-at -->
        <div class="mt-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">
                Mulai dari detik ke- <span class="text-slate-400 font-normal">(opsional)</span>
            </label>
            <input v-model.number="music.start_at" type="number" min="0" step="1"
                class="w-32 px-3 py-2 text-sm border border-slate-300 rounded-md">
        </div>
    </div>
</template>

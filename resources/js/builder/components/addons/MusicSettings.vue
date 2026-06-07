<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '../../store';
import IconMusic from '../icons/IconMusic.vue';

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
    <div class="rounded-xl border-2 border-primary/20 bg-primary/3 p-4 space-y-3">
        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-primary/15">
            <span class="inline-flex w-8 h-8 rounded-lg bg-primary/10 items-center justify-center shrink-0">
                <IconMusic class="w-4 h-4 text-primary" />
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-base-content leading-tight">Music Player</p>
                <p class="text-xs text-base-content/50">Musik latar otomatis dari YouTube</p>
            </div>
            <span class="badge badge-xs badge-primary badge-outline shrink-0">Pengaturan</span>
        </div>

        <!-- Provider -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Sumber Musik</span></div>
            <select v-model="music.provider" class="select select-sm select-bordered w-full">
                <option value="youtube">YouTube</option>
                <option value="spotify"    disabled>Spotify (segera)</option>
                <option value="soundcloud" disabled>SoundCloud (segera)</option>
                <option value="upload"     disabled>Upload MP3 (segera)</option>
            </select>
        </label>

        <!-- URL -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">URL YouTube</span></div>
            <input v-model="music.url" type="url"
                placeholder="https://youtu.be/dQw4w9WgXcQ"
                class="input input-sm input-bordered w-full font-mono">
            <div class="label py-0.5">
                <span v-if="urlStatus" :class="['label-text-alt', urlStatus.ok ? 'text-success' : 'text-error']">{{ urlStatus.msg }}</span>
                <span v-else class="label-text-alt text-base-content/40">youtu.be/... · youtube.com/watch?v=... · ID 11-karakter</span>
            </div>
        </label>

        <!-- Thumbnail preview -->
        <div v-if="thumbnailUrl" class="rounded-lg overflow-hidden border border-base-300">
            <img :src="thumbnailUrl" alt="Video preview" class="w-full h-28 object-cover">
        </div>

        <!-- Title / Artist -->
        <div class="grid grid-cols-2 gap-2">
            <label class="form-control">
                <div class="label py-0.5"><span class="label-text text-xs">Judul Lagu</span></div>
                <input v-model="music.title" type="text" placeholder="Perfect"
                    class="input input-sm input-bordered w-full">
            </label>
            <label class="form-control">
                <div class="label py-0.5"><span class="label-text text-xs">Artis</span></div>
                <input v-model="music.artist" type="text" placeholder="Ed Sheeran"
                    class="input input-sm input-bordered w-full">
            </label>
        </div>

        <div class="divider my-0 text-xs text-base-content/40">Opsi</div>

        <!-- Options -->
        <div class="space-y-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="music.autoplay" class="checkbox checkbox-xs checkbox-primary">
                <span class="text-xs text-base-content">Putar otomatis saat tamu membuka undangan</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="music.loop" class="checkbox checkbox-xs checkbox-primary">
                <span class="text-xs text-base-content">Loop (ulang terus menerus)</span>
            </label>
        </div>

        <!-- Start-at -->
        <label class="form-control">
            <div class="label py-0.5">
                <span class="label-text text-xs">Mulai dari detik ke-</span>
                <span class="label-text-alt text-base-content/40">opsional</span>
            </div>
            <input v-model.number="music.start_at" type="number" min="0" step="1"
                class="input input-sm input-bordered w-28">
        </label>
    </div>
</template>

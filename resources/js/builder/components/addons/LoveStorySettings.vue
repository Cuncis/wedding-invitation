<script setup>
import { useBuilderStore } from '../../store';

const store = useBuilderStore();

function addItem() {
    store.config.content.love_story.items.push({
        date: '',
        title: '',
        description: '',
        photo: null,
    });
}

function removeItem(i) {
    store.config.content.love_story.items.splice(i, 1);
}

function moveUp(i) {
    if (i === 0) return;
    const items = store.config.content.love_story.items;
    [items[i - 1], items[i]] = [items[i], items[i - 1]];
}

function moveDown(i) {
    const items = store.config.content.love_story.items;
    if (i === items.length - 1) return;
    [items[i], items[i + 1]] = [items[i + 1], items[i]];
}
</script>

<template>
    <div class="rounded-xl border-2 border-primary/20 bg-primary/3 p-4 space-y-3">
        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-primary/15">
            <span class="inline-flex w-8 h-8 rounded-lg bg-primary/10 items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-base-content leading-tight">Love Story</p>
                <p class="text-xs text-base-content/50">Momen timeline perjalanan cinta</p>
            </div>
            <span class="badge badge-xs badge-primary badge-outline shrink-0">Pengaturan</span>
        </div>

        <!-- Heading & Intro -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Judul Seksi</span></div>
            <input v-model="store.config.content.love_story.heading" type="text"
                placeholder="Kisah Cinta Kami"
                class="input input-sm input-bordered w-full">
        </label>
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Kalimat Pengantar</span></div>
            <textarea v-model="store.config.content.love_story.intro" rows="2"
                placeholder="Sebuah perjalanan indah yang membawa kami..."
                class="textarea textarea-sm textarea-bordered w-full resize-none"></textarea>
        </label>

        <div class="divider my-0 text-xs text-base-content/40">Momen Timeline</div>

        <div v-if="store.config.content.love_story.items.length === 0"
            class="text-xs text-base-content/40 italic text-center py-2">
            Belum ada momen.
        </div>

        <div v-for="(item, i) in store.config.content.love_story.items" :key="i"
            class="bg-base-100 rounded-lg border border-base-300 p-3 space-y-2">

            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-primary">Momen #{{ i + 1 }}</span>
                <div class="flex items-center gap-1">
                    <button @click="moveUp(i)" :disabled="i === 0" type="button"
                        class="btn btn-xs btn-ghost btn-square disabled:opacity-30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                    </button>
                    <button @click="moveDown(i)" :disabled="i === store.config.content.love_story.items.length - 1" type="button"
                        class="btn btn-xs btn-ghost btn-square disabled:opacity-30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <button @click="removeItem(i)" type="button" class="btn btn-xs btn-error btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Hapus
                    </button>
                </div>
            </div>

            <input v-model="item.date" type="text" placeholder="Tanggal / Periode (mis. Maret 2019)"
                class="input input-xs input-bordered w-full">
            <input v-model="item.title" type="text" placeholder="Judul momen (mis. Pertama Bertemu)"
                class="input input-xs input-bordered w-full">
            <textarea v-model="item.description" rows="3" placeholder="Ceritakan momen ini..."
                class="textarea textarea-xs textarea-bordered w-full resize-none"></textarea>
            <input v-model="item.photo" type="url" placeholder="URL Foto (opsional)"
                class="input input-xs input-bordered w-full">
        </div>

        <button @click="addItem" type="button" class="btn btn-sm btn-primary btn-outline w-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Momen
        </button>
    </div>
</template>

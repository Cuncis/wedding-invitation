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
    <div class="rounded-xl border-2 border-rose-200 bg-rose-50/30 p-4 space-y-4">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-base">💕</span>
            <h3 class="text-sm font-semibold text-slate-800">Love Story</h3>
        </div>

        <!-- Heading & Intro -->
        <label class="block">
            <span class="text-xs font-medium text-slate-600">Judul Seksi</span>
            <input v-model="store.config.content.love_story.heading" type="text"
                placeholder="Kisah Cinta Kami"
                class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
        </label>
        <label class="block">
            <span class="text-xs font-medium text-slate-600">Kalimat Pengantar</span>
            <textarea v-model="store.config.content.love_story.intro" rows="2"
                placeholder="Sebuah perjalanan indah yang membawa kami..."
                class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
        </label>

        <hr class="border-slate-200">

        <!-- Timeline items -->
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-700">Momen Timeline</span>
            <button @click="addItem"
                class="text-xs px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded font-medium">
                + Tambah Momen
            </button>
        </div>

        <div v-if="store.config.content.love_story.items.length === 0"
            class="text-xs text-slate-400 italic text-center py-3">
            Belum ada momen. Klik "+ Tambah Momen" untuk memulai.
        </div>

        <div v-for="(item, i) in store.config.content.love_story.items" :key="i"
            class="bg-white rounded-lg border border-slate-200 p-3 space-y-2">

            <!-- Item header -->
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-rose-600">Momen #{{ i + 1 }}</span>
                <div class="flex items-center gap-1">
                    <button @click="moveUp(i)" :disabled="i === 0"
                        class="text-xs px-1.5 py-0.5 border border-slate-200 rounded text-slate-400 hover:text-slate-600 disabled:opacity-30">↑</button>
                    <button @click="moveDown(i)" :disabled="i === store.config.content.love_story.items.length - 1"
                        class="text-xs px-1.5 py-0.5 border border-slate-200 rounded text-slate-400 hover:text-slate-600 disabled:opacity-30">↓</button>
                    <button @click="removeItem(i)"
                        class="text-xs px-1.5 py-0.5 text-red-400 hover:text-red-600 border border-red-200 rounded">Hapus</button>
                </div>
            </div>

            <label class="block">
                <span class="text-xs font-medium text-slate-600">Tanggal / Periode</span>
                <input v-model="item.date" type="text"
                    placeholder="mis. Maret 2019"
                    class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Judul Momen</span>
                <input v-model="item.title" type="text"
                    placeholder="mis. Pertama Bertemu"
                    class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Cerita</span>
                <textarea v-model="item.description" rows="3"
                    placeholder="Ceritakan momen ini..."
                    class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">URL Foto (opsional)</span>
                <input v-model="item.photo" type="url"
                    placeholder="https://..."
                    class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
            </label>
        </div>
    </div>
</template>

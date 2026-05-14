<script setup>
import { useBuilderStore } from '../store';

const store = useBuilderStore();
</script>

<template>
    <div class="space-y-5">
        <h2 class="text-sm font-semibold text-slate-900">Konten Undangan</h2>

        <!-- Cover -->
        <details open class="rounded-lg border border-slate-200">
            <summary class="px-3 py-2 text-xs font-semibold text-slate-700 cursor-pointer">Cover</summary>
            <div class="p-3 space-y-3 border-t border-slate-200">
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Pembuka (mis. Bismillah...)</span>
                    <input v-model="store.config.content.cover.opening_text"
                        type="text"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Tagline</span>
                    <input v-model="store.config.content.cover.tagline"
                        type="text" placeholder="Save the Date"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
            </div>
        </details>

        <!-- Couple -->
        <details open class="rounded-lg border border-slate-200">
            <summary class="px-3 py-2 text-xs font-semibold text-slate-700 cursor-pointer">Mempelai</summary>
            <div class="p-3 space-y-3 border-t border-slate-200">
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Nama Lengkap Pria</span>
                    <input v-model="store.config.content.couple.groom_fullname" type="text"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Putra dari</span>
                    <input v-model="store.config.content.couple.groom_parents" type="text"
                        placeholder="Bpk. Ahmad &amp; Ibu Siti"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Nama Lengkap Wanita</span>
                    <input v-model="store.config.content.couple.bride_fullname" type="text"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Putri dari</span>
                    <input v-model="store.config.content.couple.bride_parents" type="text"
                        placeholder="Bpk. Hasan &amp; Ibu Rina"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
            </div>
        </details>

        <!-- Event - Akad -->
        <details class="rounded-lg border border-slate-200">
            <summary class="px-3 py-2 text-xs font-semibold text-slate-700 cursor-pointer">Akad Nikah</summary>
            <div class="p-3 grid grid-cols-2 gap-3 border-t border-slate-200">
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Tanggal</span>
                    <input v-model="store.config.content.event.akad.date" type="date"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Jam</span>
                    <input v-model="store.config.content.event.akad.time" type="time"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="col-span-2 block">
                    <span class="text-xs font-medium text-slate-600">Lokasi</span>
                    <input v-model="store.config.content.event.akad.venue" type="text"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="col-span-2 block">
                    <span class="text-xs font-medium text-slate-600">Alamat</span>
                    <input v-model="store.config.content.event.akad.address" type="text"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
            </div>
        </details>

        <!-- Event - Resepsi -->
        <details class="rounded-lg border border-slate-200">
            <summary class="px-3 py-2 text-xs font-semibold text-slate-700 cursor-pointer">Resepsi</summary>
            <div class="p-3 grid grid-cols-2 gap-3 border-t border-slate-200">
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Tanggal</span>
                    <input v-model="store.config.content.event.resepsi.date" type="date"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Jam</span>
                    <input v-model="store.config.content.event.resepsi.time" type="time"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="col-span-2 block">
                    <span class="text-xs font-medium text-slate-600">Lokasi</span>
                    <input v-model="store.config.content.event.resepsi.venue" type="text"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
                <label class="col-span-2 block">
                    <span class="text-xs font-medium text-slate-600">Alamat</span>
                    <input v-model="store.config.content.event.resepsi.address" type="text"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                </label>
            </div>
        </details>

        <!-- Closing -->
        <details class="rounded-lg border border-slate-200">
            <summary class="px-3 py-2 text-xs font-semibold text-slate-700 cursor-pointer">Penutup</summary>
            <div class="p-3 border-t border-slate-200">
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Pesan Penutup</span>
                    <textarea v-model="store.config.content.closing.thank_you" rows="3"
                        class="mt-1 w-full rounded border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                </label>
            </div>
        </details>
    </div>
</template>

<script setup>
import { useBuilderStore } from '../store';

const store = useBuilderStore();
</script>

<template>
    <div class="space-y-5">
        <div>
            <h2 class="text-sm font-semibold text-base-content mb-0.5">Konten Undangan</h2>
            <p class="text-xs text-base-content/50 mb-4">Lengkapi detail konten undangan Anda</p>
        </div>

        <!-- Cover -->
        <details open class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Cover</summary>
            <div class="p-4 space-y-3 border-t border-base-200">
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Pembuka (mis. Bismillah...)</span></div>
                    <input v-model="store.config.content.cover.opening_text"
                        type="text"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Tagline</span></div>
                    <input v-model="store.config.content.cover.tagline"
                        type="text" placeholder="Save the Date"
                        class="input input-sm input-bordered w-full">
                </label>
            </div>
        </details>

        <!-- Couple -->
        <details open class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Mempelai</summary>
            <div class="p-4 space-y-3 border-t border-base-200">
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Nama Lengkap Pria</span></div>
                    <input v-model="store.config.content.couple.groom_fullname" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Putra dari</span></div>
                    <input v-model="store.config.content.couple.groom_parents" type="text"
                        placeholder="Bpk. Ahmad &amp; Ibu Siti"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Nama Lengkap Wanita</span></div>
                    <input v-model="store.config.content.couple.bride_fullname" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Putri dari</span></div>
                    <input v-model="store.config.content.couple.bride_parents" type="text"
                        placeholder="Bpk. Hasan &amp; Ibu Rina"
                        class="input input-sm input-bordered w-full">
                </label>
            </div>
        </details>

        <!-- Event - Akad -->
        <details class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Akad Nikah</summary>
            <div class="p-4 grid grid-cols-2 gap-3 border-t border-base-200">
                <label class="form-control">
                    <div class="label py-0.5"><span class="label-text text-xs">Tanggal</span></div>
                    <input v-model="store.config.content.event.akad.date" type="date"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control">
                    <div class="label py-0.5"><span class="label-text text-xs">Jam</span></div>
                    <input v-model="store.config.content.event.akad.time" type="time"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control col-span-2">
                    <div class="label py-0.5"><span class="label-text text-xs">Lokasi</span></div>
                    <input v-model="store.config.content.event.akad.venue" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control col-span-2">
                    <div class="label py-0.5"><span class="label-text text-xs">Alamat</span></div>
                    <input v-model="store.config.content.event.akad.address" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
            </div>
        </details>

        <!-- Event - Resepsi -->
        <details class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Resepsi</summary>
            <div class="p-4 grid grid-cols-2 gap-3 border-t border-base-200">
                <label class="form-control">
                    <div class="label py-0.5"><span class="label-text text-xs">Tanggal</span></div>
                    <input v-model="store.config.content.event.resepsi.date" type="date"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control">
                    <div class="label py-0.5"><span class="label-text text-xs">Jam</span></div>
                    <input v-model="store.config.content.event.resepsi.time" type="time"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control col-span-2">
                    <div class="label py-0.5"><span class="label-text text-xs">Lokasi</span></div>
                    <input v-model="store.config.content.event.resepsi.venue" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
                <label class="form-control col-span-2">
                    <div class="label py-0.5"><span class="label-text text-xs">Alamat</span></div>
                    <input v-model="store.config.content.event.resepsi.address" type="text"
                        class="input input-sm input-bordered w-full">
                </label>
            </div>
        </details>

        <!-- Closing -->
        <details class="rounded-xl border-2 border-base-300">
            <summary class="px-3 py-2.5 text-xs font-bold text-base-content/60 uppercase tracking-wider cursor-pointer">Penutup</summary>
            <div class="p-4 border-t border-base-200">
                <label class="form-control w-full">
                    <div class="label py-0.5"><span class="label-text text-xs">Pesan Penutup</span></div>
                    <textarea v-model="store.config.content.closing.thank_you" rows="3"
                        class="textarea textarea-bordered textarea-sm w-full"></textarea>
                </label>
            </div>
        </details>
    </div>
</template>

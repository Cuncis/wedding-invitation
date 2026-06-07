<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '../../store';

const store = useBuilderStore();

const countdown = computed({
    get: () => store.config.countdown ?? { target_date: '', label: 'Menuju Hari Bahagia' },
    set: (v) => { store.config.countdown = v; },
});

const hasTargetDate = computed(() => !!countdown.value.target_date);
</script>

<template>
    <div class="rounded-xl border-2 border-primary/20 bg-primary/3 p-4 space-y-3">
        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-primary/15">
            <span class="inline-flex w-8 h-8 rounded-lg bg-primary/10 items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-base-content leading-tight">Countdown Timer</p>
                <p class="text-xs text-base-content/50">Hitungan mundur menuju hari H</p>
            </div>
            <span class="badge badge-xs badge-primary badge-outline shrink-0">Pengaturan</span>
        </div>

        <!-- Target Date -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Tanggal &amp; Waktu</span></div>
            <input v-model="countdown.target_date" type="datetime-local"
                class="input input-sm input-bordered w-full">
        </label>

        <!-- Label -->
        <label class="form-control w-full">
            <div class="label py-0.5"><span class="label-text text-xs">Label</span></div>
            <input v-model="countdown.label" type="text"
                placeholder="Menuju Hari Bahagia"
                class="input input-sm input-bordered w-full">
        </label>

        <!-- Enable toggle -->
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" v-model="countdown.enabled" class="checkbox checkbox-xs checkbox-primary" :checked="countdown.enabled !== false">
            <span class="text-xs text-base-content">Aktifkan countdown timer</span>
        </label>
    </div>
</template>
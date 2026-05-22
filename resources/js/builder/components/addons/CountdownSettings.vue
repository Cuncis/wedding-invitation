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
    <div class="mt-4 p-4 rounded-lg border-2 border-accent-300 bg-accent-50/40">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">⏱️</span>
            <h3 class="text-sm font-bold text-slate-900">Pengaturan Countdown</h3>
        </div>
        <p class="text-xs text-slate-600 mb-4">
            Tampilkan hitungan mundur menuju hari bahagia Anda.
        </p>

        <!-- Target Date -->
        <div class="mb-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal & Waktu</label>
            <input v-model="countdown.target_date" type="datetime-local"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md">
        </div>

        <!-- Label -->
        <div class="mb-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Label</label>
            <input v-model="countdown.label" type="text"
                placeholder="Menuju Hari Bahagia"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-md">
        </div>

        <!-- Show / Hide Toggle -->
        <div class="mt-4 space-y-2">
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" v-model="countdown.enabled" class="accent-rose-500" :checked="countdown.enabled !== false">
                Aktifkan countdown timer
            </label>
        </div>
    </div>
</template>
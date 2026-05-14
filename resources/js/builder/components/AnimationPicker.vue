<script setup>
import { useBuilderStore } from '../store';

defineProps({
    packs: { type: Array, required: true },
});

const store = useBuilderStore();
const formatRupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
</script>

<template>
    <div>
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Animasi</h2>
        <p class="text-xs text-slate-500 mb-4">Tingkatkan kesan undangan dengan transisi & efek.</p>

        <div class="space-y-3">
            <button v-for="pack in packs" :key="pack.id"
                type="button"
                @click="store.config.animation_pack_id = pack.id"
                :class="[
                    'w-full text-left p-4 rounded-lg border-2 transition',
                    store.config.animation_pack_id === pack.id
                        ? 'border-rose-500 bg-rose-50/30 ring-2 ring-rose-100'
                        : 'border-slate-200 hover:border-slate-300',
                ]">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-base font-bold text-slate-900">{{ pack.name }}</p>
                    <p :class="['text-base font-bold', pack.price > 0 ? 'text-rose-600' : 'text-emerald-600']">
                        {{ pack.price > 0 ? formatRupiah(pack.price) : 'GRATIS' }}
                    </p>
                </div>
                <p v-if="pack.description" class="text-xs text-slate-600 mb-2">{{ pack.description }}</p>
                <ul v-if="Array.isArray(pack.features) && pack.features.length" class="text-xs text-slate-500 space-y-0.5">
                    <li v-for="(feat, i) in pack.features" :key="i">✓ {{ feat }}</li>
                </ul>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '../store';

defineProps({
    checkoutUrl: { type: String, required: true },
});

const store = useBuilderStore();
const formatRupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

const total = computed(() => store.pricing?.total ?? 0);
const themePrice     = computed(() => store.pricing?.theme_price ?? 0);
const addonPrice     = computed(() => store.pricing?.addon_price ?? 0);
const animationPrice = computed(() => store.pricing?.animation_price ?? 0);

const themeName     = computed(() => store.pricing?.theme?.name ?? null);
const addons        = computed(() => store.pricing?.addons ?? []);
const animationName = computed(() => store.pricing?.animation?.name ?? null);
</script>

<template>
    <div class="p-5 sticky top-0">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Ringkasan Harga</h2>

        <div v-if="store.isPricing" class="text-xs text-slate-500 italic mb-4">
            Menghitung...
        </div>

        <div class="space-y-3 text-sm">
            <!-- Theme -->
            <div class="flex justify-between gap-2 pb-2 border-b border-slate-100">
                <div class="min-w-0">
                    <p class="text-xs text-slate-500">Tema</p>
                    <p class="font-medium text-slate-800 truncate">{{ themeName ?? '—' }}</p>
                </div>
                <p class="font-medium text-slate-900 whitespace-nowrap">{{ formatRupiah(themePrice) }}</p>
            </div>

            <!-- Addons -->
            <div class="pb-2 border-b border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Fitur Tambahan ({{ addons.length }})</p>
                <ul v-if="addons.length" class="space-y-1 mb-2">
                    <li v-for="addon in addons" :key="addon.id"
                        class="flex justify-between gap-2 text-xs text-slate-600">
                        <span class="truncate">{{ addon.name }}</span>
                        <span class="whitespace-nowrap">{{ formatRupiah(addon.price) }}</span>
                    </li>
                </ul>
                <p v-else class="text-xs text-slate-400 italic mb-2">Belum ada fitur dipilih</p>
                <p class="flex justify-between text-sm">
                    <span class="text-slate-600">Subtotal</span>
                    <span class="font-medium text-slate-900">{{ formatRupiah(addonPrice) }}</span>
                </p>
            </div>

            <!-- Animation -->
            <div class="flex justify-between gap-2 pb-2 border-b border-slate-100">
                <div class="min-w-0">
                    <p class="text-xs text-slate-500">Animasi</p>
                    <p class="font-medium text-slate-800 truncate">{{ animationName ?? '—' }}</p>
                </div>
                <p class="font-medium text-slate-900 whitespace-nowrap">{{ formatRupiah(animationPrice) }}</p>
            </div>

            <!-- Total -->
            <div class="flex items-baseline justify-between pt-1">
                <p class="text-sm font-semibold text-slate-900">Total</p>
                <p class="text-2xl font-bold text-rose-600">{{ formatRupiah(total) }}</p>
            </div>
        </div>

        <a :href="checkoutUrl"
            :class="[
                'mt-5 block w-full text-center py-3 px-4 rounded-lg font-semibold transition',
                total > 0
                    ? 'bg-rose-600 hover:bg-rose-700 text-white'
                    : 'bg-slate-200 text-slate-400 cursor-not-allowed pointer-events-none',
            ]">
            Lanjut ke Checkout
        </a>

        <p class="mt-3 text-xs text-slate-400 text-center">
            Harga otomatis terupdate saat Anda mengubah pilihan.
        </p>
    </div>
</template>

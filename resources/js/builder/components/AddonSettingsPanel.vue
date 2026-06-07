<script setup>
import { ref, computed } from 'vue';
import { useBuilderStore } from '../store';
import MusicSettings     from './addons/MusicSettings.vue';
import GallerySettings   from './addons/GallerySettings.vue';
import MapsSettings      from './addons/MapsSettings.vue';
import CountdownSettings from './addons/CountdownSettings.vue';
import GiftSettings      from './addons/GiftSettings.vue';
import LoveStorySettings from './addons/LoveStorySettings.vue';
import LiveStreamSettings from './addons/LiveStreamSettings.vue';

const props = defineProps({
    addons:      { type: Array,  required: true },
    checkoutUrl: { type: String, required: true },
});

const store       = useBuilderStore();
const priceDialog = ref(null);
const formatRupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

const selectedIds = computed(() => store.config.addon_ids ?? []);
const hasAddonKey = (key) =>
    props.addons.filter(a => selectedIds.value.includes(a.id)).some(a => a.key === key);

const isMusicSelected     = computed(() => hasAddonKey('music_player'));
const isGallerySelected   = computed(() => hasAddonKey('photo_gallery'));
const isMapsSelected      = computed(() => hasAddonKey('maps'));
const isCountdownSelected = computed(() => hasAddonKey('countdown'));
const isGiftSelected      = computed(() => hasAddonKey('digital_gift'));
const isLoveStorySelected = computed(() => hasAddonKey('love_story'));
const isLiveStreamSelected = computed(() => hasAddonKey('live_stream'));

const activeCount = computed(() =>
    [isMusicSelected, isGallerySelected, isMapsSelected, isCountdownSelected,
     isGiftSelected, isLoveStorySelected, isLiveStreamSelected]
    .filter(c => c.value).length,
);

const total          = computed(() => store.pricing?.total ?? 0);
const themePrice     = computed(() => store.pricing?.theme_price ?? 0);
const addonPrice     = computed(() => store.pricing?.addon_price ?? 0);
const animationPrice = computed(() => store.pricing?.animation_price ?? 0);
const themeName      = computed(() => store.pricing?.theme?.name ?? null);
const addonsList     = computed(() => store.pricing?.addons ?? []);
const animationName  = computed(() => store.pricing?.animation?.name ?? null);
</script>

<template>
    <div class="flex flex-col h-full">

        <!-- ── Scrollable settings area ── -->
        <div class="flex-1 overflow-y-auto p-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-base-content">Pengaturan Fitur</h2>
                <span class="badge badge-sm badge-primary">{{ activeCount }} aktif</span>
            </div>

            <div class="space-y-4">
                <MusicSettings      v-if="isMusicSelected" />
                <GallerySettings    v-if="isGallerySelected" />
                <MapsSettings       v-if="isMapsSelected" />
                <CountdownSettings  v-if="isCountdownSelected" />
                <GiftSettings       v-if="isGiftSelected" />
                <LoveStorySettings  v-if="isLoveStorySelected" />
                <LiveStreamSettings v-if="isLiveStreamSelected" />
            </div>
        </div>

        <!-- ── Sticky bottom bar ── -->
        <div class="shrink-0 border-t border-base-200 p-3 bg-base-100">
            <button
                type="button"
                @click="priceDialog.showModal()"
                class="btn btn-sm btn-outline btn-primary w-full gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                </svg>
                Lihat Total Harga
                <span v-if="total > 0" class="badge badge-xs badge-primary ml-auto font-semibold">
                    {{ formatRupiah(total) }}
                </span>
            </button>
        </div>

        <!-- ── Price modal ── -->
        <dialog ref="priceDialog" class="modal">
            <div class="modal-box max-w-sm">
                <!-- Close button -->
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </form>

                <h3 class="font-semibold text-base mb-4">Ringkasan Harga</h3>

                <div v-if="store.isPricing" class="text-xs text-base-content/50 italic mb-3">
                    Menghitung...
                </div>

                <div class="space-y-3 text-sm">
                    <!-- Theme -->
                    <div class="flex justify-between gap-2 pb-2 border-b border-base-200">
                        <div class="min-w-0">
                            <p class="text-xs text-base-content/50">Tema</p>
                            <p class="font-medium truncate">{{ themeName ?? '—' }}</p>
                        </div>
                        <p class="font-medium whitespace-nowrap shrink-0">{{ formatRupiah(themePrice) }}</p>
                    </div>

                    <!-- Addons -->
                    <div class="pb-2 border-b border-base-200">
                        <p class="text-xs text-base-content/50 mb-1">Fitur Tambahan ({{ addonsList.length }})</p>
                        <ul v-if="addonsList.length" class="space-y-1 mb-2">
                            <li v-for="addon in addonsList" :key="addon.id"
                                class="flex justify-between gap-2 text-xs text-base-content/70">
                                <span class="truncate">{{ addon.name }}</span>
                                <span class="whitespace-nowrap shrink-0">{{ formatRupiah(addon.price) }}</span>
                            </li>
                        </ul>
                        <p v-else class="text-xs text-base-content/40 italic mb-1">Belum ada fitur dipilih</p>
                        <div class="flex justify-between text-sm">
                            <span class="text-base-content/60">Subtotal</span>
                            <span class="font-medium">{{ formatRupiah(addonPrice) }}</span>
                        </div>
                    </div>

                    <!-- Animation -->
                    <div class="flex justify-between gap-2 pb-2 border-b border-base-200">
                        <div class="min-w-0">
                            <p class="text-xs text-base-content/50">Animasi</p>
                            <p class="font-medium truncate">{{ animationName ?? '—' }}</p>
                        </div>
                        <p class="font-medium whitespace-nowrap shrink-0">{{ formatRupiah(animationPrice) }}</p>
                    </div>

                    <!-- Total -->
                    <div class="flex items-baseline justify-between pt-1">
                        <p class="font-semibold">Total</p>
                        <p class="text-2xl font-bold text-primary">{{ formatRupiah(total) }}</p>
                    </div>
                </div>

                <div class="modal-action mt-5">
                    <a :href="checkoutUrl"
                        :class="['btn btn-primary w-full', total <= 0 ? 'btn-disabled' : '']">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                        Lanjut ke Checkout
                    </a>
                </div>
            </div>

            <!-- Backdrop closes modal -->
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

    </div>
</template>

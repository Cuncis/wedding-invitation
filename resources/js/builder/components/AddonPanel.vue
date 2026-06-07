<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '../store';

const props = defineProps({
    addons: { type: Array, required: true },
});

const store = useBuilderStore();
const formatRupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

const selectedIds = computed({
    get: () => store.config.addon_ids ?? [],
    set: (v) => { store.config.addon_ids = v; },
});

function toggle(addon) {
    const ids = [...selectedIds.value];
    const i = ids.indexOf(addon.id);
    if (i >= 0) ids.splice(i, 1);
    else ids.push(addon.id);
    selectedIds.value = ids;
}

const runningTotal = computed(() => {
    const total = props.addons
        .filter(a => selectedIds.value.includes(a.id))
        .reduce((sum, a) => sum + Number(a.price || 0), 0);
    return formatRupiah(total);
});

const grouped = computed(() => {
    const groups = {};
    for (const addon of props.addons) {
        const cat = addon.category || 'lainnya';
        groups[cat] ??= [];
        groups[cat].push(addon);
    }
    return groups;
});

const categoryLabels = {
    media:       'Media',
    interactive: 'Interaktif',
    social:      'Sosial',
    utility:     'Utilitas',
    lainnya:     'Lainnya',
};

</script>

<template>
    <div>
        <!-- ── Header ── -->
        <h2 class="text-sm font-semibold text-base-content mb-0.5">Pilih Fitur Tambahan</h2>
        <p class="text-xs text-base-content/50 mb-4">
            Subtotal addon:
            <span class="font-semibold text-primary">{{ runningTotal }}</span>
        </p>

        <!-- ── Addon selector list ── -->
        <div v-for="(items, category) in grouped" :key="category" class="mb-5">
            <h3 class="text-xs font-bold text-base-content/40 uppercase tracking-wider mb-2">
                {{ categoryLabels[category] || category }}
            </h3>
            <div class="space-y-2">
                <label v-for="addon in items" :key="addon.id"
                    :class="[
                        'flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition select-none',
                        selectedIds.includes(addon.id)
                            ? 'border-primary bg-primary/5 shadow-sm'
                            : 'border-base-300 bg-base-100 hover:border-primary/40',
                    ]">
                    <input type="checkbox"
                        :checked="selectedIds.includes(addon.id)"
                        @change="toggle(addon)"
                        class="checkbox checkbox-sm checkbox-primary mt-0.5 shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-base-content truncate">{{ addon.name }}</p>
                            <span class="badge badge-sm badge-outline badge-primary font-semibold whitespace-nowrap shrink-0">
                                {{ formatRupiah(addon.price) }}
                            </span>
                        </div>
                        <p v-if="addon.description" class="text-xs text-base-content/50 mt-0.5 leading-snug">
                            {{ addon.description }}
                        </p>
                        <span v-if="selectedIds.includes(addon.id)"
                            class="inline-flex items-center gap-1 mt-1.5 text-xs text-primary font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            Aktif · lihat pengaturan di kanan
                        </span>
                    </div>
                </label>
            </div>
        </div>

    </div>
</template>

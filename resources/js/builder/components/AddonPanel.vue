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
        <h2 class="text-sm font-semibold text-slate-900 mb-1">Pilih Fitur Tambahan</h2>
        <p class="text-xs text-slate-500 mb-3">Subtotal addon: <span class="font-semibold text-rose-600">{{ runningTotal }}</span></p>

        <div v-for="(items, category) in grouped" :key="category" class="mb-5">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ categoryLabels[category] || category }}</h3>
            <div class="space-y-2">
                <label v-for="addon in items" :key="addon.id"
                    :class="[
                        'flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition',
                        selectedIds.includes(addon.id)
                            ? 'border-rose-500 bg-rose-50/30'
                            : 'border-slate-200 hover:border-slate-300',
                    ]">
                    <input type="checkbox"
                        :checked="selectedIds.includes(addon.id)"
                        @change="toggle(addon)"
                        class="mt-1 accent-rose-500">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ addon.name }}</p>
                            <p class="text-sm font-medium text-rose-600 whitespace-nowrap">{{ formatRupiah(addon.price) }}</p>
                        </div>
                        <p v-if="addon.description" class="text-xs text-slate-500 mt-0.5">{{ addon.description }}</p>
                    </div>
                </label>
            </div>
        </div>
    </div>
</template>

import { defineStore } from 'pinia';
import { ref, watch } from 'vue';
import axios from 'axios';

function debounce(fn, ms) {
    let timer = null;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
}

const DEFAULT_CONFIG = {
    theme_id: null,
    animation_pack_id: null,
    addon_ids: [],
    sections: ['cover', 'couple', 'event'],
    colors: {
        primary:   '#c8756a',
        secondary: '#f5e6e0',
        accent:    '#8b4a42',
        text:      '#3d2820',
    },
    typography: {
        heading: 'Playfair Display',
        body:    'Lato',
    },
    content: {
        cover:   { opening_text: 'Bismillahirrahmanirrahim', tagline: 'Save the Date' },
        couple:  { groom_fullname: '', groom_parents: '', bride_fullname: '', bride_parents: '' },
        event:   {
            akad:    { date: '', time: '', venue: '', address: '' },
            resepsi: { date: '', time: '', venue: '', address: '' },
        },
        closing: { thank_you: '' },
    },
    music: null,
    maps:  null,
};

export const useBuilderStore = defineStore('builder', () => {
    const invitationId = ref(null);
    const invitation   = ref({});
    const config       = ref({ ...DEFAULT_CONFIG });
    const isDirty      = ref(false);
    const isSaving     = ref(false);
    const lastSavedAt  = ref(null);
    const pricing      = ref(null);
    const isPricing    = ref(false);
    const previewKey   = ref(0);
    let booted = false;

    function init(id, initialConfig, inv) {
        invitationId.value = id;
        invitation.value   = inv || {};

        // Deep merge defaults with whatever the server gave us.
        config.value = mergeDeep(structuredClone(DEFAULT_CONFIG), initialConfig || {});

        // Allow Vue to commit the assignment before we attach watchers,
        // otherwise the initial fill counts as "dirty".
        setTimeout(() => { booted = true; refreshPricing(); }, 0);
    }

    function mergeDeep(target, source) {
        for (const key in source) {
            if (source[key] && typeof source[key] === 'object' && ! Array.isArray(source[key])) {
                target[key] = mergeDeep(target[key] ?? {}, source[key]);
            } else if (source[key] !== undefined && source[key] !== null) {
                target[key] = source[key];
            }
        }
        return target;
    }

    const save = debounce(async () => {
        if (! invitationId.value) return;
        isSaving.value = true;
        try {
            await axios.put(`/builder/${invitationId.value}/config`, config.value);
            isDirty.value    = false;
            lastSavedAt.value = new Date();
            previewKey.value++;
        } catch (e) {
            console.error('Auto-save failed', e);
        } finally {
            isSaving.value = false;
        }
    }, 1500);

    async function refreshPricing() {
        isPricing.value = true;
        try {
            const { data } = await axios.get('/api/pricing', {
                params: {
                    theme_id:          config.value.theme_id,
                    addon_ids:         config.value.addon_ids,
                    animation_pack_id: config.value.animation_pack_id,
                },
            });
            pricing.value = data;
        } catch (e) {
            pricing.value = null;
        } finally {
            isPricing.value = false;
        }
    }

    const debouncedPricing = debounce(refreshPricing, 500);

    watch(config, () => {
        if (! booted) return;
        isDirty.value = true;
        save();
    }, { deep: true });

    watch(
        () => [config.value.theme_id, config.value.addon_ids, config.value.animation_pack_id],
        () => { if (booted) debouncedPricing(); },
        { deep: true },
    );

    return {
        invitationId,
        invitation,
        config,
        isDirty,
        isSaving,
        lastSavedAt,
        pricing,
        isPricing,
        previewKey,
        init,
        refreshPricing,
    };
});

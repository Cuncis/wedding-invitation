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
        primary: '#c8756a',
        secondary: '#f5e6e0',
        accent: '#8b4a42',
        text: '#3d2820',
    },
    typography: {
        heading: 'Playfair Display',
        body: 'Lato',
    },
    content: {
        cover: {
            opening_text: 'Bismillahirrahmanirrahim',
            tagline: 'Save the Date',
            heading: 'The Wedding of',
            groom_short: '',
            bride_short: '',
            date_text: '',
            guest_label: 'Kepada Yth.\nTamu Undangan',
            button_label: 'Buka Undangan',
            photo_1: null,
            photo_2: null,
        },
        doa: {
            heading: 'Doa & Pengantar',
            description: 'Bismillahirrahmanirrahim. Segala puji bagi Allah, Tuhan semesta alam. Dengan menyebut nama Allah Yang Maha Pengasih lagi Maha Penyayang.',
            overlay: 0.45,
        },
        couple: {
            intro_text: 'Tanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i, serta kerabat sekalian, untuk menghadiri acara pernikahan kami:',
            groom_fullname: '',
            groom_parents: '',
            groom_photo: null,
            groom_social: { instagram: '', facebook: '', tiktok: '', twitter: '' },
            bride_fullname: '',
            bride_parents: '',
            bride_photo: null,
            bride_social: { instagram: '', facebook: '', tiktok: '', twitter: '' },
        },
        event: {
            akad: {
                date: '', time: '', day: '', venue: '', address: '',
                maps_url: '', calendar_url: '',
            },
            resepsi: {
                date: '', time: '', day: '', venue: '', address: '',
                maps_url: '', calendar_url: '',
            },
        },
        gallery: {
            photos: [], // array of image URLs, max 30
        },
        gift: {
            receiver_name: '',
            address: '',
            banks: [],    // [{ name, logo, account_no, account_holder }]
            ewallets: [], // [{ provider, account_no, account_holder }]
        },
        love_story: {
            heading: 'Kisah Cinta Kami',
            intro: 'Sebuah perjalanan indah yang membawa kami ke hari yang paling berbahagia ini.',
            items: [], // [{ date, title, description, photo }]
        },
        live_stream: {
            heading: 'Saksikan Secara Online',
            description: 'Bagi tamu yang tidak dapat hadir secara langsung, kami mengundang Anda untuk menyaksikan momen bahagia kami secara live.',
            provider: 'youtube', // youtube | custom
            url: '',
            video_id: null,
            start_date: '',
            start_time: '',
            button_label: 'Tonton Live Streaming',
        },
        wishes: {
            heading: 'Ucapan & Doa Restu',
            footer: 'Hope to see you soon, Stay safe and healthy!',
        },
        closing: {
            heading: 'Terima Kasih',
            thank_you: 'Atas Kehadiran & Doa Restunya',
            watermark: 'Undangan Pernikahan Digital Created By Libradigital.id',
        },
        backgrounds: {
            use_global: false,
            global: null,
            cover: null,
            doa: null,
            couple: null,
            event: null,
            gallery: null,
            gift: null,
            wishes: null,
            closing: null,
        },
    },
    music: {
        provider: 'youtube',
        url: '',
        video_id: null,
        title: '',
        artist: '',
        autoplay: true,
        loop: true,
        start_at: 0,
    },
    maps: {
        address: '',
        embed_url: '',
        show_marker: true,
    },
    countdown: {
        target_date: '',
        label: 'Menuju Hari Bahagia',
        enabled: true,
    },
};

export const useBuilderStore = defineStore('builder', () => {
    const invitationId = ref(null);
    const invitation = ref({});
    const config = ref({ ...DEFAULT_CONFIG });
    const isDirty = ref(false);
    const isSaving = ref(false);
    const lastSavedAt = ref(null);
    const pricing = ref(null);
    const isPricing = ref(false);
    const previewKey = ref(0);
    let booted = false;

    function init(id, initialConfig, inv) {
        invitationId.value = id;
        invitation.value = inv || {};

        // Deep merge defaults with whatever the server gave us.
        config.value = mergeDeep(structuredClone(DEFAULT_CONFIG), initialConfig || {});

        // Allow Vue to commit the assignment before we attach watchers,
        // otherwise the initial fill counts as "dirty".
        setTimeout(() => { booted = true; refreshPricing(); }, 0);
    }

    function mergeDeep(target, source) {
        for (const key in source) {
            if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                target[key] = mergeDeep(target[key] ?? {}, source[key]);
            } else if (source[key] !== undefined && source[key] !== null) {
                target[key] = source[key];
            }
        }
        return target;
    }

    const save = debounce(async () => {
        if (!invitationId.value) return;
        isSaving.value = true;
        try {
            await axios.put(`/builder/${invitationId.value}/config`, config.value);
            isDirty.value = false;
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
                    theme_id: config.value.theme_id,
                    addon_ids: config.value.addon_ids,
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
        if (!booted) return;
        isDirty.value = true;
        save();
    }, { deep: true });

    watch(
        () => [config.value.theme_id, config.value.addon_ids, config.value.animation_pack_id],
        () => { if (booted) debouncedPricing(); },
        { deep: true },
    );

    watch(
        () => config.value.addon_ids,
        () => {
            if (!booted) return;
            isDirty.value = true;
            save();
            debouncedPricing();
        },
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

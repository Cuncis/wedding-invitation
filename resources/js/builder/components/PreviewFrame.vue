<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    src: { type: String, required: true },
    reloadKey: { type: Number, default: 0 },
});

const emit = defineEmits(['load']);

const iframeEl = ref(null);
defineExpose({ iframeEl, postToIframe });

function onIframeLoad() {
    emit('load');
}

function postToIframe(data) {
    const el = iframeEl.value;
    if (el?.contentWindow) {
        el.contentWindow.postMessage(data, '*');
    }
}

// Append a cache-buster query so the iframe reloads on auto-save.
const fullSrc = computed(() => {
    const sep = props.src.includes('?') ? '&' : '?';
    return `${props.src}${sep}_v=${props.reloadKey}`;
});
</script>

<template>
    <div class="flex flex-col h-full">
        <div class="flex items-center gap-2 px-3 py-2 bg-slate-300/60 rounded-t-lg">
            <span class="w-3 h-3 rounded-full bg-red-400"></span>
            <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
            <span class="w-3 h-3 rounded-full bg-green-400"></span>
            <span class="ml-3 text-xs text-slate-600 font-mono truncate">preview · live</span>
        </div>
        <iframe ref="iframeEl" :src="fullSrc" :key="reloadKey" @load="onIframeLoad"
            class="flex-1 w-full bg-white rounded-b-lg shadow-lg border-0" title="Live preview"
            sandbox="allow-same-origin allow-scripts">
        </iframe>
    </div>
</template>

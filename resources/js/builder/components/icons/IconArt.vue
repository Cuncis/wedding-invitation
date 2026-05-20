<script setup>
defineProps({
    width: { type: String, default: '24' },
    height: { type: String, default: '24' },
});
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="width" :height="height" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="32" cy="32" r="28" fill="currentColor" opacity="0.2"/>
        <path d="M32 16v32M16 32h32" stroke-linecap="round"/>
    </svg>
</template>
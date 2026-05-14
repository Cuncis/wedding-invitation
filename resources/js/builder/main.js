import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';
import App from './App.vue';
import { useBuilderStore } from './store';

const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
if (csrf) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}

const el = document.getElementById('builder-app');
if (! el) {
    throw new Error('#builder-app element not found');
}

const data = {
    invitationId:   parseInt(el.dataset.invitationId, 10),
    previewUrl:     el.dataset.previewUrl,
    checkoutUrl:    el.dataset.checkoutUrl,
    invitation:     JSON.parse(el.dataset.invitation || '{}'),
    config:         JSON.parse(el.dataset.config || '{}'),
    themes:         JSON.parse(el.dataset.themes || '[]'),
    addons:         JSON.parse(el.dataset.addons || '[]'),
    animationPacks: JSON.parse(el.dataset.animationPacks || '[]'),
};

const app = createApp(App, {
    invitation:     data.invitation,
    themes:         data.themes,
    addons:         data.addons,
    animationPacks: data.animationPacks,
    previewUrl:     data.previewUrl,
    checkoutUrl:    data.checkoutUrl,
});

const pinia = createPinia();
app.use(pinia);

const store = useBuilderStore();
store.init(data.invitationId, data.config, data.invitation);

app.mount('#builder-app');

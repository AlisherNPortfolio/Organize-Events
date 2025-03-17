import { createApp } from 'vue/dist/vue.esm-bundler.js';
import EventListComponent from './components/EventListComponent.vue';
import EventFormComponent from './components/EventFormComponent.vue';
import ParticipantsListComponent from './components/ParticipantsListComponent.vue';
import EventImagesComponent from './components/EventImagesComponent.vue';
import ResponsiveNavMenu from './components/ResponsiveNavMenu.vue';
import 'bootstrap';

const app = createApp({});

app.component('event-list', EventListComponent);
app.component('event-form', EventFormComponent);
app.component('participants-list', ParticipantsListComponent);
app.component('event-images', EventImagesComponent);
app.component('responsive-nav-menu', ResponsiveNavMenu);

app.mount('#app');

import './bootstrap';
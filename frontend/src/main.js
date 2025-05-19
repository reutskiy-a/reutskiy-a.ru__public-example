import { createApp } from 'vue';
import './css/style.css';
import App from './App.vue';
import { createPinia } from 'pinia';
import axios from 'axios';
import globals from "./config/globals.js";

const app = createApp(App);
const pinia = createPinia();
app.use(pinia);

import router from './router/index.js';
app.use(router);

app.mount('#app');

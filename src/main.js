import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'

import App from './App.vue'
import Materiales from '../components/Materiales.vue'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/', redirect: '/app'},
        {path: '/Materiales', component: Materiales},
    ]
})



const app = createApp(App);

app.use(router);

app.mount('#app');

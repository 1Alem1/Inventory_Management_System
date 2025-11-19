import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'

import App from './App.vue'
import materiales from '../components/Materiales.vue'
import login from '../components/login.vue'
import materialesTenico from '../components/materialesTenico.vue'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/', redirect: '/login'},
        {path: '/login', component: login},
        {path: '/materiales', component: materiales},
        {path: '/materialesTenico', component: materialesTenico},

    ]
})



const app = createApp(App);

app.use(router);

app.mount('#app');

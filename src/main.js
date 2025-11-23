import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../backend/authStore'

import App from './App.vue'
import materiales from '../components/materiales.vue'
import login from '../components/login.vue'
import materialesTecnico from '../components/materialesTecnico.vue'

const routes = [

    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        component: login,
        meta: { requiresAuth: false }
    },
    {
        path: '/materiales',
        component: materiales,
        meta: { requiresAuth: true, requiredRole: 'admin' }
    },
    {
        path: '/materialesTecnico',
        component: materialesTecnico,
        meta: { requiresAuth: true, requiredRole: 'tecnico' }
    }
]





const router = createRouter({
    history: createWebHistory(),    
    routes
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    if (authStore.isLoading) {
        await authStore.checkSession();
    }

    const requiresAuth = to.meta.requiresAuth;
    const requiredRole = to.meta.requiredRole;

    if (requiresAuth) {
        if (!authStore.isAuthenticated) {
            next('/login');
            return;
        }

        if (requiredRole && !(authStore.userRole && authStore.userRole.value === requiredRole)) {
            if (authStore.userRole && authStore.userRole.value === 'admin') {
                next('/materiales');
            } else if (authStore.userRole && authStore.userRole.value === 'tecnico') {
                next('/materialesTecnico');
            } else {
                next('/login');
            }
            return;
        }

        next();
    } else {
        if (authStore.isAuthenticated && to.path === '/login') {
            if (authStore.userRole && authStore.userRole.value === 'admin') {
                next('/materiales');
            } else if (authStore.userRole && authStore.userRole.value === 'tecnico') {
                next('/materialesTecnico');
            } else {
                next();
            }
        } else {
            next();
        }
    }
});

export default router;




const app = createApp(App);

app.use(router);

app.mount('#app');

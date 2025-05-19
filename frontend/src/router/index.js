import {createRouter, createWebHistory} from "vue-router";
import {useAuthStore} from "../stores/authStore.js";
import globals from "../config/globals.js";


const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            component: () => import('../views/Posts.vue'),
            name: 'Посты',
            meta: { requiresAuth: false, menu: true }
        },
        {
            path: '/personal',
            component: () => import('../views/Personal.vue'),
            name: 'Personal',
            meta: { requiresAuth: false, menu: true }
        },
        {
            path: '/profile',
            component: () => import('../views/Profile.vue'),
            name: 'Профиль',
            meta: { requiresAuth: true, menu: true }
        },
        {
            path: '/login',
            component: () => import('../views/Login.vue'),
            name: 'Вход',
            meta: { requiresAuth: false, menu: true }
        },
        {
            path: '/registration',
            component: () => import('../views/Registration.vue'),
            name: 'Регистрация',
            meta: { requiresAuth: false, menu: true }
        },
        {
            path: '/password-reset',
            component: () => import('../views/PasswordReset.vue'),
            name: 'Сброс-пароля',
            meta: { requiresAuth: false, menu: false }
        },

        // 404 PAGE NOT FOUND
        {
            path: '/:pathMatch(.*)*',
            component: () => import('../views/404.vue'),
            name: '404',
            meta: { requiresAuth: false, menu: true }
        }
    ]
});

router.beforeEach( async (to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && ! authStore.isAuthenticated)  {
        return next({name: globals.PAGE_LOGIN});
    }

    if ((to.name === globals.PAGE_LOGIN || to.name === 'Регистрация') && authStore.isAuthenticated) {
    }

    if (to.name === 'Personal' && authStore.user.role !== 'admin') {
        return next({name: globals.PAGE_WHEN_AUTHENTICATED});
    }

    next();
});

export default router;

import { ref, watchEffect, computed, onMounted, onBeforeUnmount } from 'vue';
import { defineStore } from 'pinia';
import { refreshToken } from "../api/refreshTokenService.js";
import router from "../router/index.js";
import axios from "axios";
import globals from "../config/globals.js";

export const useAuthStore = defineStore('auth',  () => {

    const accessToken = ref(localStorage.getItem(globals.TOKEN_KEY_NAME) || null);
    const isAuthenticated = computed(() => {return !! accessToken.value})
    const user = ref(me());

    async function getUserData() {
        return await axios.get(`${globals.API_DOMAIN}/api/auth/users/me`, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${accessToken.value}`
            }
        });
    }


    function resetUser() {
        user.value = {
            name: null,
            role: null
        }
    }

    async function setAccessToken(token) {
        accessToken.value = token;
        await me();
    }

    function deleteAccessToken() {
        resetUser();
        accessToken.value = null;
    }

    watchEffect(() => {
       if (accessToken.value) {
           localStorage.setItem(globals.TOKEN_KEY_NAME, accessToken.value);
       }

        if (! accessToken.value) {
            localStorage.removeItem(globals.TOKEN_KEY_NAME);
        }
    });

    async function login(email, password) {
        try {
            const response = await axios.post(`${globals.API_DOMAIN}/api/auth/login`, {
                email: email,
                password: password
            });

            await setAccessToken(response.data.access_token);

            await router.push({ name: globals.PAGE_WHEN_AUTHENTICATED });
        } catch (e) {
            throw e;
        }
    }

    async function logout() {
        await me();

        await axios.post(`${globals.API_DOMAIN}/api/auth/logout`, {}, {
            headers: {'Content-Type': 'application/json', 'Authorization': `Bearer ${accessToken.value}`}
        });

        deleteAccessToken();
        await router.push({ name: globals.PAGE_WHEN_LOGOUT });
    }

    async function me() {
        if (! isAuthenticated) {
            resetUser();
            return;
        }

        try {
            const response = await getUserData();

            user.value = {
                name: response.data.name,
                role: response.data.role
            }

        } catch (error) {

            if (error.status === 401 && error.response.data.message === 'Token has expired')  {
                try {
                    const newToken = await refreshToken(accessToken.value);
                    await setAccessToken(newToken);
                } catch(refreshError) {
                    if (refreshError.status === 500 && refreshError.response.data.message === 'The token has been blacklisted') {
                        deleteAccessToken();
                        await router.push({name: globals.PAGE_LOGIN});
                    }
                }
            }

        }
    }

    function handleStorageEvent(event) {
        if (event.key === globals.TOKEN_KEY_NAME) {

            if (event.newValue === null) {
                deleteAccessToken();
                router.push({ name: globals.PAGE_WHEN_LOGOUT });
            }

            if (event.newValue !== accessToken.value) {
                setAccessToken(event.newValue);
            }

        }
    }

    onMounted(() => {
        window.addEventListener('storage', handleStorageEvent);
    });

    onBeforeUnmount(() => {
        window.removeEventListener('storage', handleStorageEvent);
    });

    return {
        accessToken,
        isAuthenticated,
        setAccessToken,
        deleteAccessToken,
        user,
        login,
        logout,
        me
    }

});

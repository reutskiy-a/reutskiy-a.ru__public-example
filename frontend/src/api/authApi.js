import axios from "axios";
import router from "../router/index.js";
import { useAuthStore } from "../stores/authStore.js";
import globals from "../config/globals.js";
import {refreshToken} from "./refreshTokenService.js";

export function getApi() {
    const api = axios.create({
        baseURL: globals.API_DOMAIN
    });
    const authStore = useAuthStore();

    api.interceptors.request.use(function (config) {
        if (authStore.isAuthenticated) {
            config.headers.authorization = `Bearer ${authStore.accessToken}`;
        }
        return config;
    }, function (error) {
        return Promise.reject(error);
    });

    api.interceptors.response.use(response => {
        return response;
    }, async error => {

        if (error.response.status === 401 && error.response.data.message === 'Token has expired') {

                try {
                    const newToken = await refreshToken(authStore.accessToken);
                    authStore.setAccessToken(newToken);
                    error.config.headers.authorization = `Bearer ${authStore.accessToken}`;
                    return api.request(error.config);
                } catch(refreshError) {
                    if (refreshError.status === 500 && refreshError.response.data.message === 'The token has been blacklisted') {
                        authStore.deleteAccessToken();
                        await router.push({name: globals.PAGE_LOGIN});
                    }
                }

        }

        // if (error.response.status === 401 && error.response.data.message === 'Token not provided') {
        //     authStore.deleteAccessToken();
        //     router.push({ name: globals.PAGE_LOGIN });
        // }

        return Promise.reject(error);
    });

    return api;
}

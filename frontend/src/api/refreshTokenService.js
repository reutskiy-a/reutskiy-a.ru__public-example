import axios from "axios";
import globals from "../config/globals.js";

let refreshPromise = null;

export async function refreshToken(token) {

    if (refreshPromise) {
        const response = await refreshPromise;
        return response.data.access_token;
    }

    try {
        refreshPromise = axios.post(`${globals.API_DOMAIN}/api/auth/refresh`, {}, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })

        const response =  await refreshPromise;
        return response.data.access_token;
    } catch(error) {
        throw error;
    } finally {
        refreshPromise = null;
    }

}
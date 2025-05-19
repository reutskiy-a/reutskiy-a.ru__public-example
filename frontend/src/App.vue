<script setup>
import {getApi} from "./api/authApi.js";
import router from './router/index.js';
import {ref, onMounted, computed} from "vue";
import {useAuthStore} from "./stores/authStore.js";
import globals from "./config/globals.js";

const authStore = useAuthStore();
const accessToken = ref(null);
const api = getApi();

const isMenuOpen = ref(false);

function toggleMenu() {
  isMenuOpen.value = !isMenuOpen.value;
}

onMounted(() => {
  getAccessToken();
});

function getAccessToken() {
  accessToken.value = authStore.accessToken;
}

async function logout() {
  await authStore.logout();
}

const filteredRoutes = computed(() => {
  return router.options.routes.filter((route) => {
    if (!route.meta?.menu) return false; // Показываем только маршруты с meta.menu
    if (route.meta.requiresAuth === true && !authStore.isAuthenticated) return false; // Скрываем для неавторизованных
    if (route.name === 'Вход' && authStore.isAuthenticated) return false; // Скрываем Login для авторизованных
    if (route.name === 'Регистрация' && authStore.isAuthenticated) return false; // Скрываем Registration для авторизованных
    if (route.name === 'Personal' && authStore.user.role !== 'admin') return false;
    if (route.name === '404') return false;
    return true;
  });
});

</script>

<template>
  <nav class="w-full flex-shrink-0">
    <div class="flex flex-col w-full mx-auto px-2 py-4">

      <!-- Кнопка бургер-меню -->
      <button @click="toggleMenu" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm
        text-white rounded-lg sm:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 bg-gray-700
        focus:bg-(--emerald-700) absolute right-2 top-2">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
      </button>

      <div class="flex items-center justify-end mb-4">

        <!-- Ссылки на соц.сети -->
        <div class="flex justify-end gap-2 mr-4 min-w-[40px] grow-7">
          <a href="https://github.com/reutskiy-a" target="_blank">
            <img id="social_link" class="" src="./assets/github_logo_white.svg" alt="Rounded avatar">
          </a>
          <a href="https://t.me/reutskiy_a" target="_blank">
            <img id="social_link" class="" src="./assets/tg_logo.svg" alt="Rounded avatar">
          </a>
          <a href="https://vk.com/reutskiy_a" target="_blank">
            <img id="social_link" class="" src="./assets/vk_logo.svg" alt="Rounded avatar">
          </a>
        </div>

        <!-- Меню -->
        <div class="grow-7">
          <div :class="{'hidden': !isMenuOpen, 'block': isMenuOpen}"
               class="w-full sm:block sm:w-auto bg-gray-200 rounded-xl px-4 py-2">
            <ul class="flex flex-col flex-wrap p-4 sm:p-0 mt-4 font-medium rounded-lg bg-gray-200
                sm:flex-row sm:space-x-8 sm:mt-0 sm:border-0 sm:bg-transparent justify-end">
              <li v-for="route in filteredRoutes" :key="route.path">
                <router-link
                    :to="route.path"
                    @click="isMenuOpen = false"
                    class="block py-2 px-2! text-gray-900! rounded hover:underline sm:hover:bg-transparent
                      sm:border-0 sm:hover:text-gray-900 sm:p-0 text-white sm:bg-transparent"
                    active-class="bg-teal-700! text-white!"
                >
                  {{ route.name }}
                </router-link>
              </li>
              <li v-if="authStore.isAuthenticated">
                <a
                    href="#"
                    @click.prevent="logout()"
                    class="block py-2 px-3 text-gray-900 rounded hover:underline sm:hover:bg-transparent sm:border-0 sm:p-0"
                >
                  Выход
                </a>
              </li>
            </ul>
          </div>
        </div>

      </div>

      <!-- Логотип -->
        <div class="flex items-start justify-center gap-2">
          <img class="w-20 h-20 rounded-full" src="./assets/logo_me.jpg" alt="Rounded avatar">
          <div class="flex flex-col w-full max-w-[530px] p-4 border border-gray-500 rounded-e-xl rounded-es-xl bg-gray-700">
            <p class="text-sm font-normal text-gray-900 text-white">
              <span class="block">Привет! Меня зовут Артём.</span>
              <span class="block">Делаю приложения и интеграции для Битрикс24(облако), МойСклад</span>
              <span class="block">Стэк: php, laravel, js, html, css, vue, docker, git</span>
            </p>
          </div>
        </div>

    </div>
  </nav>


  <main class="flex flex-col flex-1 px-2 w-full
    items-justify-center items-center place-items-center">
    <router-view></router-view>
  </main>

  <footer class="flex-shrink-0 text-white py-4 text-center">
    <p>заряжено laravel + vue + tailwind ❤️</p>
  </footer>
</template>

<style scoped>

#social_link {
  width: 40px;
}

</style>

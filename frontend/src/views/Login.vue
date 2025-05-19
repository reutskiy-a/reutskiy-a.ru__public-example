<script setup>
import ButtonBase from "../components/ButtonBase.vue";
import axios from 'axios';
import {useAuthStore} from "../stores/authStore.js";
import {ref, reactive} from 'vue';
import globals from "../config/globals.js";
import router from "../router/index.js";
import ErrorList from "../components/ErrorList.vue";
import Toast from "../components/Toast.vue";

const authStore = useAuthStore();
const email = ref(null);
const password = ref(null);
const loginError = ref(null);
const resetFormOpened = ref(false);
const isLoading = ref(false);
const restoreEmail = ref(null);

const toastObject = ref(null);
const toastMessage = ref('');
const errors = ref(null);

async function login() {
  isLoading.value = true;

  try {
    await authStore.login(email.value, password.value);
  } catch(error) {
    if (error.status === 401) {
      loginError.value = error.response.data.error
    }
  }

  isLoading.value = false;
}

async function passwordReset() {
  isLoading.value = true;

  try {
    errors.value = null;

    const response = await axios.post(`${globals.API_DOMAIN}/api/auth/password/forgot`, {
      email: restoreEmail.value
    });

    toastMessage.value = "Ссылка на восстановление отправлена";
    toastObject?.value.show();
    restoreEmail.value = null;
  } catch (e) {
    if (e.status === 422) {
      errors.value = e.response.data.errors
    }

    if (e.status === 429) {
      errors.value = {message: e.response.data.message};
    }
  }

  isLoading.value = false;
}

</script>

<template>

  <Toast ref="toastObject" :message="toastMessage" type="success"/>
  <h1 class="m-4 font-extrabold text-white text-3xl text-shadow text-center">Вход</h1>

  <div class="w-full max-w-130 rounded-sm p-5 shadow-(--shadow-3xl) bg-(--input-bg-color-form)">
    <form @submit.prevent="login()" class="flex flex-col mb-8">
      <div class="relative mb-3">
        <input v-model="email" type="email" id="email" placeholder="example@email.com"
               class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
        />
        <label for="email" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-2 scale-95 top-1 px-2">Почта</label>
      </div>
      <div class="relative mb-3">
        <input v-model="password" type="password" id="password" placeholder="••••••••"
               class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
        />
        <label for="password" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-2 scale-95 top-1 px-2">Пароль</label>
      </div>
      <div v-if="loginError" class="text-(--danger-color)">
        <div>{{ loginError }}</div>
      </div>
      <div class="text-right">
        <ButtonBase variant="primary" type="submit" :disabled="isLoading"> Войти </ButtonBase>
      </div>
    </form>

    <div>
      <a href="#" @click.prevent="resetFormOpened = ! resetFormOpened" class="text-(--text-accent-color)">Восстановить пароль</a>
      <form v-if="resetFormOpened" @submit.prevent="passwordReset()"
        class="flex items-center pt-3 mb-2">
        <div class="relative">
          <input v-model="restoreEmail" type="email" id="restore_email" placeholder="example@email.com"
                 class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
          />
          <label for="restore_email" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-2 scale-95 top-1 px-2">Почта</label>
        </div>
        <ButtonBase class="ml-2" type="submit" :disabled="isLoading">></ButtonBase>
      </form>
      <div v-if="errors?.email">
        <ErrorList :errors="errors?.email"></ErrorList>
      </div>
    </div>

    <div v-if="errors?.message" class="text-sm text-(--danger-color)">{{ errors.message}}</div>

  </div>
</template>

<style scoped>

</style>
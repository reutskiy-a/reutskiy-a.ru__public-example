<script setup>
import ErrorList from '../components/ErrorList.vue';
import axios from 'axios';
import globals from '../config/globals.js';
import ButtonBase from "../components/ButtonBase.vue";
import {useAuthStore} from "../stores/authStore.js";
import router from "../router/index.js";
import { ref, reactive } from 'vue';
import Toast from "../components/Toast.vue";

const name = ref(null);
const email = ref(null);
const password = ref(null);
const passwordConfirmation = ref(null);
const isLoading = ref(false);
const errors = ref(null);
const authStore = useAuthStore();

const toastObject = ref(null);
const toastMessage = ref('');

async function storeUser() {
  isLoading.value = true;

  try {
    const response = await axios.post(`${globals.API_DOMAIN}/api/users`, {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });

    await authStore.setAccessToken(response.data.access_token);

    toastMessage.value = 'Done';
    toastObject.value?.show();

    router.push({ name: globals.PAGE_WHEN_AUTHENTICATED});
  } catch (error) {
    if (error.status === 422) {
      errors.value = error.response.data.errors
    }
  }

  isLoading.value = false;
}
</script>

<template>
  <Toast ref="toastObject" :message="toastMessage" type="success"/>
  <h1 class="m-4 font-extrabold text-white text-3xl text-shadow text-center">Регистрация</h1>

  <div class="w-full max-w-130 rounded-sm p-5 shadow-(--shadow-3xl) bg-(--input-bg-color-form)">
    <form @submit.prevent="storeUser()" class="flex flex-col">
      <div class="relative mb-3">
        <input v-model="name" type="text" id="name" placeholder="Имя"
            class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
        />
        <label for="name" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-2 scale-95 top-1 px-2">Имя</label>
        <div v-if="errors?.name">
          <ErrorList :errors = errors.name></ErrorList>
        </div>
      </div>
      <div class="relative mb-3">
        <input v-model="email" type="email" id="email" placeholder="example@email.com"
               class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
        />
        <label for="email" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-2 scale-95 top-1 px-2">Почта</label>
        <div v-if="errors?.email">
          <ErrorList :errors = errors.email></ErrorList>
        </div>
      </div>
      <div class="relative mb-3">
        <input v-model="password" type="password" id="password" placeholder="••••••••"
               class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
        />
        <label for="password" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-2 scale-95 top-1 px-2">Пароль</label>
        <div v-if="errors?.password">
          <ErrorList :errors = errors.password></ErrorList>
        </div>
      </div>
      <div class="relative mb-3">
        <input v-model="passwordConfirmation" type="password" id="password_confirmation" placeholder="••••••••"
               class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
        />
        <label for="password" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-1 scale-95 top-1 px-2">Подтверждение пароля</label>
        <div v-if="errors?.password_confirmation">
          <ErrorList :errors = errors.password_confirmation></ErrorList>
        </div>
      </div>
      <div class="text-right">
        <ButtonBase variant="primary" type="submit" :disabled="isLoading"> Зарегистироваться </ButtonBase>
      </div>
    </form>
  </div>

</template>

<style scoped>

</style>
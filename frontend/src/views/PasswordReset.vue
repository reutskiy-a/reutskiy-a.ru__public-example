<script setup>
import ErrorList from "../components/ErrorList.vue";
import ButtonBase from "../components/ButtonBase.vue";
import globals from "../config/globals.js";
import axios from "axios";
import {ref} from 'vue';
import Toast from "../components/Toast.vue";
import router from "../router/index.js";

const password = ref(null);
const passwordConfirmation = ref(null);

const toastObject = ref(null);
const toastMessage = ref('');
const errors = ref(null);

const urlParams = new URLSearchParams(window.location.search);
const isLoading = ref(false);

async function setPassword() {
  errors.value = null;

  isLoading.value = true;

  try {
    const response = await axios.post(`${globals.API_DOMAIN}/api/auth/password/reset`, {
      password: password.value,
      password_confirmation: passwordConfirmation.value,
      token: urlParams.get('token'),
      email: urlParams.get('email')
    });

    if (response.data.data === true) {
      password.value = null;
      passwordConfirmation.value = null;

      router.push({name: globals.PAGE_LOGIN});
    }
  } catch(e) {
    if (e.status === 422) {
      errors.value = e.response.data.errors;
    }
  }

  isLoading.value = false;
}


</script>

<template>

  <Toast ref="toastObject" :message="toastMessage" type="success"/>
  <h1 class="m-4 font-extrabold text-white text-3xl text-shadow text-center">Восстановление пароля</h1>

  <div class="w-full max-w-130 rounded-sm p-5 shadow-(--shadow-3xl) bg-(--input-bg-color-form)">
    <p class="mb-6">Укажите новый пароль</p>
    <form @submit.prevent="setPassword()">
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
      <div v-if="errors?.update" class="text-sm text-(--danger-color)">
        {{ errors.update }}
      </div>
      <div class="text-right">
        <ButtonBase variant="primary" type="submit" :disabled="isLoading"> Сменить пароль </ButtonBase>
      </div>
    </form>

  </div>
</template>

<style scoped>

</style>
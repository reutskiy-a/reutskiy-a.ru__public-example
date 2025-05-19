<script setup>
import { ref, onMounted, reactive } from 'vue';
import ButtonBase from "../components/ButtonBase.vue";
import {useAuthStore} from "../stores/authStore.js";
import ErrorList from "../components/ErrorList.vue";
import { getApi } from "../api/authApi.js";
import Toast from "../components/Toast.vue";


const api = getApi();
const authStore = useAuthStore();

const toastMessage = ref('');
const toastObject = ref(null);

const nameEditMode = ref(false);
const newName = ref(null);

const passwordEditMode = ref(false);
const password = ref(null);
const newPassword = ref(null);
const newPasswordConfirmation = ref(null);

const isLoading = ref(false);
const updateErrors = ref(null);

function toggleNameEditMode() {
  newName.value = authStore.user.name;
  nameEditMode.value = ! nameEditMode.value;
  if (! nameEditMode.value) {
    updateErrors.value = null;
  }
}

function togglePasswordEditMode() {
  passwordEditMode.value = ! passwordEditMode.value;
  if (! passwordEditMode.value) {
    passwordEditMode.value = null;
    password.value = null;
    newPassword.value = null;
    newPasswordConfirmation.value = null;
  }
}

async function updateName() {
  try {
    const response = await api.patch('api/auth/user', {
      name: newName.value
    });

    if (response.data.data === true) {
      await authStore.me();
      nameEditMode.value = false;
      newName.value = null;
      updateErrors.value = null;

      toastMessage.value = 'Имя изменено'
      toastObject.value.show();
    }

  } catch(e) {
    if (e.status === 422) {
      updateErrors.value = e.response.data.errors;
    }
  }
}

async function updatePassword() {
  try {
    const response = await api.patch('api/auth/user', {
      password: password.value,
      new_password: newPassword.value,
      new_password_confirmation: newPasswordConfirmation.value,
    });

    if (response.data.data === true) {
      passwordEditMode.value = false;
      password.value = null;
      newPassword.value = null;
      newPasswordConfirmation.value = null;
      updateErrors.value = null;

      toastMessage.value = 'Пароль изменен'
      toastObject.value.show();
    }
  } catch(e) {
    if (e.status === 422) {
      updateErrors.value = e.response.data.errors;
    }
  }
}


</script>

<template>
  <Toast ref="toastObject" :message="toastMessage" type="success"/>
  <h1 class="m-4 font-extrabold text-white text-3xl text-shadow text-center">Профиль</h1>

  <div class="w-full max-w-130 rounded-sm p-5 shadow-(--shadow-3xl) bg-(--input-bg-color-form)">

    <div class="flex flex-col mb-10">
      <div class="flex place-items-center mb-1">
        <p>Имя:</p>
        <div class="flex place-items-center rounded-md px-2">
          <div class="flex h-8 place-items-center">
            <p v-if="! nameEditMode" class="px-2">{{ authStore.user.name }}</p>
            <div v-if="nameEditMode">
              <div class="flex">
                <input
                    type="text"
                    placeholder="новое имя"
                    v-model="newName"
                    class="rounded-md px-2 bg-(--comment-bg-color) text-(--comment-text-color) mr-2">
                <ButtonBase @click.prevent="updateName()" type="button" variant="primary" :disabled="isLoading">Ok</ButtonBase>
              </div>
            </div>
          </div>
        </div>
        <div class="grow justify-items-end">
          <svg @click.prevent="toggleNameEditMode()"
               xmlns="http://www.w3.org/2000/svg" fill="none"
               viewBox="0 0 24 24"
               stroke-width="1.5"
               stroke="currentColor"
               class="size-4 cursor-pointer text-(--text-accent-color)">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
          </svg>
        </div>
      </div>
      <div v-if="updateErrors?.name">
        <ErrorList :errors="updateErrors.name"></ErrorList>
      </div>
    </div>


    <div>
      <div class="flex place-items-center justify-between rounded-md mb-10">
        <p>Изменить пароль</p>
        <svg @click.prevent="togglePasswordEditMode()"
             xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24"
             stroke-width="1.5"
             stroke="currentColor"
             class="size-4 cursor-pointer text-(--text-accent-color)">
          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
      </div>

      <form v-if="passwordEditMode" @submit.prevent="updatePassword()">

        <div class="relative mb-3">
          <input v-model="password" type="password" id="password" placeholder="••••••••"
                 class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
          />
          <label for="password" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-2 scale-95 top-1 px-2">Текущий пароль</label>
          <div v-if="updateErrors?.password">
            <ErrorList :errors = updateErrors.password></ErrorList>
          </div>
        </div>
        <div class="relative mb-3">
          <input v-model="newPassword" type="password" id="new_password" placeholder="••••••••"
                 class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
          />
          <label for="new_password" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-2 scale-95 top-1 px-2">Новый пароль</label>
          <div v-if="updateErrors?.new_password">
            <ErrorList :errors = updateErrors.new_password></ErrorList>
          </div>
        </div>
        <div class="relative mb-3">
          <input v-model="newPasswordConfirmation" type="password" id="new_password_confirmation" placeholder="••••••••"
                 class="block px-3 py-2 w-full text-gray-900 bg-transparent rounded-sm border-1
            rounded border-(--line-color) px-2 focus-within:outline-1 focus-within:outline-(--line-color)"
          />
          <label for="new_password_confirmation" class="absolute bg-(--input-bg-color-form) text-(--text-accent-color)
          transform -translate-y-4 translate-x-1 scale-95 top-1 px-2">Подтверждение нового пароля</label>
          <div v-if="updateErrors?.new_password_confirmation">
            <ErrorList :errors = updateErrors.new_password_confirmation></ErrorList>
          </div>
        </div>
        <div class="text-right">
          <ButtonBase variant="primary" type="submit" :disabled="isLoading"> Изменить пароль </ButtonBase>
        </div>
      </form>
    </div>




  </div>
</template>

<style scoped>

</style>
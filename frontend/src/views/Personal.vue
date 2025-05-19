<script setup>
import ButtonBase from "../components/ButtonBase.vue";
import ErrorList from "../components/ErrorList.vue";
import { getApi } from "../api/authApi.js";
import {ref, onMounted} from 'vue';
import PostItem from "../components/PostItem.vue";
import {useAuthStore} from "../stores/authStore.js";

const auth = useAuthStore();

const api = getApi();
const title = ref(null);
const content = ref(null);
const image = ref (null);
const fileInput = ref(null);
const storeImageErrors = ref(null);
const storeImageResponse = ref(null);
const storePostErrors = ref(null);
const isLoading = ref(false);

const posts = ref(null);

onMounted(() => {
  getPosts();
});

async function getPosts() {
  const response = await api.get('api/auth/posts');
  posts.value = response.data.data;
}

async function storePost() {

  try {
    const response = await api.post('/api/auth/posts/', {
      title: title.value,
      content: content.value,
      image_id: storeImageResponse.value?.id || null
    });

    title.value = null;
    content.value = null;
    storeImageResponse.value = null;

    posts.value.unshift(response.data.data);
  } catch(e) {
    if (e.response.status === 422) {
      storePostErrors.value = e.response.data.errors
    }

    if (e.status === 403) {
      console.error(e.response.data.message);
    }
  }

}

function selectImage() {
  fileInput.value.click();
}

async function storeImage(event) {
  isLoading.value = true;

  const file = event.target.files[0];
  const formData = new FormData();
  formData.append('image', file);

  try {
    const response = await api.post('/api/auth/post_images', formData);

    storeImageErrors.value = null;
    storeImageResponse.value = response.data.data;
  } catch(error) {
    if (error.response.status === 422) {
      storeImageErrors.value = error.response.data.errors
    }
  }

  isLoading.value = false;
}

function deleteSelectedImage() {
  storeImageResponse.value = null;
}

async function deletePost(post) {
  isLoading.value = true;

  const confirmation = confirm(`"${post.title}" \n Удаляем пост?`);

  if (confirmation) {

    try {
      const response = await api.delete(`api/auth/posts/${post.id}`);
      if (response.data.data === true) {
        posts.value = posts.value.filter(filteredPost => (
            filteredPost.id !== post.id
        ));
      }
    } catch (e) {
      if (e.status === 403) {
        console.error(e.response.data.message);
      }
    }

  }

  isLoading.value = false;
}

</script>

<template>

  <h1 class="m-4 font-extrabold text-white text-3xl text-shadow text-center">Добавить пост</h1>
  <div class="w-full">
    <div class="w-full">
      <form @submit.prevent="storePost()">
        <div class="mb-4">
          <label for="post_title" class="text-sm">Заголовок</label>
          <input v-model="title" type="text" id="post_title"
                 class="block w-full text-(--input-text-color) bg-(--input-bg-color) text-sm py-1 px-2 white_space">
          <div v-if="storePostErrors?.title">
            <ErrorList :errors="storePostErrors?.title"></ErrorList>
          </div>
        </div>

        <div class="mb-4">
          <label for="message" class="block mb-2 text-sm">Содержание</label>
          <textarea v-model="content" id="message" rows="4"
                    class="block p-2 w-full text-sm text-(--input-text-color) bg-(--input-bg-color) rounded-sm white_space"
                    placeholder="печатать тут ..."></textarea>
          <div v-if="storePostErrors?.content">
            <ErrorList :errors="storePostErrors?.content"></ErrorList>
          </div>
        </div>
        
        <div>
          <input @change="storeImage" ref="fileInput" type="file" hidden>
          <a @click.prevent="selectImage()" href="#">image</a>
          <a v-if="storeImageResponse?.url" @click.prevent="deleteSelectedImage()" href="#" class="ml-4">Отмена</a>
          <div v-if="storeImageErrors?.image">
            <ErrorList :errors="storeImageErrors.image"></ErrorList>
          </div>
          <div v-if="storeImageResponse?.url" class="flex justify-center my-4">
            <img :src="storeImageResponse.url" alt="preview" style="width: 60%; height:auto">
          </div>
        </div>



        <div class="text-right">
        <ButtonBase type="submit" variant="primary" :disabled="isLoading">Создать пост</ButtonBase>
        </div>
      </form>
    </div>

    <div class="my-10 w-full">
      <h1 class="m-4 font-extrabold text-white text-center text-3xl text-shadow">Посты</h1>
      <div v-if="posts" v-for="post in posts" :key="post.id" class="w-full">
        <PostItem :post="post"></PostItem>
        <div class="text-right mb-10">
          <ButtonBase @click.prevent="deletePost(post)" variant="danger" :disabled="isLoading">Удалить пост</ButtonBase>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped>

.white_space {
  white-space: pre-line;
}

</style>
<script setup>
import PostItem from '../components/PostItem.vue';
import { getApi } from "../api/authApi.js";
import {ref, onMounted} from 'vue';
import { useAuthStore } from "../stores/authStore.js";

const auth = useAuthStore();
const api = getApi();
const posts = ref(null);

onMounted(() => {
  getPosts();
});

async function getPosts() {
  if (auth.isAuthenticated) {
    try {
      const response = await api.get('api/auth/posts');
      posts.value = response.data.data;
    } catch(e) {

    }
  }

  if (! auth.isAuthenticated) {
    try {
      const response = await api.get('api/posts');
      posts.value = response.data.data;
    } catch(e) {

    }
  }

}

</script>

<template>
  <h1 class="m-4 font-extrabold text-white text-3xl text-shadow text-center">Посты</h1>
  <div v-if="posts" v-for="post in posts" class="w-full">
    <PostItem :post="post"></PostItem>
  </div>
</template>

<style scoped>

</style>
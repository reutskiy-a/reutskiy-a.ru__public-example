<script setup>
import ErrorList from "./ErrorList.vue";
import {getApi} from '../api/authApi.js';
import {ref, nextTick, reactive} from "vue";
import ButtonBase from "./ButtonBase.vue";
import Toast from "./Toast.vue";
import { useAuthStore } from "../stores/authStore.js"

defineProps(['post']);
const emit = defineEmits(['update:post']);

const authStore = useAuthStore();

const toastVisible = ref(null);
const toastMessage = ref('qwe');

const api = getApi();
const isLoading = ref(false);

const commentBody = ref(null);
const isCommentShown = ref(false);
const comments = ref(null);

const textareaRefs = reactive({});
const someCommentEditModeEnabled = ref(null);

const storeCommentErrors = ref(null);
const updateCommentErrors = ref(null);

async function toggleLike(post) {

  try {
    const response = await api.post(`api/auth/posts/${post.id}/toggleLike`);
    post.is_liked = response.data.is_liked;
    post.likes_count = response.data.likes_count;
  } catch (e) {
    toastMessage.value = 'Авторизуйтесь, чтобы поставить лайк';
    toastVisible.value?.show();
  }

}

async function storeComment(post) {
  isLoading.value = true;

  try {
    const response = await api.post(`api/auth/posts/${post.id}/comment`, {
      body: commentBody.value
    });

    commentBody.value = null;
    post.comments_count++;
    await getComments(post);

    storeCommentErrors.value = null;
    delete post.is_focused;
  } catch (error) {
    if (! authStore.isAuthenticated) {
      toastMessage.value = 'Авторизуйтесь, чтобы оставить комментарий';
      toastVisible.value?.show();
    }
    storeCommentErrors.value = error.response.data.errors;
  }

  isLoading.value = false;
}

async function getComments(post) {
  try {
    let response = {};

    if (authStore.isAuthenticated) {
      response = await api.get(`api/auth/posts/${post.id}/comment`);
    } else {
      response = await api.get(`api/posts/${post.id}/comment`);
    }

    comments.value = response.data.data;
    isCommentShown.value = true;
  } catch (e) {

  }
}

async function deleteComment(post, comment) {
  isLoading.value = true;
  const confirmation = confirm('Удалить комментарий ?');

  if (confirmation) {
    const response = await api.delete(`api/auth/comments/${comment.id}`);
    comments.value = comments.value.filter(filteredComment => {
      return filteredComment.id !== comment.id
    });

    post.comments_count = post.comments_count -1;
    if (post.comments_count === 0) {
      isCommentShown.value = false;
    }
  }

  isLoading.value = false;
}

function toggleCommentEditMode(comment) {
  if (! comment.edit_mode) {

    if (someCommentEditModeEnabled.value) {
      delete someCommentEditModeEnabled.value.edit_mode;
      updateCommentErrors.value = null;
    }

    someCommentEditModeEnabled.value = comment;

    comment.edited_body = comment.body;
    comment.edit_mode = true;

    nextTick(() => {

      const textarea = textareaRefs[comment.id];

      if (textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';

        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
      }
    });

    return;
  }

  delete comment.edited_body;
  delete comment.edit_mode;
}

function setTextareaRef(element, comment) {
  if (element) {
    textareaRefs[comment.id] = element;
  }
}

function handleStoreCommentTextareaBlur(event, post) {
  if (event.relatedTarget?.id === 'storePost' ||
      event.relatedTarget?.id === 'storePostCansel') {
    return
  }

  storeCommentErrors.value = null;
  delete post.is_focused;
}

function handleStoreCommentTextareaFocus(post) {
  post.is_focused = true;

  if (! authStore.isAuthenticated) {
    toastMessage.value = 'Авторизуйтесь, чтобы оставить комментарий';
    toastVisible.value?.show();
  }
}

async function updateComment(comment) {

  try {
    const response = await api.patch(`api/auth/comments/${comment.id}`, {
      body: comment.edited_body
    });

    comment.body = comment.edited_body;
    delete comment.edited_body;
    delete comment.edit_mode;
  } catch (error) {
    if (! authStore.isAuthenticated) {
      toastMessage.value = 'Авторизуйтесь, чтобы отредактировать комментарий';
      toastVisible.value?.show();
    }
    updateCommentErrors.value = error.response.data.errors;
  }

}

function storeCommentCancel(post) {
  storeCommentErrors.value = null;
  commentBody.value = null;
  delete post?.is_focused;
}


</script>

<template>

  <Toast ref="toastVisible" :message="toastMessage" type="danger" />

  <div class="w-full mb-8 order border-gray-200 rounded-md p-3 bg-(--input-bg-color) shadow-(--shadow-3xl)">
    <p class="mb-3 text-1xl font-bold text-left text-(--input-text-color)">{{ post.title }}</p>
    <p class="mb-5  text-sm text-justify text-base text-(--input-text-color) white_space">{{ post.content }}</p>
    <img v-if="post?.image_path" :src="post.image_path" alt="preview" class="rounded-md">

    <div class="flex justify-between py-2">
      <div class="flex">
        <svg @click.prevent="toggleLike(post)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor"
             :class="['cursor-pointer  size-6', post.is_liked ? 'fill-red-500 stroke-red-500 hover:stroke-red-500' : 'fill-gray-600 stroke-gray-600 hover:stroke-gray-600']">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
        </svg>
        <p class="ml-2 text-sm">{{ post.likes_count }}</p>
      </div>

      <div>
        <p class="text-sm text-right text-base text-(--input-text-color) ">{{ post.date }}</p>
      </div>
    </div>

    <!-- Комментарии -->
    <div>
      <div v-if="post.comments_count > 0">
        <div v-if="! isCommentShown" class="text-sm py-2 text-(--text-accent-color)">
          <a @click.prevent="getComments(post)" href="#">Показать комментарии ({{ post.comments_count }})</a>
        </div>
        <div v-if="isCommentShown" class="text-sm py-2 text-(--text-accent-color)">
          <a @click.prevent="isCommentShown = false" href="#">Скрыть комментарии</a>
          <div v-for="comment in comments" :key="comment.id" class="flex-col">

            <div :class='["my-3 px-2 py-1 border bg-(--comment-list-bg-color) text-(--comment-list-text-color) rounded-e-xl rounded-es-xl", comment?.owner ? "border-1 border-(--line-color)" : "border-1 border-(--line-color)"]'>

                <div class="flex justify-between">
                  <div class="flex items-center justify-center">
                    <div class="text-sm mr-1 text-(--text-accent-color)">{{ comment.user.name }}</div>
                    <svg v-if="comment.user.is_admin" viewBox="0 0 22 22" aria-label="Verified account"
                         role="img"
                         class="size-4 fill-teal-600"
                         data-testid="icon-verified">
                      <g><path d="M20.396 11c-.018-.646-.215-1.275-.57-1.816-.354-.54-.852-.972-1.438-1.246.223-.607.27-1.264.14-1.897-.131-.634-.437-1.218-.882-1.687-.47-.445-1.053-.75-1.687-.882-.633-.13-1.29-.083-1.897.14-.273-.587-.704-1.086-1.245-1.44S11.647 1.62 11 1.604c-.646.017-1.273.213-1.813.568s-.969.854-1.24 1.44c-.608-.223-1.267-.272-1.902-.14-.635.13-1.22.436-1.69.882-.445.47-.749 1.055-.878 1.688-.13.633-.08 1.29.144 1.896-.587.274-1.087.705-1.443 1.245-.356.54-.555 1.17-.574 1.817.02.647.218 1.276.574 1.817.356.54.856.972 1.443 1.245-.224.606-.274 1.263-.144 1.896.13.634.433 1.218.877 1.688.47.443 1.054.747 1.687.878.633.132 1.29.084 1.897-.136.274.586.705 1.084 1.246 1.439.54.354 1.17.551 1.816.569.647-.016 1.276-.213 1.817-.567s.972-.854 1.245-1.44c.604.239 1.266.296 1.903.164.636-.132 1.22-.447 1.68-.907.46-.46.776-1.044.908-1.681s.075-1.299-.165-1.903c.586-.274 1.084-.705 1.439-1.246.354-.54.551-1.17.569-1.816zM9.662 14.85l-3.429-3.428 1.293-1.302 2.072 2.072 4.4-4.794 1.347 1.246z"></path></g>
                    </svg>

                  </div>
                  <div class="flex">
                    <svg v-if="comment.owner" @click.prevent="toggleCommentEditMode(comment)"
                         xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.5"
                         stroke="currentColor"
                         class="size-4 mx-1 cursor-pointer text-(--text-accent-color)">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>

                    <svg v-if="comment.owner" @click.prevent="deleteComment(post, comment)"
                           xmlns="http://www.w3.org/2000/svg" fill="none"
                           viewBox="0 0 24 24"
                           stroke-width="1.5"
                           stroke="currentColor"
                           class="size-4 mx-1 cursor-pointer text-(--text-accent-color)">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                      </svg>
                    <div class="ml-4 text-sm">{{ comment.date }}</div>
                  </div>
              </div>

              <div v-if="! comment.edit_mode" class="white_space">{{ comment.body }}</div>
              <div v-if="comment.edit_mode">
                          <textarea
                              :ref="element => setTextareaRef(element, comment)"
                              v-model="comment.edited_body"
                              class="w-full resize-none overflow-hidden border rounded-md px-1 bg-(--comment-bg-color) text-(--comment-text-color)"
                              rows="1"
                              oninput="this.style.height = 'auto'; this.style.height =  this.scrollHeight + 'px';"
                          ></textarea>
                  <div v-if="updateCommentErrors" v-for="error in updateCommentErrors">
                    <ErrorList :errors="error"></ErrorList>
                  </div>
                <div class="text-right">
                  <ButtonBase @click.prevent="delete comment.edit_mode" type="button" variant="secondary" :disabled="isLoading" class="mr-2">Отмена</ButtonBase>
                  <ButtonBase @click.prevent="updateComment(comment)" type="button" variant="primary" :disabled="isLoading">Сохранить</ButtonBase>
                </div>
              </div>

            </div>

          </div>
        </div>
      </div>


      <form @submit.prevent="storeComment(post)">
        <div class="flex-col">
          <textarea
            v-model="commentBody"
            @focus="handleStoreCommentTextareaFocus(post)"
            @blur="event => handleStoreCommentTextareaBlur(event, post)"
            class="w-full resize-none overflow-hidden border rounded-md px-1 bg-(--comment-bg-color) text-(--comment-text-color)"
            rows="1"
            placeholder="Написать комментарий..."
            oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';"
          ></textarea>

          <div v-if="storeCommentErrors" v-for="error in storeCommentErrors">
            <ErrorList :errors="error"></ErrorList>
          </div>

          <div v-if="post.is_focused" class="text-right">
            <ButtonBase @click.prevent="storeCommentCancel(post)" type="button" variant="secondary" :disabled="isLoading" id="storePostCansel" class="mr-2">Отмена</ButtonBase>
            <ButtonBase @touchend.prevent="storeComment(post)" type="submit" variant="primary" :disabled="isLoading" id="storePost">Отправить</ButtonBase>
          </div>
        </div>
      </form>
    </div>
    <!-- /Комментарии -->

  </div>

</template>

<style scoped>
.white_space {
  white-space: pre-line
}
</style>
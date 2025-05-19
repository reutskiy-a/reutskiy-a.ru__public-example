<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

// Определяем пропсы с помощью defineProps
const { message, type = 'info', duration = 3000, showCloseButton = true } = defineProps({
  message: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'info',
  },
  duration: {
    type: Number,
    default: 3000,
  },
  showCloseButton: {
    type: Boolean,
    default: true,
  },
});

// Состояние видимости
const isVisible = ref(true);

// Эмит для уведомления родителя о закрытии
const emit = defineEmits(['closed']);

// Функция закрытия уведомления
const close = () => {
  isVisible.value = false;
  emit('closed');
};

// Автоматическое закрытие через заданное время
onMounted(() => {
  if (props.duration > 0) {
    setTimeout(() => {
      close();
    }, props.duration);
  }
});

// Очистка таймера при размонтировании (если компонент уничтожен раньше)
onUnmounted(() => {
  clearTimeout();
});
</script>

<template>
  <div v-if="isVisible" :class="['notification', type]" role="alert">
    {{ message }}
    <button v-if="showCloseButton" @click="close" class="close-btn">×</button>
  </div>
</template>

<style scoped>
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 15px 20px;
  border-radius: 5px;
  color: white;
  font-family: Arial, sans-serif;
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-width: 200px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  z-index: 1000;
}

.info {
  background-color: #3498db;
}

.success {
  background-color: #2ecc71;
}

.error {
  background-color: #e74c3c;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
  margin-left: 10px;
  line-height: 1;
}

.close-btn:hover {
  opacity: 0.8;
}
</style>
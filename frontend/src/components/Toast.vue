<template>
  <transition name="fade">
    <div v-if="isVisible" class="toast" :class="type">
      <div class="toast-content">
        {{ message }}
      </div>
      <button class="toast-close" @click="hide">&times;</button>
    </div>
  </transition>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['primary', 'info', 'success', 'warning', 'danger'].includes(value)
  },
  duration: {
    type: Number,
    default: 3000 // время в миллисекундах
  },
  autoClose: {
    type: Boolean,
    default: true
  }
})

const isVisible = ref(false)
let timeoutId = null

const show = () => {
  isVisible.value = true
  if (props.autoClose) {
    timeoutId = setTimeout(hide, props.duration)
  }
}

const hide = () => {
  isVisible.value = false
  if (timeoutId) {
    clearTimeout(timeoutId)
    timeoutId = null
  }
}

// Экспортируем методы для использования извне
defineExpose({
  show,
  hide
})
</script>

<style scoped>
.toast {
  position: fixed;
  top: 40px;
  right: 40px;
  padding: 12px 20px;
  border-radius: 4px;
  color: white;
  display: flex;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  max-width: 300px;
  font-weight: normal;
}

.toast-content {
  flex-grow: 1;
}

.toast-close {
  background: none;
  border: none;
  color: inherit;
  margin-left: 10px;
  cursor: pointer;
  font-size: 1.2em;
}

.toast.primary {
  background-color: #0069d9;
  border: 1px solid #014fa3;
  color: white;
}

.toast.info {
  background-color: #138496;
  border: 1px solid #0c6877;
  color: white;
}

.toast.success {
  background-color: #218838;
  border: 1px solid #145e25;
  color: white;
}

.toast.warning {
  background-color: #ffc107;
  border: 1px solid #d39e00;
  color: #212529;
}

.toast.danger {
  background-color: #c82333;
  border: 1px solid #97101d;
  color: white;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
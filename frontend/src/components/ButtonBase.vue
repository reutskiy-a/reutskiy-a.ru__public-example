<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger', 'success', 'warning', 'info'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

defineEmits(['click'])
</script>

<template>
  <button
      :type="type"
      :class="[
      'custom-button',
      `button--${variant}`,
      { 'button--disabled': disabled }
    ]"
      :disabled="disabled"
      @click="$emit('click', $event)"
  >
    <slot>Default Button</slot>
  </button>
</template>

<style scoped>
/* Базовый стиль кнопки */
.custom-button {
  border-style: solid;
  border-width: 1px;
  border-radius: 3px;
  box-sizing: border-box;
  display: inline-block;
  font-size: 13px;
  font-weight: normal;
  height: 29px;
  padding: 0 11px;
  text-align: center;
  text-decoration: none;
  text-overflow: ellipsis;
  user-select: none;
  -webkit-user-select: none;
  overflow: hidden;
  transition: background-color 0.1s ease-in-out;
}

/* Primary */
.button--primary {
  background-color: #007bff;
  border-color: #0062cc;
  color: white;
}
.button--primary:hover:not(.button--disabled) {
  background-color: #0069d9;
}
.button--primary:active:not(.button--disabled) {
  background-color: #0056b3;
}

/* Secondary */
.button--secondary {
  background-color: #6c757d;
  border-color: #545b62;
  color: white;
}
.button--secondary:hover:not(.button--disabled) {
  background-color: #5a6268;
}
.button--secondary:active:not(.button--disabled) {
  background-color: #495057;
}

/* Success */
.button--success {
  background-color: #28a745;
  border-color: #1e7e34;
  color: white;
}
.button--success:hover:not(.button--disabled) {
  background-color: #218838;
}
.button--success:active:not(.button--disabled) {
  background-color: #1c7430;
}

/* Danger */
.button--danger {
  background-color: #dc3545;
  border-color: #bd2130;
  color: white;
}
.button--danger:hover:not(.button--disabled) {
  background-color: #c82333;
}
.button--danger:active:not(.button--disabled) {
  background-color: #bd2130;
}

/* Warning */
.button--warning {
  background-color: #ffc107;
  border-color: #d39e00;
  color: #212529;
}
.button--warning:hover:not(.button--disabled) {
  background-color: #e0a800;
}
.button--warning:active:not(.button--disabled) {
  background-color: #d39e00;
}

/* Info */
.button--info {
  background-color: #17a2b8;
  border-color: #117a8b;
  color: white;
}
.button--info:hover:not(.button--disabled) {
  background-color: #138496;
}
.button--info:active:not(.button--disabled) {
  background-color: #117a8b;
}

.button--disabled {
  background-color: #d6d8db;
  border-color: #7e8184;
  color: #3d4347;
  cursor: not-allowed;
  opacity: 0.7;
}

</style>
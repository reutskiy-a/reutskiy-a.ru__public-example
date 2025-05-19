<script setup>
defineProps(['errors'])
</script>

<template>
  <div v-for="(error, index) in errors" :key="index" class="text-sm text-(--danger-color)">
    <div>{{ error }}</div>
  </div>
</template>

<style scoped>

</style>
<script setup>
defineProps({ message: String })
</script>

<template>
  <el-alert v-if="message" class="error-message" :title="message" type="error" show-icon :closable="false" />
</template>

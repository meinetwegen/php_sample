<script setup>
import ErrorMessage from '@/components/system/ErrorMessage.vue'
import TheButton from '@/components/TheButton/TheButton.vue'

defineProps({
  submitLabel: String,
  loading: Boolean,
  error: String,
})
defineEmits(['submit'])
</script>

<template>
  <el-form label-position="top" @submit.prevent="$emit('submit')">
    <ErrorMessage :message="error" />
    <slot />
    <TheButton native-type="submit" :loading="loading" class="the-form__submit">{{ submitLabel }}</TheButton>
  </el-form>
</template>

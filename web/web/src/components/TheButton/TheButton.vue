<script setup>
defineProps({
  type: { type: String, default: 'primary' },
  nativeType: { type: String, default: 'button' },
  loading: Boolean,
  disabled: Boolean,
})
</script>

<template>
  <el-button :type="type" :native-type="nativeType" :loading="loading" :disabled="disabled">
    <slot />
  </el-button>
</template>

<script setup>
defineProps({
  modelValue: Boolean,
  title: String,
  width: { type: String, default: '520px' },
})
defineEmits(['update:modelValue'])
</script>

<template>
  <el-dialog :model-value="modelValue" :title="title" :width="width" destroy-on-close @update:model-value="$emit('update:modelValue', $event)">
    <slot />
  </el-dialog>
</template>

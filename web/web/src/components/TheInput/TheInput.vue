<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: String,
  type: { type: String, default: 'text' },
  placeholder: String,
  autocomplete: String,
  error: String,
})
defineEmits(['update:modelValue'])
</script>

<template>
  <el-form-item :label="label" :error="error">
    <el-input
      :model-value="modelValue"
      :type="type"
      :placeholder="placeholder"
      :autocomplete="autocomplete"
      :show-password="type === 'password'"
      @update:model-value="$emit('update:modelValue', $event)"
    />
  </el-form-item>
</template>

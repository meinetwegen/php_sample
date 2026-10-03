<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, required: true },
  label: String,
  placeholder: String,
  clearable: Boolean,
  error: String,
})
defineEmits(['update:modelValue'])
</script>

<template>
  <el-form-item v-if="label" :label="label" :error="error">
    <el-select :model-value="modelValue" :placeholder="placeholder" :clearable="clearable" @update:model-value="$emit('update:modelValue', $event)">
      <el-option v-for="option in options" :key="option.value" :label="option.label" :value="option.value" />
    </el-select>
  </el-form-item>
  <el-select v-else :model-value="modelValue" :placeholder="placeholder" :clearable="clearable" @update:model-value="$emit('update:modelValue', $event)">
    <el-option v-for="option in options" :key="option.value" :label="option.label" :value="option.value" />
  </el-select>
</template>

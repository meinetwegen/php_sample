<script setup>
defineProps({
  modelValue: { type: String, required: true },
  tabs: { type: Array, required: true },
})
defineEmits(['update:modelValue'])
</script>

<template>
  <el-tabs :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)">
    <el-tab-pane v-for="tab in tabs" :key="tab.name" :label="tab.label" :name="tab.name" />
  </el-tabs>
</template>

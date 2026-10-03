<script setup>
defineProps({
  columns: { type: Array, required: true },
  data: { type: Array, default: () => [] },
  loading: Boolean,
  emptyText: String,
})
</script>

<template>
  <el-table v-loading="loading" :data="data" :empty-text="emptyText" stripe>
    <el-table-column v-if="$slots.expand" type="expand">
      <template #default="{ row }"><slot name="expand" :row="row" /></template>
    </el-table-column>
    <el-table-column v-for="column in columns" :key="column.prop" :prop="column.prop" :label="column.label" :width="column.width" :align="column.align">
      <template #default="{ row }">
        <slot :name="`cell-${column.prop}`" :row="row">{{ row[column.prop] }}</slot>
      </template>
    </el-table-column>
  </el-table>
</template>

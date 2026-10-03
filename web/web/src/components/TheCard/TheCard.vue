<script setup>
defineProps({ title: String })
</script>

<template>
  <el-card class="the-card" shadow="hover">
    <template v-if="title" #header>{{ title }}</template>
    <slot />
  </el-card>
</template>

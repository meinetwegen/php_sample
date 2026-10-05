<script setup>
import TheCard from '@/components/TheCard/TheCard.vue'
</script>

<template>
  <div class="auth-layout">
    <router-link to="/" class="auth-layout__brand">{{ $t('app.title') }}</router-link>
    <TheCard class="auth-layout__card">
      <slot />
    </TheCard>
  </div>
</template>

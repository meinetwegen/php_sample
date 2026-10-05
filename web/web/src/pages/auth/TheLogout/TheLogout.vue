<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const store = useStore()
const router = useRouter()

onMounted(async () => {
  await store.dispatch('auth/logout')
  router.replace({ name: 'login' })
})
</script>

<template>
  <p>{{ $t('auth.loggingOut') }}</p>
</template>

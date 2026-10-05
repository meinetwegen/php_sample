<script setup>
import { computed, onBeforeUnmount, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import AdminLayout from '@/layouts/AdminLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'

const layouts = { auth: AuthLayout, default: DefaultLayout, admin: AdminLayout }

const route = useRoute()
const router = useRouter()
const store = useStore()

const layout = computed(() => layouts[route.meta.layout] || DefaultLayout)

// The API client fires this event when a token can neither be used nor refreshed.
async function onForcedLogout() {
  store.dispatch('auth/clear')
  router.push({ name: 'login' })
}

onMounted(() => window.addEventListener('auth:logout', onForcedLogout))
onBeforeUnmount(() => window.removeEventListener('auth:logout', onForcedLogout))
</script>

<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

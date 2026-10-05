<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import TheForm from '@/components/TheForm/TheForm.vue'
import TheInput from '@/components/TheInput/TheInput.vue'
import { errorMessage, fieldErrors } from '@/utils/errors'

const store = useStore()
const route = useRoute()
const router = useRouter()

const form = reactive({ email: '', password: '' })
const errors = ref({})
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  errors.value = {}
  try {
    await store.dispatch('auth/login', form)
    router.push(route.query.redirect || '/')
  } catch (e) {
    errors.value = fieldErrors(e)
    error.value = Object.keys(errors.value).length ? '' : errorMessage(e)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <TheForm :submit-label="$t('auth.login')" :loading="loading" :error="error" @submit="submit">
    <TheInput v-model="form.email" :label="$t('auth.email')" type="email" autocomplete="email" :error="errors.email" />
    <TheInput v-model="form.password" :label="$t('auth.password')" type="password" autocomplete="current-password" :error="errors.password" />
  </TheForm>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import TheForm from '@/components/TheForm/TheForm.vue'
import TheInput from '@/components/TheInput/TheInput.vue'
import { errorMessage, fieldErrors } from '@/utils/errors'

const store = useStore()
const router = useRouter()

const form = reactive({ name: '', email: '', password: '' })
const errors = ref({})
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  errors.value = {}
  try {
    await store.dispatch('auth/register', form)
    router.push('/')
  } catch (e) {
    errors.value = fieldErrors(e)
    error.value = Object.keys(errors.value).length ? '' : errorMessage(e)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <TheForm :submit-label="$t('auth.register')" :loading="loading" :error="error" @submit="submit">
    <TheInput v-model="form.name" :label="$t('auth.name')" autocomplete="name" :error="errors.name" />
    <TheInput v-model="form.email" :label="$t('auth.email')" type="email" autocomplete="email" :error="errors.email" />
    <TheInput v-model="form.password" :label="$t('auth.password')" type="password" autocomplete="new-password" :error="errors.password" />
  </TheForm>
</template>

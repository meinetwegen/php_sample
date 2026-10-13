<script setup>
import { reactive, ref, watch } from 'vue'
import TheForm from '@/components/TheForm/TheForm.vue'
import TheInput from '@/components/TheInput/TheInput.vue'
import { adminApi } from '@/plugins/api'
import { errorMessage, fieldErrors } from '@/utils/errors'

const props = defineProps({ category: { type: Object, default: null } })
const emit = defineEmits(['saved'])

const form = reactive({ name: '' })
const errors = ref({})
const error = ref('')
const loading = ref(false)

watch(() => props.category, (category) => { form.name = category?.name || '' }, { immediate: true })

async function submit() {
  loading.value = true
  error.value = ''
  errors.value = {}
  try {
    if (props.category) await adminApi.updateCategory(props.category.id, form)
    else await adminApi.createCategory(form)
    emit('saved')
  } catch (e) {
    errors.value = fieldErrors(e)
    error.value = Object.keys(errors.value).length ? '' : errorMessage(e)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <TheForm :submit-label="$t('common.save')" :loading="loading" :error="error" @submit="submit">
    <TheInput v-model="form.name" :label="$t('admin.fields.name')" :error="errors.name" />
  </TheForm>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import TheForm from '@/components/TheForm/TheForm.vue'
import TheInput from '@/components/TheInput/TheInput.vue'
import TheSelector from '@/components/TheSelector/TheSelector.vue'
import { FORMATS } from '@/config'
import { centsToEuros, eurosToCents } from '@/helpers/money'
import { adminApi } from '@/plugins/api'
import { errorMessage, fieldErrors } from '@/utils/errors'

const props = defineProps({
  magazine: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
})
const emit = defineEmits(['saved'])

const { t } = useI18n()
const blank = () => ({ title: '', issue_number: '', description: '', price: 0, stock: 0, format: 'print', category_id: '', cover_url: '', published_at: '' })
const form = reactive(blank())
const errors = ref({})
const error = ref('')
const loading = ref(false)

watch(() => props.magazine, (m) => {
  Object.assign(form, blank(), m && {
    ...m,
    price: centsToEuros(m.price_cents),
    category_id: m.category_id ?? '',
    issue_number: m.issue_number ?? '',
    description: m.description ?? '',
    cover_url: m.cover_url ?? '',
    published_at: m.published_at ?? '',
  })
}, { immediate: true })

const formatOptions = computed(() => FORMATS.map((value) => ({ value, label: t(`formats.${value}`) })))
const categoryOptions = computed(() => props.categories.map((c) => ({ value: c.id, label: c.name })))

async function submit() {
  loading.value = true
  error.value = ''
  errors.value = {}
  const payload = {
    title: form.title,
    issue_number: form.issue_number,
    description: form.description,
    price_cents: eurosToCents(form.price),
    stock: Number(form.stock),
    format: form.format,
    category_id: form.category_id === '' ? null : form.category_id,
    cover_url: form.cover_url,
    published_at: form.published_at,
  }
  try {
    if (props.magazine) await adminApi.updateMagazine(props.magazine.id, payload)
    else await adminApi.createMagazine(payload)
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
    <TheInput v-model="form.title" :label="$t('admin.fields.title')" :error="errors.title" />
    <TheInput v-model="form.issue_number" :label="$t('admin.fields.issue')" :error="errors.issue_number" />
    <TheInput v-model="form.description" type="textarea" :label="$t('admin.fields.description')" :error="errors.description" />
    <TheInput v-model.number="form.price" type="number" :label="$t('admin.fields.price')" :error="errors.price_cents" />
    <TheInput v-model.number="form.stock" type="number" :label="$t('admin.fields.stock')" :error="errors.stock" />
    <TheSelector v-model="form.format" :options="formatOptions" :label="$t('admin.fields.format')" :error="errors.format" />
    <TheSelector v-model="form.category_id" :options="categoryOptions" :label="$t('admin.fields.category')" clearable :error="errors.category_id" />
    <TheInput v-model="form.cover_url" :label="$t('admin.fields.cover')" :error="errors.cover_url" />
    <TheInput v-model="form.published_at" type="date" :label="$t('admin.fields.published')" :error="errors.published_at" />
  </TheForm>
</template>

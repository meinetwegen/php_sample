<script setup>
import { computed, reactive, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import TheSelector from '@/components/TheSelector/TheSelector.vue'
import { FORMATS, SORTS } from '@/config'
import { debounce } from '@/utils/debounce'

const props = defineProps({
  modelValue: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue'])

const { t } = useI18n()
const form = reactive({ ...props.modelValue })

const emitChange = () => emit('update:modelValue', { ...form })
watch(() => form.q, debounce(emitChange, 350))
watch(() => [form.category, form.format, form.sort], emitChange)

const categoryOptions = computed(() => props.categories.map((c) => ({ value: c.slug, label: `${c.name} (${c.magazines_count})` })))
const formatOptions = computed(() => FORMATS.map((value) => ({ value, label: t(`formats.${value}`) })))
const sortOptions = computed(() => SORTS.map((value) => ({ value, label: t(`sorts.${value}`) })))
</script>

<template>
  <div class="the-filter">
    <el-input v-model="form.q" :placeholder="$t('catalog.search')" clearable />
    <TheSelector v-model="form.category" :options="categoryOptions" :placeholder="$t('catalog.category')" clearable />
    <TheSelector v-model="form.format" :options="formatOptions" :placeholder="$t('catalog.format')" clearable />
    <TheSelector v-model="form.sort" :options="sortOptions" :placeholder="$t('catalog.sort')" />
  </div>
</template>

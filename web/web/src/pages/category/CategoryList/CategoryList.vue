<script setup>
import { computed, onMounted, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useI18n } from 'vue-i18n'
import TheButton from '@/components/TheButton/TheButton.vue'
import TheDialog from '@/components/TheDialog/TheDialog.vue'
import TheTable from '@/components/TheTable/TheTable.vue'
import { adminApi, catalogApi } from '@/plugins/api'
import { errorMessage } from '@/utils/errors'
import CategoryForm from './components/CategoryForm.vue'

const { t } = useI18n()
const categories = ref([])
const loading = ref(true)
const dialog = ref(false)
const editing = ref(null)

const columns = computed(() => [
  { prop: 'name', label: t('admin.fields.name') },
  { prop: 'slug', label: 'Slug' },
  { prop: 'magazines_count', label: t('admin.magazines'), width: 130 },
  { prop: 'actions', label: '', width: 200 },
])

async function load() {
  loading.value = true
  try {
    categories.value = (await catalogApi.categories()).data.data
  } finally {
    loading.value = false
  }
}

function open(category = null) {
  editing.value = category
  dialog.value = true
}

async function onSaved() {
  dialog.value = false
  await load()
}

async function remove(category) {
  try {
    await ElMessageBox.confirm(t('admin.confirmDelete', { name: category.name }), { type: 'warning' })
    await adminApi.deleteCategory(category.id)
    await load()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(errorMessage(e))
  }
}

onMounted(load)
</script>

<template>
  <section>
    <div class="page-header">
      <h1>{{ $t('admin.categories') }}</h1>
      <TheButton @click="open()">{{ $t('admin.new') }}</TheButton>
    </div>
    <TheTable :columns="columns" :data="categories" :loading="loading">
      <template #cell-actions="{ row }">
        <TheButton @click="open(row)">{{ $t('common.edit') }}</TheButton>
        <TheButton type="danger" @click="remove(row)">{{ $t('common.delete') }}</TheButton>
      </template>
    </TheTable>
    <TheDialog v-model="dialog" :title="editing ? $t('common.edit') : $t('admin.new')">
      <CategoryForm :category="editing" @saved="onSaved" />
    </TheDialog>
  </section>
</template>

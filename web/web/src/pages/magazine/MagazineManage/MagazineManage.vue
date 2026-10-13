<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useI18n } from 'vue-i18n'
import TheButton from '@/components/TheButton/TheButton.vue'
import TheDialog from '@/components/TheDialog/TheDialog.vue'
import TheTable from '@/components/TheTable/TheTable.vue'
import { formatMoney } from '@/helpers/money'
import { adminApi, catalogApi } from '@/plugins/api'
import { errorMessage } from '@/utils/errors'
import MagazineForm from './components/MagazineForm.vue'

const PER_PAGE = 20
const { t, locale } = useI18n()
const state = reactive({ items: [], total: 0, page: 1, loading: true })
const categories = ref([])
const dialog = ref(false)
const editing = ref(null)

const columns = computed(() => [
  { prop: 'title', label: t('admin.fields.title') },
  { prop: 'issue_number', label: t('admin.fields.issue'), width: 100 },
  { prop: 'price_cents', label: t('admin.fields.price'), width: 110 },
  { prop: 'stock', label: t('admin.fields.stock'), width: 90 },
  { prop: 'format', label: t('admin.fields.format'), width: 110 },
  { prop: 'is_active', label: t('admin.fields.active'), width: 90 },
  { prop: 'actions', label: '', width: 220 },
])

async function load() {
  state.loading = true
  try {
    const { data } = await adminApi.magazines({ page: state.page, per_page: PER_PAGE, sort: 'title' })
    state.items = data.data
    state.total = data.meta.total
  } finally {
    state.loading = false
  }
}

function open(magazine = null) {
  editing.value = magazine
  dialog.value = true
}

async function onSaved() {
  dialog.value = false
  await load()
}

async function deactivate(magazine) {
  try {
    await ElMessageBox.confirm(t('admin.confirmDeactivate', { name: magazine.title }), { type: 'warning' })
    await adminApi.deleteMagazine(magazine.id)
    await load()
  } catch (e) {
    if (e !== 'cancel') ElMessage.error(errorMessage(e))
  }
}

watch(() => state.page, load)
onMounted(async () => {
  categories.value = (await catalogApi.categories()).data.data
  load()
})
</script>

<template>
  <section>
    <div class="page-header">
      <h1>{{ $t('admin.magazines') }}</h1>
      <TheButton @click="open()">{{ $t('admin.new') }}</TheButton>
    </div>
    <TheTable :columns="columns" :data="state.items" :loading="state.loading">
      <template #cell-price_cents="{ row }">{{ formatMoney(row.price_cents, locale) }}</template>
      <template #cell-format="{ row }">{{ $t(`formats.${row.format}`) }}</template>
      <template #cell-is_active="{ row }">{{ row.is_active ? $t('common.yes') : $t('common.no') }}</template>
      <template #cell-actions="{ row }">
        <TheButton @click="open(row)">{{ $t('common.edit') }}</TheButton>
        <TheButton v-if="row.is_active" type="danger" @click="deactivate(row)">{{ $t('admin.deactivate') }}</TheButton>
      </template>
    </TheTable>
    <el-pagination v-if="state.total > PER_PAGE" v-model:current-page="state.page" :page-size="PER_PAGE" :total="state.total" layout="prev, pager, next" background class="pagination" />
    <TheDialog v-model="dialog" :title="editing ? $t('common.edit') : $t('admin.new')" width="640px">
      <MagazineForm :magazine="editing" :categories="categories" @saved="onSaved" />
    </TheDialog>
  </section>
</template>

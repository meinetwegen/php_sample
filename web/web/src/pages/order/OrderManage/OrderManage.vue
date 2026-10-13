<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import TheSelector from '@/components/TheSelector/TheSelector.vue'
import TheTable from '@/components/TheTable/TheTable.vue'
import TheTabs from '@/components/TheTabs/TheTabs.vue'
import { ORDER_STATUSES, ORDER_TRANSITIONS } from '@/config'
import { formatDate } from '@/helpers/date'
import { formatMoney } from '@/helpers/money'
import { adminApi } from '@/plugins/api'
import { errorMessage } from '@/utils/errors'

const PER_PAGE = 20
const { t, locale } = useI18n()
const status = ref('all')
const state = reactive({ items: [], total: 0, page: 1, loading: true })

const tabs = computed(() => ['all', ...ORDER_STATUSES].map((name) => ({ name, label: t(`statuses.${name}`) })))
const columns = computed(() => [
  { prop: 'id', label: t('orders.number'), width: 80 },
  { prop: 'created_at', label: t('orders.date'), width: 130 },
  { prop: 'user_email', label: t('auth.email') },
  { prop: 'payment_status', label: t('orders.payment'), width: 110 },
  { prop: 'total_cents', label: t('orders.total'), width: 110 },
  { prop: 'status', label: t('orders.status'), width: 190 },
])

const nextOptions = (order) => ORDER_TRANSITIONS[order.status].map((value) => ({ value, label: t(`statuses.${value}`) }))

async function load() {
  state.loading = true
  try {
    const params = { page: state.page, per_page: PER_PAGE }
    if (status.value !== 'all') params.status = status.value
    const { data } = await adminApi.orders(params)
    state.items = data.data
    state.total = data.meta.total
  } finally {
    state.loading = false
  }
}

async function changeStatus(order, value) {
  try {
    await adminApi.setOrderStatus(order.id, value)
    await load()
  } catch (e) {
    ElMessage.error(errorMessage(e))
  }
}

watch(status, () => {
  state.page = 1
  load()
})
watch(() => state.page, load)
onMounted(load)
</script>

<template>
  <section>
    <h1>{{ $t('admin.orders') }}</h1>
    <TheTabs v-model="status" :tabs="tabs" />
    <TheTable :columns="columns" :data="state.items" :loading="state.loading" :empty-text="$t('orders.empty')">
      <template #cell-created_at="{ row }">{{ formatDate(row.created_at, locale) }}</template>
      <template #cell-total_cents="{ row }">{{ formatMoney(row.total_cents, locale) }}</template>
      <template #cell-payment_status="{ row }">{{ $t(`orders.${row.payment_status}`) }}</template>
      <template #cell-status="{ row }">
        <TheSelector
          v-if="nextOptions(row).length"
          :model-value="row.status"
          :options="[{ value: row.status, label: $t(`statuses.${row.status}`) }, ...nextOptions(row)]"
          @update:model-value="(value) => value !== row.status && changeStatus(row, value)"
        />
        <span v-else>{{ $t(`statuses.${row.status}`) }}</span>
      </template>
    </TheTable>
    <el-pagination v-if="state.total > PER_PAGE" v-model:current-page="state.page" :page-size="PER_PAGE" :total="state.total" layout="prev, pager, next" background class="pagination" />
  </section>
</template>

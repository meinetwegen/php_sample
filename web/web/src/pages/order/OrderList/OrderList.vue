<script setup>
import { computed, onMounted, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import TheButton from '@/components/TheButton/TheButton.vue'
import TheTable from '@/components/TheTable/TheTable.vue'
import { formatDate } from '@/helpers/date'
import { formatMoney } from '@/helpers/money'
import { ordersApi } from '@/plugins/api'
import { errorMessage } from '@/utils/errors'

const { t, locale } = useI18n()
const orders = ref([])
const loading = ref(true)

const columns = computed(() => [
  { prop: 'id', label: t('orders.number'), width: 90 },
  { prop: 'created_at', label: t('orders.date'), width: 140 },
  { prop: 'status', label: t('orders.status'), width: 140 },
  { prop: 'payment_status', label: t('orders.payment'), width: 120 },
  { prop: 'total_cents', label: t('orders.total'), width: 120 },
  { prop: 'actions', label: '' },
])

async function load() {
  loading.value = true
  try {
    orders.value = (await ordersApi.list()).data.data
  } catch (e) {
    ElMessage.error(errorMessage(e))
  } finally {
    loading.value = false
  }
}

async function act(call, order) {
  try {
    await call(order.id)
    await load()
  } catch (e) {
    ElMessage.error(errorMessage(e))
  }
}

onMounted(load)
</script>

<template>
  <section>
    <h1>{{ $t('orders.title') }}</h1>
    <TheTable :columns="columns" :data="orders" :loading="loading" :empty-text="$t('orders.empty')">
      <template #expand="{ row }">
        <ul class="order-items">
          <li v-for="item in row.items" :key="item.title">
            {{ item.title }} &times; {{ item.quantity }} — {{ formatMoney(item.price_cents * item.quantity, locale) }}
          </li>
        </ul>
      </template>
      <template #cell-created_at="{ row }">{{ formatDate(row.created_at, locale) }}</template>
      <template #cell-status="{ row }"><el-tag :type="row.status === 'cancelled' ? 'info' : 'success'">{{ $t(`statuses.${row.status}`) }}</el-tag></template>
      <template #cell-payment_status="{ row }">{{ $t(`orders.${row.payment_status}`) }}</template>
      <template #cell-total_cents="{ row }">{{ formatMoney(row.total_cents, locale) }}</template>
      <template #cell-actions="{ row }">
        <template v-if="row.status === 'pending'">
          <TheButton @click="act(ordersApi.pay, row)">{{ $t('orders.pay') }}</TheButton>
          <TheButton type="danger" @click="act(ordersApi.cancel, row)">{{ $t('orders.cancel') }}</TheButton>
        </template>
      </template>
    </TheTable>
  </section>
</template>

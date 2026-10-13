<script setup>
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import RevenueChart from '@/components/Charts/RevenueChart.vue'
import ErrorMessage from '@/components/system/ErrorMessage.vue'
import TheCard from '@/components/TheCard/TheCard.vue'
import TheTable from '@/components/TheTable/TheTable.vue'
import { adminApi } from '@/plugins/api'
import { errorMessage } from '@/utils/errors'
import StatsCards from './components/StatsCards.vue'

const { t } = useI18n()
const stats = ref(null)
const error = ref('')

const lowStockColumns = computed(() => [
  { prop: 'title', label: t('cart.magazine') },
  { prop: 'issue_number', label: t('admin.fields.issue'), width: 110 },
  { prop: 'stock', label: t('admin.fields.stock'), width: 90 },
])

onMounted(async () => {
  try {
    stats.value = (await adminApi.stats()).data.data
  } catch (e) {
    error.value = errorMessage(e)
  }
})
</script>

<template>
  <section>
    <h1>{{ $t('admin.dashboard') }}</h1>
    <ErrorMessage :message="error" />
    <template v-if="stats">
      <StatsCards :stats="stats" />
      <TheCard :title="$t('admin.revenue14')" class="dashboard-block">
        <RevenueChart :points="stats.revenue_by_day" :label="$t('admin.stats.revenue')" />
      </TheCard>
      <TheCard :title="$t('admin.lowStock')" class="dashboard-block">
        <TheTable :columns="lowStockColumns" :data="stats.low_stock" :empty-text="$t('admin.noLowStock')" />
      </TheCard>
    </template>
  </section>
</template>

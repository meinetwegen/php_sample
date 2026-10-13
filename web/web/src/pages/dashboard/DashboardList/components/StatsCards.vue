<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import TheCard from '@/components/TheCard/TheCard.vue'
import { formatMoney } from '@/helpers/money'

const props = defineProps({ stats: { type: Object, required: true } })
const { locale } = useI18n()

const cards = computed(() => [
  { key: 'revenue', value: formatMoney(props.stats.revenue_cents, locale.value) },
  { key: 'orders', value: props.stats.orders },
  { key: 'users', value: props.stats.users },
  { key: 'magazines', value: props.stats.magazines },
])
</script>

<template>
  <div class="stats-cards">
    <TheCard v-for="card in cards" :key="card.key">
      <div class="stats-cards__label">{{ $t(`admin.stats.${card.key}`) }}</div>
      <div class="stats-cards__value">{{ card.value }}</div>
    </TheCard>
  </div>
</template>

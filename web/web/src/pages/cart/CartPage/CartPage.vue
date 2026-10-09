<script setup>
import { computed, onMounted, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { useStore } from 'vuex'
import TheButton from '@/components/TheButton/TheButton.vue'
import TheCard from '@/components/TheCard/TheCard.vue'
import TheTable from '@/components/TheTable/TheTable.vue'
import { formatMoney } from '@/helpers/money'
import { errorMessage } from '@/utils/errors'
import CheckoutForm from './components/CheckoutForm.vue'

const store = useStore()
const { t, locale } = useI18n()
const loading = ref(true)

const cart = computed(() => store.state.cart.cart)
const columns = computed(() => [
  { prop: 'title', label: t('cart.magazine') },
  { prop: 'price_cents', label: t('cart.price'), width: 110 },
  { prop: 'quantity', label: t('cart.quantity'), width: 160 },
  { prop: 'line', label: t('cart.lineTotal'), width: 110 },
  { prop: 'actions', label: '', width: 110 },
])

async function run(action, payload) {
  try {
    await store.dispatch(action, payload)
  } catch (e) {
    ElMessage.error(errorMessage(e))
  }
}

onMounted(async () => {
  await run('cart/fetch')
  loading.value = false
})
</script>

<template>
  <section>
    <h1>{{ $t('cart.title') }}</h1>
    <el-empty v-if="!loading && !cart.items.length" :description="$t('cart.empty')">
      <router-link :to="{ name: 'catalog' }">{{ $t('nav.catalog') }}</router-link>
    </el-empty>
    <div v-else class="cart-layout">
      <TheTable :columns="columns" :data="cart.items" :loading="loading">
        <template #cell-title="{ row }">{{ row.title }} <small>{{ row.issue_number }}</small></template>
        <template #cell-price_cents="{ row }">{{ formatMoney(row.price_cents, locale) }}</template>
        <template #cell-quantity="{ row }">
          <el-input-number
            :model-value="row.quantity"
            :min="1"
            :max="row.format === 'digital' ? 1 : row.stock"
            size="small"
            @change="(value) => run('cart/update', { magazineId: row.magazine_id, quantity: value })"
          />
        </template>
        <template #cell-line="{ row }">{{ formatMoney(row.price_cents * row.quantity, locale) }}</template>
        <template #cell-actions="{ row }">
          <TheButton type="danger" @click="run('cart/remove', row.magazine_id)">{{ $t('cart.remove') }}</TheButton>
        </template>
      </TheTable>

      <TheCard :title="$t('cart.summary')" class="cart-summary">
        <dl>
          <dt>{{ $t('cart.subtotal') }}</dt>
          <dd>{{ formatMoney(cart.subtotal_cents, locale) }}</dd>
          <dt>{{ $t('cart.shipping') }}</dt>
          <dd>{{ cart.shipping_cents ? formatMoney(cart.shipping_cents, locale) : $t('cart.free') }}</dd>
          <dt>{{ $t('cart.total') }}</dt>
          <dd><strong>{{ formatMoney(cart.total_cents, locale) }}</strong></dd>
        </dl>
        <h3>{{ $t('checkout.title') }}</h3>
        <CheckoutForm />
      </TheCard>
    </div>
  </section>
</template>

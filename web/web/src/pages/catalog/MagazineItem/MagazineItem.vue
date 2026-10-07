<script setup>
import { computed, onMounted, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import TheButton from '@/components/TheButton/TheButton.vue'
import ErrorMessage from '@/components/system/ErrorMessage.vue'
import { formatDate } from '@/helpers/date'
import { formatMoney } from '@/helpers/money'
import { catalogApi } from '@/plugins/api'
import { errorMessage } from '@/utils/errors'

const route = useRoute()
const router = useRouter()
const store = useStore()
const { t, locale } = useI18n()

const magazine = ref(null)
const quantity = ref(1)
const error = ref('')
const adding = ref(false)

const digital = computed(() => magazine.value?.format === 'digital')
const soldOut = computed(() => !digital.value && magazine.value?.stock === 0)

onMounted(async () => {
  try {
    magazine.value = (await catalogApi.magazine(route.params.slug)).data.data
  } catch (e) {
    error.value = errorMessage(e)
  }
})

async function addToCart() {
  if (!store.getters['auth/isAuthenticated']) return router.push({ name: 'login' })
  adding.value = true
  try {
    await store.dispatch('cart/add', { magazineId: magazine.value.id, quantity: quantity.value })
    ElMessage.success(t('catalog.added'))
  } catch (e) {
    ElMessage.error(errorMessage(e))
  } finally {
    adding.value = false
  }
}
</script>

<template>
  <section>
    <router-link :to="{ name: 'catalog' }">&larr; {{ $t('catalog.title') }}</router-link>
    <ErrorMessage :message="error" />
    <div v-if="magazine" class="magazine-item">
      <div class="magazine-item__cover">{{ magazine.title.charAt(0) }}</div>
      <div>
        <h1>{{ magazine.title }} <small>{{ magazine.issue_number }}</small></h1>
        <p>
          {{ magazine.category?.name }}
          <el-tag size="small">{{ $t(`formats.${magazine.format}`) }}</el-tag>
          <span v-if="magazine.published_at"> · {{ formatDate(magazine.published_at, locale) }}</span>
        </p>
        <p>{{ magazine.description }}</p>
        <p class="magazine-item__price">{{ formatMoney(magazine.price_cents, locale) }}</p>
        <p v-if="!digital">{{ soldOut ? $t('catalog.outOfStock') : $t('catalog.inStock', { count: magazine.stock }) }}</p>
        <div class="magazine-item__buy">
          <el-input-number v-if="!digital" v-model="quantity" :min="1" :max="Math.max(1, magazine.stock)" :disabled="soldOut" />
          <TheButton :loading="adding" :disabled="soldOut" @click="addToCart">{{ $t('catalog.addToCart') }}</TheButton>
        </div>
      </div>
    </div>
  </section>
</template>

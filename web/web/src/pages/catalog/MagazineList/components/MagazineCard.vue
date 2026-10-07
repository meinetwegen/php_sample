<script setup>
import { computed, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import TheButton from '@/components/TheButton/TheButton.vue'
import TheCard from '@/components/TheCard/TheCard.vue'
import { formatMoney } from '@/helpers/money'
import { errorMessage } from '@/utils/errors'

const props = defineProps({ magazine: { type: Object, required: true } })

const store = useStore()
const router = useRouter()
const { t, locale } = useI18n()
const adding = ref(false)

const soldOut = computed(() => props.magazine.format !== 'digital' && props.magazine.stock === 0)

async function addToCart() {
  if (!store.getters['auth/isAuthenticated']) return router.push({ name: 'login' })
  adding.value = true
  try {
    await store.dispatch('cart/add', { magazineId: props.magazine.id })
    ElMessage.success(t('catalog.added'))
  } catch (e) {
    ElMessage.error(errorMessage(e))
  } finally {
    adding.value = false
  }
}
</script>

<template>
  <TheCard class="magazine-card">
    <router-link :to="{ name: 'magazine', params: { slug: magazine.slug } }" class="magazine-card__cover">
      {{ magazine.title.charAt(0) }}
    </router-link>
    <h3 class="magazine-card__title">
      {{ magazine.title }} <small>{{ magazine.issue_number }}</small>
    </h3>
    <p class="magazine-card__meta">
      {{ magazine.category?.name }}
      <el-tag size="small">{{ $t(`formats.${magazine.format}`) }}</el-tag>
    </p>
    <div class="magazine-card__footer">
      <strong>{{ formatMoney(magazine.price_cents, locale) }}</strong>
      <TheButton :loading="adding" :disabled="soldOut" @click="addToCart">
        {{ soldOut ? $t('catalog.outOfStock') : $t('catalog.addToCart') }}
      </TheButton>
    </div>
  </TheCard>
</template>

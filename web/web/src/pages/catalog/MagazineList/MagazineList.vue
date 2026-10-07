<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { PER_PAGE } from '@/config'
import TheFilter from '@/components/TheFilter/TheFilter.vue'
import ErrorMessage from '@/components/system/ErrorMessage.vue'
import { catalogApi } from '@/plugins/api'
import { errorMessage } from '@/utils/errors'
import MagazineCard from './components/MagazineCard.vue'

const filters = ref({ q: '', category: '', format: '', sort: 'newest' })
const categories = ref([])
const state = reactive({ items: [], total: 0, page: 1, loading: false, error: '' })

async function load() {
  state.loading = true
  state.error = ''
  try {
    const { data } = await catalogApi.magazines({ ...filters.value, page: state.page, per_page: PER_PAGE })
    state.items = data.data
    state.total = data.meta.total
  } catch (e) {
    state.error = errorMessage(e)
  } finally {
    state.loading = false
  }
}

watch(filters, () => {
  state.page = 1
  load()
})
watch(() => state.page, load)

onMounted(async () => {
  categories.value = (await catalogApi.categories()).data.data
  load()
})
</script>

<template>
  <section>
    <h1>{{ $t('catalog.title') }}</h1>
    <TheFilter v-model="filters" :categories="categories" />
    <ErrorMessage :message="state.error" />
    <div v-loading="state.loading" class="magazine-grid">
      <MagazineCard v-for="magazine in state.items" :key="magazine.id" :magazine="magazine" />
    </div>
    <el-empty v-if="!state.loading && !state.items.length" :description="$t('catalog.empty')" />
    <el-pagination v-if="state.total > PER_PAGE" v-model:current-page="state.page" :page-size="PER_PAGE" :total="state.total" layout="prev, pager, next" background class="pagination" />
  </section>
</template>

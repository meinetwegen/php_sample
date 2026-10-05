<script setup>
import { computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useStore } from 'vuex'
import CartIcon from '@/components/Icons/CartIcon.vue'
import { LOCALES } from '@/config'

const store = useStore()
const { locale } = useI18n()

const isAuthenticated = computed(() => store.getters['auth/isAuthenticated'])
const isAdmin = computed(() => store.getters['auth/isAdmin'])
const cartCount = computed(() => store.getters['cart/count'])

watch(isAuthenticated, (loggedIn) => loggedIn && store.dispatch('cart/fetch'), { immediate: true })

function setLocale(value) {
  locale.value = value
  localStorage.setItem('locale', value)
}
</script>

<template>
  <div class="default-layout">
    <header class="topbar">
      <router-link :to="{ name: 'catalog' }" class="topbar__brand">{{ $t('app.title') }}</router-link>
      <nav class="topbar__nav">
        <router-link :to="{ name: 'catalog' }">{{ $t('nav.catalog') }}</router-link>
        <template v-if="isAuthenticated">
          <router-link :to="{ name: 'orders' }">{{ $t('nav.orders') }}</router-link>
          <router-link v-if="isAdmin" :to="{ name: 'admin-dashboard' }">{{ $t('nav.admin') }}</router-link>
          <router-link :to="{ name: 'cart' }" class="topbar__cart">
            <CartIcon />
            <el-badge :value="cartCount" :hidden="!cartCount" />
          </router-link>
          <router-link :to="{ name: 'logout' }">{{ $t('nav.logout') }}</router-link>
        </template>
        <template v-else>
          <router-link :to="{ name: 'login' }">{{ $t('nav.login') }}</router-link>
          <router-link :to="{ name: 'register' }">{{ $t('nav.register') }}</router-link>
        </template>
        <a v-for="code in LOCALES" :key="code" href="#" :class="{ 'is-active': locale === code }" @click.prevent="setLocale(code)">{{ code.toUpperCase() }}</a>
      </nav>
    </header>
    <main class="page">
      <slot />
    </main>
  </div>
</template>

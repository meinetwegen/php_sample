<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const items = [
  { name: 'admin-dashboard', label: 'admin.dashboard' },
  { name: 'admin-magazines', label: 'admin.magazines' },
  { name: 'admin-categories', label: 'admin.categories' },
  { name: 'admin-orders', label: 'admin.orders' },
]
</script>

<template>
  <el-container class="admin-layout">
    <el-aside width="220px" class="admin-layout__aside">
      <div class="admin-layout__brand">{{ $t('app.title') }}</div>
      <el-menu :default-active="route.name" router>
        <el-menu-item v-for="item in items" :key="item.name" :index="item.name" :route="{ name: item.name }">{{ $t(item.label) }}</el-menu-item>
        <el-menu-item index="catalog" :route="{ name: 'catalog' }">{{ $t('admin.backToShop') }}</el-menu-item>
        <el-menu-item index="logout" :route="{ name: 'logout' }">{{ $t('nav.logout') }}</el-menu-item>
      </el-menu>
    </el-aside>
    <el-main class="admin-layout__main">
      <slot />
    </el-main>
  </el-container>
</template>

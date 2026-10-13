import { createRouter, createWebHistory } from 'vue-router'
import store from '@/store'
import authRoutes from './apps/auth'
import shopRoutes from './apps/shop'
import adminRoutes from './apps/admin'

const routes = []
routes.push(...authRoutes)
routes.push(...shopRoutes)
routes.push(...adminRoutes)

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach(async (to) => {
  if (store.getters['auth/isAuthenticated'] && !store.state.auth.user) {
    await store.dispatch('auth/fetchUser')
  }

  const loggedIn = store.getters['auth/isAuthenticated']

  if (to.meta.guestOnly && loggedIn) return '/'
  if (to.meta.requiresAuth && !loggedIn) return { name: 'login', query: { redirect: to.fullPath } }
  if (to.meta.requiresAdmin && !store.getters['auth/isAdmin']) return '/'

  return true
})

export default router

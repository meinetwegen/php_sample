export default [
  { path: '/', name: 'catalog', component: () => import('@/pages/catalog/MagazineList/MagazineList.vue'), meta: { layout: 'default' } },
  { path: '/magazines/:slug', name: 'magazine', component: () => import('@/pages/catalog/MagazineItem/MagazineItem.vue'), meta: { layout: 'default' } },
  { path: '/cart', name: 'cart', component: () => import('@/pages/cart/CartPage/CartPage.vue'), meta: { layout: 'default', requiresAuth: true } },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

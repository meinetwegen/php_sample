const admin = { layout: 'admin', requiresAuth: true, requiresAdmin: true }

export default [
  { path: '/admin', name: 'admin-dashboard', component: () => import('@/pages/dashboard/DashboardList/DashboardList.vue'), meta: admin },
  { path: '/admin/magazines', name: 'admin-magazines', component: () => import('@/pages/magazine/MagazineManage/MagazineManage.vue'), meta: admin },
  { path: '/admin/categories', name: 'admin-categories', component: () => import('@/pages/category/CategoryList/CategoryList.vue'), meta: admin },
  { path: '/admin/orders', name: 'admin-orders', component: () => import('@/pages/order/OrderManage/OrderManage.vue'), meta: admin },
]

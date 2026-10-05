export default [
  { path: '/login', name: 'login', component: () => import('@/pages/auth/TheLogin/TheLogin.vue'), meta: { layout: 'auth', guestOnly: true } },
  { path: '/register', name: 'register', component: () => import('@/pages/auth/TheRegister/TheRegister.vue'), meta: { layout: 'auth', guestOnly: true } },
  { path: '/logout', name: 'logout', component: () => import('@/pages/auth/TheLogout/TheLogout.vue'), meta: { layout: 'auth' } },
]

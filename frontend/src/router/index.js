import { createRouter, createWebHistory } from 'vue-router'
import { LocalStorageService } from '@/services/LocalStorageService'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('../components/user/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/',
      name: 'home',
      component: () => import('../components/translation/TranslationTest.vue'),
      meta: { auth: true } 
    },
    {
      path: '/management',
      name: 'management',
      component: () => import('../components/translation/TranslationSearch.vue'),
      meta: { auth: true } 
    },
  ],
})

router.beforeEach((to, from, next) => {
  const token = LocalStorageService.getToken()

  if (to.meta.auth && !token) {
    next({ name: 'login' })
  } 
  else if (to.meta.guest && token) {
    next({ name: 'test-translations' })
  } 
  else {
    next()
  }
})

export default router
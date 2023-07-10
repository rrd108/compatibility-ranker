import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/pages/Home.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/addPerson',
    name: 'AddPerson',
    component: () => import('@/pages/AddPerson.vue'),
  },
  {
    path: '/moondata',
    name: 'Moondata',
    component: () => import('@/pages/Moondata.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router

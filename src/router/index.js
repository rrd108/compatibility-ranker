import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/addPerson',
    name: 'AddPerson',
    component: () => import(/* webpackChunkName: "addPerson" */ '../views/AddPerson.vue')
  },
  {
    path: '/moondata',
    name: 'Moondata',
    component: () => import(/* webpackChunkName: "moondata" */ '../views/Moondata.vue')
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router

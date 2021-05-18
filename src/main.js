import Vue from 'vue'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {
  faBaby,
  faBomb,
  faCheckCircle,
  faClock,
  faExclamationCircle,
  faFemale,
  faGlobe,
  faHeart,
  faInfo,
  faKey,
  faLink,
  faMale,
  faMarsStroke,
  faMeteor,
  faMoneyBillAlt,
  faMoon,
  faSave,
  faSkullCrossbones,
  faSpinner,
  faSync,
  faUserAstronaut,
  faUserPlus,
  faWalking
} from '@fortawesome/free-solid-svg-icons'
import router from './router'

library.add(
  faBaby,
  faBomb,
  faCheckCircle,
  faClock,
  faExclamationCircle,
  faFemale,
  faGlobe,
  faHeart,
  faInfo,
  faKey,
  faLink,
  faMale,
  faMarsStroke,
  faMeteor,
  faMoneyBillAlt,
  faMoon,
  faSave,
  faSkullCrossbones,
  faSpinner,
  faSync,
  faUserAstronaut,
  faUserPlus,
  faWalking
)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')

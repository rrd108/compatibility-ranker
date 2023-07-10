import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
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
  faWalking,
} from '@fortawesome/free-solid-svg-icons'

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

createApp(App)
  .component('font-awesome-icon', FontAwesomeIcon)
  .use(router)
  .mount('#app')

import Vue from 'vue'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faBaby, faBomb, faClock, faFemale, faGlobe, faHeart, faInfo, faKey, faLink, faMale, faMarsStroke, faMeteor, faMoon, faSave, faSpinner, faSync, faUserAstronaut, faUserPlus } from '@fortawesome/free-solid-svg-icons'
import router from './router'

library.add(faBaby, faBomb, faClock, faFemale, faGlobe, faHeart, faInfo, faKey, faLink, faMale, faMarsStroke, faMeteor, faMoon, faSave, faSpinner, faSync, faUserAstronaut, faUserPlus)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')

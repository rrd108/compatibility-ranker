import Vue from 'vue'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faBaby, faBomb, faClock, faGlobe, faHeart, faKey, faLink, faMarsStroke, faMeteor, faMoon, faUserAstronaut, faUserPlus } from '@fortawesome/free-solid-svg-icons'

library.add(faBaby, faBomb, faClock, faGlobe, faHeart, faKey, faLink, faMarsStroke, faMeteor, faMoon, faUserAstronaut, faUserPlus)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.config.productionTip = false

new Vue({
  render: h => h(App),
}).$mount('#app')

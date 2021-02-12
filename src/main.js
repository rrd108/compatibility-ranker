import Vue from 'vue'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faBomb, faHeart, faKey, faLink, faUserAstronaut } from '@fortawesome/free-solid-svg-icons'

library.add(faBomb, faHeart, faKey, faLink, faUserAstronaut)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.config.productionTip = false

new Vue({
  render: h => h(App),
}).$mount('#app')

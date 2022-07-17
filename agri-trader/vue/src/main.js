import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUser } from '@fortawesome/free-regular-svg-icons'
import { faPagelines } from '@fortawesome/free-brands-svg-icons'
import { faCirclePlus, faArrowRightFromBracket } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {BootstrapVue, BootstrapVueIcons, ToastPlugin} from 'bootstrap-vue'
import VueToastr from 'vue-toastr'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.config.productionTip = false
library.add(faCirclePlus, faUser, faArrowRightFromBracket, faPagelines)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(ToastPlugin)
Vue.use(VueToastr)
// window.bootstrap = require('bootstrap')

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')

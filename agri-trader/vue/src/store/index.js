import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/Auth/auth'
import farm from './modules/Farm/farm'
import produce from './modules/Produce/produce'
import project from './modules/Project/project'
import catalog from './modules/Catalog/catalog'
import bidOrder from './modules/BidOrder/bidOrder'
import harvest from './modules/Harvest/harvest'
import delivery from './modules/Delivery/delivery'
import chat from './modules/Chat/chat'
import inventory from './modules/Inventory/inventory'
import dashboard from './modules/Dashboard/dashboard'
import supplier from './modules/Supplier/supplier'
import supply from './modules/Supply/supply'
import supplyPurchaseOrder from './modules/SupplyPurchaseOrder/supplyPurchaseOrder'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(Vuex)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

export default new Vuex.Store({  
  state: {
    loading: false
  },
  actions: {
    readyApp({ commit }){
      setTimeout(() => {
        commit('appReady')
      }, 3000)
      return Promise.resolve('success')
    }
  },
  mutations: {
    appReady: (state) => {
      state.loading = true
    }
  },
  modules: {
    auth,
    farm,
    produce,
    project,
    catalog,
    bidOrder,
    harvest,
    delivery,
    chat,
    inventory,
    dashboard,
    supplier,
    supply,
    supplyPurchaseOrder
  },
})

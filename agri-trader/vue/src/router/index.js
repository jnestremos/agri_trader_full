import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/auth/Dashboard.vue'
import AllFarms from '../views/auth/Farm/AllFarms.vue'
import AddFarm from '../views/auth/Farm/AddFarm.vue'
import AddFarmOwner from '../views/auth/Farm/AddFarmOwner.vue'
import ShowFarm from '../views/auth/Farm/ShowFarm.vue'
import AllProduces from '../views/auth/Produce/AllProduces.vue'
import AddProduce from '../views/auth/Produce/AddProduce.vue'
import ShowProduce from '../views/auth/Produce/ShowProduce.vue'
import AllProjects from '../views/auth/Project/AllProjects.vue'
import ShowProject from '../views/auth/Project/ShowProject.vue'
import AddProject from '../views/auth/Project/AddProject.vue'
import Catalog from '../views/auth/Catalog/Catalog.vue'
import BidOrderProgress from '../views/auth/BidOrder/BidOrderProgress.vue'
import ShowBidOrder from '../views/auth/BidOrder/ShowBidOrder.vue'
import AuthLayout from '../components/AuthLayout.vue'
import DistributorLayout from '../components/DistributorLayout.vue'
import GuestLayout from '../components/GuestLayout.vue'
import ErrorPage from '../views/404.vue'
import auth from '../store/modules/Auth/auth'
import store from '../store'

Vue.use(VueRouter)

const routes = [ 
  {
    path:'/', 
    redirect: '/dashboard',    
    component: AuthLayout,
    children: [
      {
        path: '/dashboard', 
        name: 'Dashboard',  
        meta: {needsAuth : true, role:'trader'},    
        component: Dashboard
      },
      {
        path: '/farms', 
        name: 'Farms',  
        meta: {needsAuth : true, role:'trader'},    
        component: AllFarms
      },
      {
        path: '/farms/add', 
        name: 'AddFarm',  
        meta: {needsAuth : true, role:'trader'},    
        component: AddFarm
      },
      {
        path: '/farms/owners/add', 
        name: 'AddFarmOwner',  
        meta: {needsAuth : true, role:'trader'},    
        component: AddFarmOwner
      },
      {
        path: '/farm/:id', 
        name: 'ShowFarm',  
        meta: {needsAuth : true, role:'trader'},    
        component: ShowFarm
      },
      {
        path: '/produces', 
        name: 'AllProduces',  
        meta: {needsAuth : true, role:'trader'},    
        component: AllProduces
      },
      {
        path: '/produces/add', 
        name: 'AddProduce',  
        meta: {needsAuth : true, role:'trader'},    
        component: AddProduce
      },
      {
        path: '/produce/:id', 
        name: 'ShowProduce',  
        meta: {needsAuth : true, role:'trader'},    
        component: ShowProduce
      },
      {
        path: '/projects',
        name: 'AllProjects', 
        meta: {needsAuth : true, role:'trader'},
        component: AllProjects
      },
      {
        path: '/projects/:id',
        name: 'ShowProject', 
        meta: {needsAuth : true, role:'trader'},
        component: ShowProject
      },
      {
        path: '/project/add',
        name: 'AddProject', 
        meta: {needsAuth : true, role:'trader'},
        component: AddProject
      },
    ]
  }, 
  {
    path: '/distributor',
    redirect: '/catalog',
    component: DistributorLayout,
    children: [
      {
        path: '/catalog',
        name: 'Catalog',
        meta: {needsAuth: true, role: 'distributor'},
        component: Catalog
      },
      {
        path: '/bid_order/progress/:id',
        name: 'BidOrderProgress',
        meta: {needsAuth: true, role: 'distributor'},
        component: BidOrderProgress
      },
      {
        path: '/bid_order/project/:id',
        name: 'BidOrderProject',
        meta: {needsAuth: true, role: 'distributor'},
        component: ShowBidOrder
      },
      {
        path: '/bid_order/on_hand/:id',
        name: 'BidOrderOnHand',
        meta: {needsAuth: true, role: 'distributor'},
        component: ShowBidOrder
      },
    ]
  }, 
  {
    path:'/login', 
    redirect:'/login/distributor'
  },
  {
    path: '/guest', 
    redirect: '/login/distributor',     
    component: GuestLayout,
    children: [
      {
        path: '/login/distributor',
        name: 'LoginDistributor',    
        meta: {needsAuth: false},         
        component: Login
        
      },
      {
        path: '/login/trader',
        name: 'LoginTrader',    
        meta: {needsAuth: false},         
        component: Login
        
      },
      {
        path: '/register/distributor',
        name: 'RegisterDistributor',  
        meta: {needsAuth: false},            
        component: Register
      },     
      {
        path: '/register/trader',
        name: 'RegisterTrader',  
        meta: {needsAuth: false},            
        component: Register
      },     
    ]
  },
  {
    path: '/404',
    name: 'ErrorPage',    
    meta: {needsAuth: false},
    component: ErrorPage
  },
  {
    path: '/:catchAll(.*)',
    redirect: '/404',    
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach((to, from, next) => {
  store.state.loading = false
    
  if(!to.meta.needsAuth){    
    store.state.loading = true
  }
  // else{
  //   store.watch(
  //     (state) => state.loading,
  //     (ready) => {
  //         if(ready){              
  //           proceed()
  //           console.log(to)
  //         }
  //     }
  //   )
  // }      
  
  // else if(!to.meta.needsAuth){
  //   store.state.loading = true
  // } 
  // else{    
  //   store.state.loading = false
  // }  
  proceed() 
  
  
  
  function proceed(){
    if(to.meta.needsAuth && !auth.state.user.api_token){    
      console.log(1)
      next({name: 'LoginDistributor'});
      
    }    
    else if(!to.meta.needsAuth && auth.state.user.api_token){
      console.log(1)
      if(auth.state.user.role == 'trader'){
        console.log(1)
        next({name: 'Dashboard'});
      }   
      else{
        console.log(1)
        next({name: 'Catalog'});
      } 
      
    }
    else{
      if(to.meta.role == 'distributor' && auth.state.user.role == 'trader'){
        console.log(1)
        next({name: 'Dashboard'});
      }
      else if(to.meta.role == 'trader' && auth.state.user.role == 'distributor'){        
        console.log(1)
        next({name: 'Catalog'});
      }
      else{
        console.log(1)
        next();
      }     
    }
  }
  
});



export default router

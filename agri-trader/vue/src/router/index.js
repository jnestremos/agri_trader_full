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
import BidOrderHistory from '../views/auth/BidOrder/BidOrderHistory.vue'
import HarvestDetails from '../views/auth/Harvest/HarvestDetails.vue'
import DeliveryDetails from '../views/auth/Delivery/DeliveryDetails.vue'
import AllBidOrders from '../views/auth/BidOrder/trader/AllBidOrders.vue'
import AllBidOrdersFiltered from '../views/auth/BidOrder/trader/AllBidOrdersFiltered.vue'
import DistributorListOfBids from '../views/auth/BidOrder/trader/DistributorListOfBids.vue'
// import BidOrderDetails from '../views/auth/BidOrder/trader/BidOrderDetails.vue'
import BidOrderDetailss from '../views/auth/BidOrder/trader/BidOrderDetailss.vue'
import ChangeBidOrder from '../views/auth/BidOrder/trader/ChangeBidOrder.vue'
import TraderMessaging from '../views/auth/Messaging/trader/TraderMessaging.vue'
import DistMessaging from '../views/auth/Messaging/DistMessaging.vue'
import ProduceInventory from '../views/auth/ProduceInventory/ProduceInventory.vue'
import RefundDetails from '../views/auth/Refund/RefundDetails.vue'
import AddSupplier from '../views/auth/Supplier/AddSupplier.vue'
import SupplierList from '../views/auth/Supplier/SupplierList.vue'
import SupplierDetails from '../views/auth/Supplier/SupplierDetails.vue'
import AddSupply from '../views/auth/Supply/AddSupply.vue'
import AddFarmWorker from '../views/auth/Farm Worker/AddFarmWorker.vue'
import InitialPurchaseOrder from '../views/auth/Supply Purchase Order/InitialPurchaseOrder.vue'
import PurchaseOrderSummary from '../views/auth/Supply Purchase Order/PurchaseOrderSummary.vue'
import PurchaseOrderPayment from '../views/auth/Supply Purchase Order/POPayment.vue'
import PurchaseOrderDashboard from '../views/auth/Supply Purchase Order/PurchaseOrderDashboard.vue'
import PurchaseReturnDashboard from '../views/auth/Supply Purchase Order/PurchaseReturnDashboard.vue'
import PaymentDashboard from '../views/auth/Supply Purchase Order/PaymentDashboard.vue'
import PurchaseOrderStatus from '../views/auth/Supply Purchase Order/PurchaseOrderStatus.vue'
import InitialStockIn from '../views/auth/Stock In Inventory/InitialStockIn.vue'
import IncomeSummary from '../views/auth/Reports/IncomeSummary.vue'
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
      {
        path: '/bid/orders',
        name: 'AllBidOrders', 
        meta: {needsAuth: true, role:'trader'},
        component: AllBidOrders
      },
      {
        path: '/bid/orders/filter',
        name: 'AllBidOrdersFiltered', 
        meta: {needsAuth: true, role:'trader'},
        component: AllBidOrdersFiltered
      },
      {
        path: '/bid/orders/:id',
        name: 'ShowBidOrder',
        meta: {needsAuth: true, role:'trader'},
        component: BidOrderDetailss
      },
      {
        path: '/bid/orders/:id/bids',
        name: 'DistributorListOfBids',
        meta: {needsAuth: true, role:'trader'},
        component: DistributorListOfBids
      },
      {
        path: '/income/summary',
        name: 'IncomeSummary',
        meta: {needsAuth: true, role:'trader'},
        component: IncomeSummary
      },
      // {
      //   path: '/bid/orderss/:id',
      //   name: 'ShowBidOrderr',
      //   meta: {needsAuth: true, role:'trader'},
      //   component: BidOrderDetailss
      // },
      {
        path: '/bid/orders/:id/refund',
        name: 'RefundDetails',
        meta: {needsAuth: true, role:'trader'},
        component: RefundDetails
      },
      {
        path: '/bid/orders/:id/renegotiate',
        name: 'ChangeBidOrder',
        meta: {needsAuth: true, role:'trader'},
        component: ChangeBidOrder
      },
      {
        path: '/harvest/:id',
        name: HarvestDetails,
        meta: {needsAuth: true, role:'trader'},
        component: HarvestDetails
      },
      {
        path: '/delivery/:id',
        name: 'DeliveryDetails',
        meta: {needsAuth: true, role:'trader'},
        component: DeliveryDetails
      },
      {
        path: '/messages/:id/trader',
        name: 'TraderMessaging',
        meta: {needsAuth: true, role:'trader'},
        component: TraderMessaging
      },
      {
        path: '/harvest/inventory/:id',
        name: 'ProduceInventory',
        meta: {needsAuth: true, role:'trader'},
        component: ProduceInventory
      },
      {
        path:"/supplier/add",
        name:"AddSupplier",
        meta:{needsAuth: true, role: "trader"},
        component:AddSupplier
      },
      {
        path:"/supplier/list",
        name:"SupplierList",
        meta:{needsAuth: true, role: "trader"},
        component:SupplierList
      },
      {
        path:"/supplier/:id",
        name:"SupplierDetails",
        meta:{needsAuth: true, role: "trader"},
        component:SupplierDetails
      },
      {
        path:"/supply/add",
        name:"AddSupply",
        meta:{needsAuth: true, role: "trader"},
        component:AddSupply
      },
      {
        path:"/farm_worker/add",
        name:"AddFarmWorker",
        meta:{needsAuth: true, role: "trader"},
        component:AddFarmWorker
      },
      {
        path:"/supplyOrder/add",
        name:"InitialPurchaseOrder",
        meta:{needsAuth: true, role: "trader"},
        component:InitialPurchaseOrder
      },
      {
        path:"/supplyOrder/orderSummary",
        name:"PurchaseOrderSummary",
        meta:{needsAuth: true, role: "trader"},
        component:PurchaseOrderSummary
      },
      {
        path:"/supplyOrder/payment",
        name:"PurchaseOrderPayment",
        meta:{needsAuth: true, role: "trader"},
        component:PurchaseOrderPayment
      },
      {
        path:"/supplyOrder/statusDashboard",
        name:"PurchaseOrderDashboard",
        meta:{needsAuth: true, role: "trader"},
        component:PurchaseOrderDashboard
      },
      {
        path:"/supplyOrder/returnsDashboard",
        name:"PurchaseReturnDashboard",
        meta:{needsAuth: true, role: "trader"},
        component:PurchaseReturnDashboard
      },
      {
        path:"/supplyOrder/outstandingBalance",
        name:"PaymentDashboard",
        meta:{needsAuth: true, role: "trader"},
        component:PaymentDashboard
      },
      {
        path:"/supplyOrder/:id",
        name:"PurchaseOrderStatus",
        meta:{needsAuth: true, role: "trader"},
        component:PurchaseOrderStatus
      },
      {
        path:"/stockIn/",
        name:"InitialStockIn",
        meta:{needsAuth: true, role: "trader"},
        component:InitialStockIn
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
        path: '/bid_order/on_hand/:farm_id/:produce_trader_id',
        name: 'BidOrderOnHand',
        meta: {needsAuth: true, role: 'distributor'},
        component: ShowBidOrder
      },
      {
        path: '/bid_order/history',
        name: 'BidOrderHistory',
        meta: {needsAuth: true, role: 'distributor'},
        component: BidOrderHistory
      },
      {
        path: '/messages/:id/dist',
        name: 'DistMessaging',
        meta: {needsAuth: true, role:'distributor'},
        component: DistMessaging
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

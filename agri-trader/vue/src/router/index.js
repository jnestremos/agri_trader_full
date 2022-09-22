import Vue from "vue";
import VueRouter from "vue-router";
import AuthLayout from "../components/AuthLayout.vue"
import AddSupplier from "../views/Supplier/AddSupplier.vue"
import SupplierDetails from "../views/Supplier/SupplierDetails.vue"
import AddSupply from "../views/Supply/AddSupply.vue"
import AddFarmWorker from "../views/Farm Worker/AddFarmWorker.vue"
import InitialPurchaseOrder from "../views/Supply Purchase Order/InitialPurchaseOrder.vue"
import PurchaseOrderSummary from "../views/Supply Purchase Order/PurchaseOrderSummary.vue"
import PurchaseOrderPayment from "../views/Supply Purchase Order/POPayment.vue"
import PurchaseOrderDashboard from "../views/Supply Purchase Order/PurchaseOrderDashboard.vue"
import SupplierList from "../views/Supplier/SupplierList.vue"
// import Home from "../views/Home.vue";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    redirect: "/supplier/add",
    component: AuthLayout,
    children:[
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
      
    ]
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});

export default router;

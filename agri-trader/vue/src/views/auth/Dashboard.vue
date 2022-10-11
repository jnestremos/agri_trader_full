<template>
  <div id="dashboard" class="vh-100 px-5 py-5 d-flex flex-column justify-content-between" style="width:92vw;">
    <div>
      <h3>Welcome!</h3>
      <h2>{{ getName }}</h2>
    </div>
    <div class="mb-5 mt-3">      
      <div class="container-fluid">
      <div class="row">
        <div class="col-6">
          <h5>Total Sales</h5>
          <div style="height:300px; background-color:greenyellow; border-radius:20px;" class="px-3 pb-3">
            <TotalSalesGraph style="height:100%; width:100%;" v-if="getRole == 'trader' && getTotalSales && getTotalSales.length > 0" :chartData="getTotalSales" label="Total Sales"/>
            <TotalSalesOwnerGraph style="height:100%; width:100%;" v-else-if="getRole == 'farm_owner' && salesOwner && salesOwner.length > 0" :chartData="salesOwner" :labels="salesLabels"/>
          </div>
        </div>
        <!-- <div class="col-4">
          <h5>Lead Produce in Sales</h5>
          <div style="height:300px; background-color:greenyellow; border-radius:20px;">

          </div>
        </div> -->
        <div class="col-6">
          <h5 v-if="getRole == 'trader'">Income Summary</h5>
          <h5 v-else>Profit Sharing</h5>
          <div style="height:300px; background-color:greenyellow; border-radius:20px; cursor:pointer" class="px-3 pb-3" @click="redirectIncomeSummary()">
            <IncomeSummaryGraph style="height:100%; width:100%;" v-if="getRole == 'trader' && getIncomeSumm" :chartData="getIncomeSumm" label="Income"/>
            <ProfitSharingOwnerGraph style="height:100%; width:100%;" v-else-if="getRole == 'farm_owner' && profitsOwner && profitsOwner.length > 0" :chartData="profitsOwner" :labels="shareLabels"/>
          </div>
        </div>
      </div>
    </div>
    </div>     
    
    <div>
      <h5>Expenses</h5>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div style="height:350px; background-color:grey; border-radius:20px;">

            </div>
          </div>
        </div>
      </div> 
    </div>    
  </div>
</template>

<script>
import { format } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
import TotalSalesGraph from '../../components/TotalSalesGraph.vue';
import IncomeSummaryGraph from '../../components/IncomeSummaryGraph.vue';
import TotalSalesOwnerGraph from "../../components/TotalSalesOwnerGraph.vue"
import ProfitSharingOwnerGraph from "../../components/ProfitSharingOwnerGraph.vue"
import auth from '../../store/modules/Auth/auth';
export default {
    name: 'Dashboard',
    created(){
      if(auth.state.user.role == 'trader'){
        this.fetchDashboardData()
        .then(() => {
          this.readyApp()
        })   
      }  
      else{
        this.fetchOwnerDashboardData()
        .then(() => {
          if(this.getDashboardOwnerData.sales && this.getDashboardOwnerData.profit_sharings){
            var sales = [];        
            var dates = new Set()
            this.getDashboardOwnerData.sales.forEach((sale) => {
              dates.add(format(new Date(sale.created_at), 'yyyy-MM-dd'))
            })
            var arr = []
            dates.forEach((date) => {
              arr.push(date)
            })           
            if(arr.length > 5){
              arr.splice(0, arr.length - 5)
            }    
            var salesLabels = arr   
            salesLabels.forEach((date) => {       
              var salesObj = this.getDashboardOwnerData.sales.filter((s) => {
                return date === format(new Date(s.created_at), 'yyyy-MM-dd')
              })
              var totals = []
              salesObj.forEach((ss) => {
                totals.push(ss.sale_total)
              })
              var total = totals.reduce((a, b) => a + b, 0)
              sales.push(total)
            })

            dates.clear()   
            var owner_shares = [];
            this.getDashboardOwnerData.profit_sharings.forEach((share) => {
              dates.add(format(new Date(share.created_at), 'yyyy-MM-dd'))
            })
            var arr2 = [];
            dates.forEach((date) => {
              arr2.push(date)
            })
            if(arr2.length > 5){
              arr2.splice(0, arr2.length - 5)
            }  
            var shareLabels = arr2
            shareLabels.forEach((date) => {       
              var shareObj = this.getDashboardOwnerData.profit_sharings.filter((s) => {
                return date === format(new Date(s.created_at), 'yyyy-MM-dd')
              })
              var shares = [];
              shareObj.forEach((ss) => {
                shares.push(ss.ar_ownerShare)
              })
              var total2 = shares.reduce((a, b) => a + b, 0)
              owner_shares.push(total2)
            })
            this.salesOwner = sales
            this.salesLabels = salesLabels
            this.shareLabels = shareLabels
            this.profitsOwner = owner_shares
          }         
          this.readyApp()
        })        
      }       
    }, 
    data(){
      return {
        salesOwner: null,
        profitsOwner: null,
        salesLabels: null,
        shareLabels: null,        
      }
    },
    components: { TotalSalesGraph, IncomeSummaryGraph, TotalSalesOwnerGraph, ProfitSharingOwnerGraph },      
    computed: {
        ...mapGetters(['getName', 'getTotalSales', 'getIncomeSumm', 'getDashboardOwnerData']),   
        getRole(){
          return auth.state.user.role
        }     
    },    
    methods: {
      ...mapActions(['readyApp', 'fetchDashboardData', 'fetchOwnerDashboardData']),
      redirectIncomeSummary(){
        this.$router.push({ name: 'IncomeSummary' })
      }
    }
    
}
</script>

<style>

</style>
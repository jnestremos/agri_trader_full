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
            <TotalSalesGraph style="height:100%; width:100%;" v-if="getTotalSales && getTotalSales.length > 0" :chartData="getTotalSales" label="Total Sales"/>
          </div>
        </div>
        <!-- <div class="col-4">
          <h5>Lead Produce in Sales</h5>
          <div style="height:300px; background-color:greenyellow; border-radius:20px;">

          </div>
        </div> -->
        <div class="col-6">
          <h5>Income Summary</h5>
          <div style="height:300px; background-color:greenyellow; border-radius:20px; cursor:pointer" class="px-3 pb-3" @click="redirectIncomeSummary()">
            <IncomeSummaryGraph style="height:100%; width:100%;" v-if="getIncomeSumm" :chartData="getIncomeSumm" label="Income"/>
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
import { mapActions, mapGetters } from 'vuex'
import TotalSalesGraph from '../../components/TotalSalesGraph.vue';
import IncomeSummaryGraph from '../../components/IncomeSummaryGraph.vue';
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
        this.readyApp()
      }       
    },  
    components: { TotalSalesGraph, IncomeSummaryGraph },      
    computed: {
        ...mapGetters(['getName', 'getTotalSales', 'getIncomeSumm']),        
    },    
    methods: {
      ...mapActions(['readyApp', 'fetchDashboardData']),
      redirectIncomeSummary(){
        this.$router.push({ name: 'IncomeSummary' })
      }
    }
    
}
</script>

<style>

</style>
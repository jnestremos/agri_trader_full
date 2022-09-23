<template>
  <div id="dashboard" class="vh-100 px-5 py-5 d-flex flex-column justify-content-between" style="width:92vw;">
    <div>
      <h3>Welcome!</h3>
      <h2>{{ getName }}</h2>
    </div>
    <div class="mb-5 mt-3">      
      <div class="container-fluid">
      <div class="row">
        <div class="col-9">
          <h5>Total Sales</h5>
          <div style="height:300px; background-color:greenyellow; border-radius:20px;" class="px-3 pb-3">
            <TotalSalesGraph style="height:100%; width:100%;" v-if="getTotalSales" :chartData="getTotalSales" label="Total Sales"/>
          </div>
        </div>
        <!-- <div class="col-4">
          <h5>Lead Produce in Sales</h5>
          <div style="height:300px; background-color:greenyellow; border-radius:20px;">

          </div>
        </div> -->
        <div class="col-3">
          <h5>Lead Produce in Sales</h5>
          <div style="height:300px; background-color:greenyellow; border-radius:20px;">

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
export default {
    name: 'Dashboard',
    created(){
      this.fetchDashboardData()
      .then(() => {
        this.readyApp()
      })      
    },  
    components: { TotalSalesGraph },      
    computed: {
        ...mapGetters(['getName', 'getTotalSales']),        
    },    
    methods: {
      ...mapActions(['readyApp', 'fetchDashboardData'])
    }
    
}
</script>

<style>

</style>
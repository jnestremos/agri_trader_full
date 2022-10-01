<template>
  <div class="showProduceForOwner px-3">
    <div class="container-fluid w-100 d-flex align-items-center" style="height:10%;">
        <h3 v-if="getProduceInfoForOwner.produce">{{ getProduceInfoForOwner.produce.prod_name + ' ' + getProduceInfoForOwner.produce.prod_type }}</h3>        
    </div>
    <div class="container-fluid d-flex justify-content-between mb-5" style="width:70%; float:left;">
      <div>
        <h4 v-if="getProduceInfoForOwner.produce">Time To Harvest: {{ getProduceInfoForOwner.produce.prod_timeOfHarvest }}</h4>
        <h4 v-if="getProduceInfoForOwner.produce_trader && getProduceInfoForOwner.produce_trader.produce_numOfGrades > 1">Grades: A, B, C</h4>
        <h4 v-else>Grades: None</h4>
        <!-- <h4>Farms Associated: {{ getProduceDetails.prod_numOfFarms }}</h4> -->
        <h4 v-if="getProduceInfoForOwner.produce_history && getProduceInfoForOwner.produce_history.length > 0">Last Date of Harvest: {{ getProduceInfoForOwner.produce_history[getProduceInfoForOwner.produce_history.length - 1].produce_yield_dateHarvestTo }}</h4>
        <h4 v-else>Last Date of Harvest: No Harvest Date Recorded</h4>
      </div>
      <div class="">
        <h4 v-if="getProduceInfoForOwner.produce_trader && getProduceInfoForOwner.produce_trader.produce_numOfGrades > 1">Prices (per class)</h4>
        <h4 v-else>Price</h4>
        <div v-if="getProduceInfoForOwner.produce_trader && getProduceInfoForOwner.produce_trader.produce_numOfGrades > 1">
           <div class="d-flex">
            <h4 class="me-5">A: {{ getProduceInfoForOwner.produce_history && getProduceInfoForOwner.produce_history[2] ? getProduceInfoForOwner.produce_history[2].produce_yield_price : 'No Price Set' }}</h4>
            <h4>&NonBreakingSpace; B: {{ getProduceInfoForOwner.produce_history && getProduceInfoForOwner.produce_history[1] ? getProduceInfoForOwner.produce_history[1].produce_yield_price : 'No Price Set' }}</h4>
          </div> 
          <h4>C: {{ getProduceInfoForOwner.produce_history && getProduceInfoForOwner.produce_history[0] ? getProduceInfoForOwner.produce_history[0].produce_yield_price : 'No Price Set' }}</h4>                 
        </div>
        <h4 v-else>{{ getProduceInfoForOwner.produce_history && getProduceInfoForOwner.produce_history[0] ? getProduceInfoForOwner.produce_history[0].produce_yield_price : 'No Price Set' }}</h4>
      </div>
    </div>
    <div style="width:100%; height:65%; clear:left;">
      <table id="example" class="table table-striped" style="width:100%; height:100%;">
        <thead>
            <tr>
                <th>Project ID</th>
                <th>Farm Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Date of Stock-in</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(record, index) in getProduceInfoForOwner.produce_history" :key="index">
                <td>{{ record.project_id }}</td>
                <td>{{ getFarmName(record) }}</td>
                <td>{{ record.produce_yield_qtyHarvested }}</td>
                <td>{{ record.produce_yield_price }}</td>
                <td>{{ record.produce_yield_dateHarvestTo }}</td>
                <td>{{ record.produce_yield_class }}</td>
            </tr>
        </tbody>    
      </table>
    </div>     
  </div>
</template>

<script>
import DataTable from 'datatables.net-bs4'    
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'ShowProduceForOwner',
    created(){
        this.fetchProduceForOwner(this.$route.params.id)
        .then(() => {
            new DataTable('#example', {
                scrollY: 400,
                pageLength: 2
            })             
            this.readyApp()
        })        
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProduceForOwner']),
        getFarmName(record){
            var projObj = this.getProduceInfoForOwner.projects.filter((p) => {
                return parseInt(record.project_id) === parseInt(p.id)
            })
            var contractObj = this.getProduceInfoForOwner.contracts.filter((c) => {
                return parseInt(projObj[0].contract_id) === parseInt(c.id)
            })
            return contractObj[0].farm_name
        }
    },
    computed: {
        ...mapGetters(['getProduceInfoForOwner'])
    }
}
</script>

<style>

</style>
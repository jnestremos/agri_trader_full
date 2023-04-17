<template>
  <div class="showProduce pb-5 pe-4">
    <div class="container-fluid w-100 d-flex align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3 v-if="getProduceDetails.produce">{{ getProduceDetails.produce.prod_name + ' ' + getProduceDetails.produce.prod_type }}</h3>        
    </div>  
    <div class="container-fluid d-flex justify-content-between mb-5 mt-2" style="width:80%; float:left;">
      <div>
        <h4  v-if="getProduceDetails.produce">Time To Harvest: {{ getProduceDetails.produce.prod_timeOfHarvest }}</h4>
        <h4  v-if="getProduceDetails.produce_trader && getProduceDetails.produce_trader[0].produce_numOfGrades > 1">Grades: A, B, C</h4>
        <h4  v-else>Grades: None</h4>
        <h4  v-if="getProduceDetails.produce_trader && getProduceDetails.produce_trader.length > 0">Farms Associated: {{ getProduceDetails.produce_trader[0].prod_numOfFarms }}</h4>
        <h4  v-if="getProduceDetails.produce_yields && getProduceDetails.produce_yields.length > 0">Last Date of Harvest: {{ getProduceDetails.produce_yields[0].produce_yield_dateHarvestTo }}</h4>
        <h4  v-else>Last Date of Harvest: No Harvest Date Recorded</h4>
      </div>
      <div class="">
        <h4 class="form-row ms-5" v-if="getProduceDetails.produce_trader && getProduceDetails.produce_trader[0].produce_numOfGrades > 1">Prices (Per Class)</h4>
        <h4 v-else>Price</h4>
        <div v-if="getProduceDetails.produce_trader && getProduceDetails.produce_trader[0].produce_numOfGrades > 1">
          <div class="form-row">
            <h4 class="ms-5">A: {{ getProduceDetails.produce_yields && getProduceDetails.produce_yields.length > 0 ? getProduceDetails.produce_yields[2].produce_yield_price.toLocaleString("en-PH") : 'No Price Data!' }}</h4>
            <h4 class="ms-5">B: {{ getProduceDetails.produce_yields && getProduceDetails.produce_yields.length > 0 ? getProduceDetails.produce_yields[1].produce_yield_price.toLocaleString("en-PH") : 'No Price Data!' }}</h4>
            <h4 class="ms-5">C: {{ getProduceDetails.produce_yields && getProduceDetails.produce_yields.length > 0 ? getProduceDetails.produce_yields[0].produce_yield_price.toLocaleString("en-PH") : 'No Price Data!' }}</h4>
          </div>                  
        </div>
        <h4 v-else>{{ getProduceDetails.produce_yields && getProduceDetails.produce_yields.length > 0 ? getProduceDetails.produce_yields[0].produce_yield_price.toLocaleString("en-PH") : 'No Price Data!' }}</h4>
      </div>
    </div>
    <div style="width:100%; height:65%; clear:left;">
      <table id="example" class="table table-striped" style="width:100%; height:100%;">
        <thead>
            <tr>
                <th>Farm Name</th>
                <th>Farm Owner</th>
                <th>Quantity</th>
                <th>Date of Stock-in</th>
                <th>Grade</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
          <tr v-for="(record, index) in getProduceDetails.produce_yields" :key="index">
            <td>{{ getFarmName(record) }}</td>
            <td>{{ getFarmOwner(record) }}</td>
            <td>{{ record.produce_yield_qtyHarvested }}</td>
            <td>{{ record.produce_yield_dateHarvestTo }}</td>
            <td>{{ record.produce_yield_class }}</td>
            <td>{{ record.produce_yield_price | toCurrency }}</td>
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
    name: "ShowProduce",
    created(){
        this.readyApp()
        .then(() => {
            this.fetchProduce(this.$route.params.id) 
            .then(() => {
              new DataTable('#example', {
                scrollY: 200,
                pageLength: 5
              })  
            })
          .catch((err) => {
              console.log(err)
              this.$router.push({ name: 'AllProduces' })
            })                                               
         })                 
    },    
    computed: {
        ...mapGetters(['getProduceDetails'])
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProduce']),
        getFarmName(record){
          var projObj = this.getProduceDetails.projects.filter((p) => {
            return parseInt(record.project_id) === parseInt(p.id)
          })
          var contractObj = this.getProduceDetails.contracts.filter((c) => {
            return parseInt(projObj[0].contract_id) === parseInt(c.id)
          })
          return contractObj[0].farm_name
        },
        getFarmOwner(record){
          var projObj = this.getProduceDetails.projects.filter((p) => {
            return parseInt(record.project_id) === parseInt(p.id)
          })
          var contractObj = this.getProduceDetails.contracts.filter((c) => {
            return parseInt(projObj[0].contract_id) === parseInt(c.id)
          })
          var farmObj = this.getProduceDetails.farms.filter((f) => {
            return parseInt(contractObj[0].farm_id) === parseInt(f.id)
          })
          var ownerObj = this.getProduceDetails.farm_owners.filter((o) => {
            return parseInt(farmObj[0].farm_owner_id) === parseInt(o.id)
          })
          return ownerObj[0].owner_firstName + ' ' + ownerObj[0].owner_lastName
        }
    },    
}
</script>

<style scoped>

</style>
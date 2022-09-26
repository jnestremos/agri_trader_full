<template>
  <div class="showProduce px-3">
    <div class="container-fluid w-100 d-flex align-items-center" style="height:10%;">
        <h3>{{ getProduceDetails.prod_name }}</h3>        
    </div>  
    <div class="container-fluid d-flex justify-content-between mb-5" style="width:70%; float:left;">
      <div>
        <h4>Time To Harvest: {{ getProduceDetails.prod_timeOfHarvest }}</h4>
        <h4 v-if="getProduceDetails.produce_numOfGrades > 1">Grades: A, B, C</h4>
        <h4 v-else>Grades: None</h4>
        <h4>Farms Associated: {{ getProduceDetails.prod_numOfFarms }}</h4>
        <h4 v-if="getProduceDetails.produce_yield_dateHarvestTo">Expected Date of Harvest: {{ getProduceDetails.produce_yield_dateHarvestTo }}</h4>
        <h4 v-else>Expected Date of Harvest: No Harvest Date Recorded</h4>
      </div>
      <div class="">
        <h4 v-if="getProduceDetails.produce_numOfGrades > 1">Prices (per class)</h4>
        <h4 v-else>Price</h4>
        <div v-if="getProduceDetails.produce_numOfGrades > 1">
           <div class="d-flex">
            <h4 class="me-5">A: 150.00</h4>
            <h4>&NonBreakingSpace; B: 120.00</h4>
          </div> 
          <h4>C: 100.00</h4>                 
        </div>
        <h4 v-else>100.00</h4>
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
            </tr>
        </thead>
        <tbody>

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
                scrollY: 400,
                pageLength: 2
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
        ...mapActions(['readyApp', 'fetchProduce'])
    },    
}
</script>

<style scoped>

</style>
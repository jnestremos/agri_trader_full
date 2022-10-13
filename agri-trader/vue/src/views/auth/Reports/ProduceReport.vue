<template>
    <div class="ProduceReport">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Produce Report</h3>        
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Produce</label>
                  <select class="form-select" @change="setProduce($event)">
                      <option value="None">Select Produce</option>
                      <option v-for="(produce, index) in getProduceReport.produces" :key="index" :value="produce.id">{{ produce.prod_name + ' ' + produce.prod_type }}</option>
                  </select>
              </div>
          </div>
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Produce Name</th>
                          <th scope="col">Associated Farms</th>
                          <th scope="col">Time To Harvest</th>
                          <th scope="col">Last Date of Harvest</th>
                          <th scope="col">Latest Price</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                      <tr style="cursor:pointer;" v-for="(produce, index) in filteredTable" :key="index" @click="$router.push({ path: `/produce/${produce.produce_id}` })">
                        <td>{{ getProduceName(produce) }}</td>
                        <td>{{ produce.prod_numOfFarms }}</td>
                        <td>{{ produce.prod_timeOfHarvest }}</td>
                        <td>{{ produce.prod_lastDateOfHarvest }}</td>
                        <td>{{ getLatestPrice(produce) }}</td>                        
                      </tr>                                                                        
                  </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </template>
  
<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: "ProduceReport",
    created() {
        this.fetchProduceReport()
        .then(() => {
            this.readyApp()
        })        
    },
    data(){
        return {
            filter_produce: "None"
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProduceReport']),
        setProduce(e){
            this.filter_produce = e.target.value
        },
        getProduceName(produce){
            var prodObj = this.getProduceReport.produces.filter((p) => {
                return parseInt(produce.produce_id) === parseInt(p.id)
            })
            var arr = produce.prod_name.split('(Class')
            if(arr.indexOf("(Class") != -1){
                arr.splice(arr.indexOf("(Class"), 0, prodObj[0].prod_type)
                return arr.join(' ')
            }
            else{
                return produce.prod_name + ' ' + prodObj[0].prod_type
            }
        },
        getLatestPrice(produce){
            var farmProdObj = this.getProduceReport.farm_produces.filter((p) => {
                return parseInt(produce.id) === parseInt(p.produce_trader_id)
            })
            farmProdObj = farmProdObj.sort((a, b) => {
                return new Date(a.prod_lastDateOfHarvest) - new Date(b.prod_lastDateOfHarvest)
            })
            return farmProdObj[farmProdObj.length - 1].on_hand_latestPrice
        }
    },
    computed: {
        ...mapGetters(['getProduceReport']),
        filteredTable(){
            var table = []
            if(this.getProduceReport.produce_traders){
                if(this.filter_produce != "None"){
                    table = this.getProduceReport.produce_traders.filter((p) => {
                        return parseInt(this.filter_produce) === parseInt(p.produce_id)
                    })
                }
                else{
                    return this.getProduceReport.produce_traders
                }
            }
            return table
        }
    }
}
</script>


<style>

</style>
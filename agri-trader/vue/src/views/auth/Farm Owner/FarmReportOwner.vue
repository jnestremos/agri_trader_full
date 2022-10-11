<template>
  <div class="farmReportOwner">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Farms</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">            
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">Select Produce</label>
                <select name="" class="form-select" id="" @change="setProduce($event)">
                    <option selected value="None">None</option>
                    <option v-for="(produce, index) in getFarmReportForOwner.produces" :key="index" :value="produce.id">{{ produce.prod_name + ' ' + produce.prod_type }}</option>
                </select>
            </div>            
          </div>   
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Farm Name</th>                          
                          <th scope="col">Number of Produces</th>                          
                          <th scope="col">Last Date of Harvest</th>                                                   
                          <th scope="col">Date Created</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr v-for="(farm, index) in filteredTable" :key="index">
                        <td>{{ farm.farm_name }}</td>
                        <td>{{ getNumberOfProduces(farm) }}</td>
                        <td>{{ getLastDateHarvest(farm) }}</td>                                                             
                        <td>{{ farm.created_at.split('T')[0] }}</td>
                    </tr>
                  </tbody>
              </table>
            </div>
        </div>
      </div>    
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name:"FarmReportOwner",
    created(){
        this.fetchFarmReportForOwner()
        .then(() => {
            this.readyApp()
        })
    },
    data(){
        return {            
            filter_produce: "None",
        }
    },   
    methods: {
        ...mapActions(['readyApp', 'fetchFarmReportForOwner']),
        setProduce(e){
            this.filter_produce = e.target.value
        },
        getNumberOfProduces(farm){
            var farmProdObj = this.getFarmReportForOwner.farm_produces.filter((p) => {
                return parseInt(farm.id) === parseInt(p.farm_id)
            })
            return farmProdObj.length
        },
        getLastDateHarvest(farm){
            var farmProdObj = this.getFarmReportForOwner.farm_produces.filter((p) => {
                return parseInt(farm.id) === parseInt(p.farm_id)
            })
            farmProdObj = farmProdObj.sort((a, b) => {
                return new Date(a.prod_lastDateOfHarvest) - new Date(b.prod_lastDateOfHarvest)
            })
            return farmProdObj[farmProdObj.length - 1].prod_lastDateOfHarvest
        },              
    },
    computed: {
        ...mapGetters(['getFarmReportForOwner']),        
        filteredTable(){
            var table = [];
            if(this.filter_produce != 'None'){
                var farmProdObj = this.getFarmReportForOwner.farm_produces.filter((p) => {
                    return parseInt(this.filter_produce) === parseInt(p.produce_id)
                })
                farmProdObj.forEach((f) => {
                    var farmObj = this.getFarmReportForOwner.farms.filter((ff) => {
                        return parseInt(f.farm_id) === parseInt(ff.id)
                    })
                    if(!table.includes(farmObj[0])){
                        table.push(farmObj[0])
                    }
                })
                return table
            }
            else{
                return this.getFarmReportForOwner.farms
            }
        }
    }
}
</script>

<style>

</style>
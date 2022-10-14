<template>
  <div class="produceReportOwner">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Produces</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">            
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">Select Produce</label>
                <select name="" class="form-select" id="" @change="setProduce($event)">
                    <option selected value="None">None</option>
                    <option v-for="(produce, index) in getProduceReportForOwner.produce_list" :key="index" :value="produce.id">{{ produce.prod_name + ' ' + produce.prod_type }}</option>
                </select>
            </div>            
          </div>   
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Produce Name</th>                          
                          <th scope="col">Time to Harvest</th>                          
                          <th scope="col">Last Date of Harvest</th>                                                   
                          <th scope="col">Farms Associated</th>                                                   
                          <th scope="col">Qty In-Stock</th>                                                   
                          <th scope="col">Date Created</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr style="cursor:pointer" v-for="(produce, index) in filteredTable" :key="index" @click="$router.push({ path: `/produce/owner/details/${produce.id}` })">
                        <td>{{ getProduceName(produce) }}</td>
                        <td>{{ produce.prod_timeOfHarvest }}</td>
                        <td>{{ getLastDateHarvest(produce) }}</td>                                                             
                        <td>{{ getFarmsAssociated(produce) }}</td>                                                             
                        <td>{{ getQty(produce) }}</td>                                                             
                        <td>{{ produce.created_at.split('T')[0] }}</td>
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
    name: 'ProduceReportOwner',
    created(){
        this.fetchProduceReportForOwner()
        .then(() => {
            this.readyApp()
        })        
    },
    data(){
        return {
            filter_produce: 'None'
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProduceReportForOwner']),
        setProduce(e){
            this.filter_produce = e.target.value
        },
        getProduceName(produce){
            var prodObj = this.getProduceReportForOwner.produce_list.filter((p) => {
                return parseInt(produce.produce_id) === parseInt(p.id)
            }) 
            var arr = produce.prod_name.split('(Class') 
            if(arr.indexOf('(Class') != -1){
                arr.splice(arr.indexOf('(Class'), 0, prodObj[0].prod_type)
                return arr.join(' ')
            }
            else{
                return produce.prod_name + ' ' + prodObj[0].prod_type 
            }                       
        },
        getLastDateHarvest(produce){
            var farmProdObj = this.getProduceReportForOwner.farm_produces.filter((p) => {
                return parseInt(produce.id) === parseInt(p.produce_trader_id)
            })
            farmProdObj = farmProdObj.sort((a, b) => {
                return new Date(a.prod_lastDateOfHarvest) - new Date(b.prod_lastDateOfHarvest)
            })   
            farmProdObj = farmProdObj.reverse()     
            return farmProdObj[0].prod_lastDateOfHarvest
        },
        getFarmsAssociated(produce){
            var farmProdObj = this.getProduceReportForOwner.farm_produces.filter((p) => {
                return parseInt(produce.id) === parseInt(p.produce_trader_id)
            })
            return farmProdObj.length
        },
        getQty(produce){
            var farmProdObj = this.getProduceReportForOwner.farm_produces.filter((p) => {
                return parseInt(produce.id) === parseInt(p.produce_trader_id)
            })
            var total = farmProdObj.reduce((a, b) => a.on_hand_qty + b.on_hand_qty, 0)
            return total
        }
    },
    computed: {
        ...mapGetters(['getProduceReportForOwner']),
        filteredTable(){
            var table = []
            if(this.getProduceReportForOwner.produces){
                if(this.filter_produce != "None"){
                    table = this.getProduceReportForOwner.produces.filter((p) => {
                        return parseInt(this.filter_produce) === parseInt(p.produce_id)
                    })
                    return table
                }
                else{
                    return this.getProduceReportForOwner.produces
                }
            }
            return table
        }
    }
}
</script>

<style>

</style>
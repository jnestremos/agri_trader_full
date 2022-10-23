<template>
  <div class="farmReport">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Farms</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
            <div class="form-row mt-3">
                <div class="col-lg-2 me-3">
                    <label for="stockInHitsory_supplierList" class="form-label me-4">Select Farm</label>
                    <select class="form-select" id="supplier_name" :disabled="filter_owner != 'None'" @change="setFarm($event)">
                        <option selected value="None">None</option>
                        <option v-for="(farm, index) in getFarmReport.farms" :key="index" :value="farm.id">{{ farm.farm_name }}</option>                        
                    </select>                    
                </div>                  
                <div class="col-lg-2 me-3">
                    <label for="stockInHitsory_supplierList" class="form-label me-4">Select Farm Owner</label>
                    <select class="form-select" id="supplier_name" :disabled="filter_farm != 'None'" @change="setOwner($event)">
                        <option selected value="None">None</option>
                        <option v-for="(owner, index) in getFarmReport.farm_owners" :key="index" :value="owner.id">{{ owner.owner_firstName + ' ' + owner.owner_lastName }}</option>                        
                    </select>                    
                </div>                                 
            </div>
            <div class="mb-2 mt-4" style="width:100%; height:90%; clear:left;">
                <table id="supplySelect" class="table table-striped table-bordered align-middle" :style="[filteredTable && filteredTable.length > 8 ? {'overflow-y':'scroll'} : {}, {'width': '100%'}]">
                    <thead align="center">
                        <tr>                              
                            <th scope="col">Farm Name</th>                                                                               
                            <th scope="col">Farm Owner</th>
                            <th scope="col">Number of Produces</th>                             
                            <th scope="col">Last Date of Harvest</th>                             
                            <th scope="col">Date Created</th>                                                         
                        </tr>
                    </thead>
                    <tbody align="center">
                        <tr style="cursor:pointer;" v-for="(farm, index) in filteredTable" :key="index" @click="$router.push({ path: `/farm/${farm.id}` })">                   
                            <td>{{ farm.farm_name }}</td>                                                                         
                            <td>{{ getOwnerName(farm) }}</td>
                            <td>{{ getNumOfProduces(farm) }}</td>                                            
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
    name: "FarmReport",
    created(){
        this.fetchFarmReport()
        .then(() => {
            this.readyApp()
        })        
    },
    data(){
        return {
            filter_farm: 'None',
            filter_owner: 'None',
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchFarmReport']),
        setFarm(e){
            this.filter_farm = e.target.value
        },
        setOwner(e){
            this.filter_owner = e.target.value
        },
        getOwnerName(farm){
            var ownerObj = this.getFarmReport.farm_owners.filter((o) => {
                return parseInt(farm.farm_owner_id) === parseInt(o.id)
            })
            return ownerObj[0].owner_firstName + ' ' + ownerObj[0].owner_lastName
        },
        getNumOfProduces(farm){
            var farmProdObj = this.getFarmReport.farm_produces.filter((p) => {
                return parseInt(farm.id) === parseInt(p.farm_id)
            })
            return farmProdObj.length
        },
        getLastDateHarvest(farm){
            var farmProdObj = this.getFarmReport.farm_produces.filter((p) => {
                return parseInt(farm.id) === parseInt(p.farm_id)
            })
            farmProdObj = farmProdObj.sort((a, b) => {
                return new Date(a.prod_lastDateOfHarvest) - new Date(b.prod_lastDateOfHarvest)
            })
            return farmProdObj[farmProdObj.length - 1] ? farmProdObj[farmProdObj.length - 1].prod_lastDateOfHarvest
            : 'No Harvest Data'
        },
    },
    computed: {
        ...mapGetters(['getFarmReport']),
        filteredTable(){
            var table = []
            if(this.filter_farm != 'None'){
                table = this.getFarmReport.farms.filter((f) => {
                    return parseInt(f.id) === parseInt(this.filter_farm)
                })
            }
            else if(this.filter_owner != 'None'){
                table = this.getFarmReport.farms.filter((f) => {
                    return parseInt(f.farm_owner_id) === parseInt(this.filter_owner)
                })
            }          
            else {
                return this.getFarmReport.farms
            }
            return table
        }
    }
}
</script>

<style>

</style>
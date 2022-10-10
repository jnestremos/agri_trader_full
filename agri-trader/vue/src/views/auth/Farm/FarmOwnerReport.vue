<template>
  <div class="farmOwnerReport">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Farm Owners</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
            <div class="form-row mt-3">
                <div class="col-lg-2 me-3">
                    <label for="stockInHitsory_supplierList" class="form-label me-4">Select Farm Owner</label>
                    <select class="form-select" id="supplier_name" @change="setOwner($event)">
                        <option selected value="None">None</option>
                        <option v-for="(owner, index) in getFarmOwnerReport.farm_owners" :key="index" :value="owner.id">{{ owner.owner_firstName + ' ' + owner.owner_lastName }}</option>                        
                    </select>                    
                </div>                                                                 
            </div>
            <div class="mb-2 mt-4" style="width:100%; height:90%; clear:left;">
                <table id="supplySelect" class="table table-striped table-bordered align-middle" :style="[filteredTable && filteredTable.length > 8 ? {'overflow-y':'scroll'} : {}, {'width': '100%'}]">
                    <thead align="center">
                        <tr>                              
                            <th scope="col">Farm Owner</th>                                                                               
                            <th scope="col">Gender</th>                                                                               
                            <th scope="col">Birthdate</th>                                                                               
                            <th scope="col">Email</th>                                                                               
                            <th scope="col">Address</th>
                            <th scope="col">Contact Number</th>                                                         
                            <th scope="col">Number Of Farms</th>                                                         
                            <th scope="col">Account Created</th>                                                         
                        </tr>
                    </thead>
                    <tbody align="center">
                        <tr style="cursor:pointer;" v-for="(owner, index) in filteredTable" :key="index">                   
                            <td>{{ owner.owner_firstName + ' ' + owner.owner_lastName }}</td>                                                                         
                            <td>{{ owner.owner_gender }}</td>
                            <td>{{ owner.owner_birthDate }}</td>                                            
                            <td>{{ owner.owner_email }}</td>
                            <td>{{ getOwnerAddress(owner) }}</td>
                            <td>{{ getOwnerContactNum(owner) }}</td>
                            <td>{{ getNumOfFarms(owner) }}</td>
                            <td>{{ owner.created_at.split('T')[0] }}</td>                            
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
    name: 'FarmOwnerReport',
    created(){
        this.fetchFarmOwnerReport()
        .then(() => {
            this.readyApp()
        })        
    },
    data(){
        return {
            filter_owner: 'None'
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchFarmOwnerReport']),
        setOwner(e){
            this.filter_owner = e.target.value
        },
        getOwnerAddress(owner){
            var addressObj = this.getFarmOwnerReport.farm_owner_addresses.filter((a) => {
                return parseInt(owner.id) === parseInt(a.farm_owner_id)
            })
            return addressObj[0].owner_address + ' ' + addressObj[0].owner_province 
            + ' ' + addressObj[0].owner_zipcode
        },
        getOwnerContactNum(owner){
            var contactObj = this.getFarmOwnerReport.farm_owner_contact_numbers.filter((n) => {
                return parseInt(owner.id) === parseInt(n.farm_owner_id)
            })
            return contactObj[0].owner_contactNum
        },
        getNumOfFarms(owner){
            var farmObj = this.getFarmOwnerReport.farms.filter((f) => {
                return parseInt(owner.id) === parseInt(f.farm_owner_id)
            })
            return farmObj.length
        }
    },
    computed: {
        ...mapGetters(['getFarmOwnerReport']),
        filteredTable(){
            var table = [];
            if(this.filter_owner != 'None'){
                table = this.getFarmOwnerReport.farm_owners.filter((o) => {
                    return parseInt(this.filter_owner) === parseInt(o.id)
                })
            }
            else{
                return this.getFarmOwnerReport.farm_owners
            }
            return table
        }
    }
}
</script>

<style>

</style>
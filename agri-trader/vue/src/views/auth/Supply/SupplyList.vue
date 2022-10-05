<template>
  <div class="SupplyList">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply List</h3>        
    </div>
    <div class="container-fluid" style="height:85%; position: relative; z-index: 9;">
        <div class="form-row mb-3 mt-2">
            <div class="col-lg-2 me-3">
                <label class="form-label me-4 fw-bold">Supplier</label>
                <select class="form-select" @change="setSupplier($event)">
                    <option value="None">Select Supplier</option>
                    <option v-for="(supplier, index) in getSupplyList.suppliers" :key="index" :value="supplier.id">{{ supplier.supplier_name }}</option>                    
                </select>
            </div>
            <div class="col-lg-2 me-3">
                <label class="form-label me-4 fw-bold">Supply Type</label>
                <select class="form-select" :disabled="filter_supplier == 'None'" id="supply_type" @change="setSupplyType($event)">
                    <option selected value="None">Select Supply Type</option>
                    <option v-for="(type, index) in getTypes" :key="index" :value="type">{{ type }}</option>                    
                </select>
            </div>
            <div class="col-lg-2 me-2">
                <label class="form-label me-4 fw-bold">Choose Supply For</label>
                <select class="form-select" :disabled="filter_supplier == 'None'" id="supply_for" @change="setSupplyFor($event)">
                    <option selected value="None">Select Supply For</option>
                    <option v-for="(produce, index) in getSupplyForFiltered" :key="index" :value="produce">{{ produce }}</option>                    
                </select>
            </div>
        </div>
        <!-- width:100%; height: 40vh; -->
        <div class="container-fluid m-0 p-0" :style="[filteredTable && filteredTable.length > 6 ? {'overflow-y': 'scroll'} : {}, {'width':'100%'}, {'height':'40vh'}]">
            <table id="supplySelect" class="table table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                <thead align="center">
                    <tr>
                        <th scope="col">Supplier</th>
                        <th scope="col">Supply Name</th>
                        <th scope="col">Supply Type</th>
                        <th scope="col">Supply For</th>
                        <th scope="col">Description</th>
                        <th scope="col">Reorder Level</th>
                        <th scope="col">Price</th>
                        <th scope="col">Unit</th>
                    </tr>
                </thead>
                <tbody align="center">
                    <tr v-for="(supply, index) in filteredTable" :key="index" @click="triggerModal(supply)">
                        <td>{{ getSupplierName(supply) }}</td>
                        <td>{{ supply.supply_name }}</td>
                        <td>{{ supply.supply_type }}</td>
                        <td>{{ supply.supply_for }}</td>
                        <td>{{ supply.supply_description }}</td>
                        <td>{{ supply.supply_reorderLevel }}</td>
                        <td>{{ supply.supply_initialPrice.toFixed(2) }}</td>
                        <td>{{ supply.supply_unit }}</td>
                        <b-modal size="lg" :id="`modal-${supply.id}`" :title="supply.supply_name">
                            <div class="d-flex p-3 w-100 h-100">
                                <div class="d-flex align-items-baseline w-50">
                                    <label for="" class="form-label me-3">Retail Price: </label>
                                    <input type="number" name="" class="form-control" v-model="supply_initialPrice" @change="validatePrice($event)" @blur="validatePrice($event)" style="width:150px" id="">
                                </div>                                
                                <div class="d-flex align-items-baseline w-50">
                                    <label for="" class="form-label me-3">Reorder Level: </label>
                                    <input type="number" name="" class="form-control" v-model="supply_reorderLevel" @change="validateLevel($event)" @blur="validateLevel($event)" style="width:150px" id="">
                                </div>                                
                            </div>
                        </b-modal>
                    </tr>                                                                    
                </tbody>
            </table>
        </div>
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group me-3">
                <router-link to="/supply/add"> <button class="btn btn-success" style="width:200px; height: 50px;"> Add Supply Profile </button> </router-link>
            </div> 
        </div>     
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: "SupplyList",
    created() {
        this.fetchSupplyList()
        .then(() => {
            this.readyApp()
        })
        
    },
    data(){
        return {
            filter_supplier: 'None',
            filter_type: 'None',
            filter_for: 'None',
            supply_id: null,
            supply_initialPrice: null,
            supply_reorderLevel: null
        }
    },
    watch: {
        'filter_supplier'(newVal){
            if(newVal == "None"){
                var supply_type = document.getElementById('supply_type')
                var supply_for = document.getElementById('supply_for')
                this.filter_type = 'None'
                this.filter_for = 'None'
                supply_type.value = "None"
                supply_for.value = "None"
            }
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchSupplyList', 'updateSupply']),
        getSupplierName(supply){
            var supplierObj = this.getSupplyList.suppliers.filter((s) => {
                return parseInt(supply.supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        },
        triggerModal(supply){
            this.$bvModal.show(`modal-${supply.id}`)
            this.supply_id = supply.supply_id
            this.supply_initialPrice = supply.supply_initialPrice
            this.supply_reorderLevel = supply.supply_reorderLevel
        },
        validatePrice(e){
            this.supply_initialPrice = Math.abs(e.target.value)
        },
        validateLevel(e){
            this.supply_reorderLevel = Math.abs(e.target.value)
        },
        setSupplier(e){
            this.filter_supplier = e.target.value
        },
        setSupplyType(e){
            this.filter_type = e.target.value
        },
        setSupplyFor(e){
            this.filter_for = e.target.value
        },
    },
    mounted(){
        this.$root.$on('bv::modal::hide', (bvEvent) => {
            if(bvEvent.trigger == 'headerclose' || bvEvent.trigger == 'cancel'){
                this.supply_id = null
                this.supply_initialPrice = null
                this.supply_reorderLevel = null
            }
            else if(bvEvent.trigger == 'ok'){
                var data = {
                    id: this.supply_id,
                    supply_initialPrice: this.supply_initialPrice,
                    supply_reorderLevel: this.supply_reorderLevel,
                }
                this.updateSupply(data)
                .then(() => {
                    this.$toastr.s('Supply successfully updated!')
                    setTimeout(() => {
                        location.reload()
                    }, 5000)
                })
            }
        })
    },
    computed: {
        ...mapGetters(['getSupplyList']),
        filteredTable(){
            var table = []
            if(this.filter_supplier != 'None'){
                table = this.getSupplyList.supplies.filter((s) => {
                    return parseInt(this.filter_supplier) === parseInt(s.supplier_id)
                })
                if(this.filter_type != 'None' && this.filter_for != 'None'){
                    table = table.filter((s) => {
                        return this.filter_type == s.supply_type && this.filter_for === s.supply_for
                    })
                }
                else if(this.filter_type != 'None'){
                    table = table.filter((s) => {
                        return this.filter_type == s.supply_type
                    })
                }
                else if(this.filter_for != 'None'){
                    table = table.filter((s) => {
                        return this.filter_for == s.supply_for
                    })
                }
                return table
            }
            else{
                return this.getSupplyList.supplies
            }
        },
        getTypes(){
            var types = new Set();
            var arr = []
            if(this.getSupplyList.supplies){
                this.getSupplyList.supplies.forEach((s) => {
                    types.add(s.supply_type)
                })
                types.forEach((t) => {
                    arr.push(t)
                })
            }
            return arr
        },
        getSupplyForFiltered(){
            var types = new Set();
            var arr = []
            if(this.getSupplyList.supplies){
                this.getSupplyList.supplies.forEach((s) => {
                    types.add(s.supply_for)
                })
                types.forEach((t) => {
                    arr.push(t)
                })
            }
            return arr
        },
    },
}
</script>

<style scoped>
tr{
    background:transparent
}
tr:hover{
    background:lightgrey;
    transition: 0.5s ease-in;
    cursor: pointer;
}
</style>
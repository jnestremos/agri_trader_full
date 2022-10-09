<template>
  <div class="SupplyInventory">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Supply Inventory</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
            <div class="form-row mt-3">
                <div class="col-lg-3 me-3">
                    <label for="stockInHitsory_supplierList" class="form-label me-4">Choose Supplier</label>
                    <select class="form-select" id="supplier_name">
                        <option selected value="None">Select Supplier</option>
                        <option v-for="(supplier, index) in getSupplyInventory.suppliers" :key="index" :value="supplier.id">{{ supplier.supplier_name }}</option>                        
                    </select>                    
                </div>
                <div class="col-lg-3 me-3">
                    <label for="stockInHistory_supplyType" class="form-label me-4" >Choose Supply Type</label>
                    <select class="form-select" id="supply_type">
                        <option selected value="None">Select Supply Type</option>
                        <option v-for="(type, index) in getTypes" :key="index" :value="type">{{ type }}</option>
                    </select>
                </div>
                <div class="col-lg-3 me-3">
                    <label for="stockInHistory_SupplyFor" class="form-label me-4">Choose Supply For</label>
                    <select class="form-select" id="supply_type">
                        <option selected value="None">Select Supply For</option>  
                        <option v-for="(produce, index) in getSupplyForFiltered" :key="index" :value="produce">{{ produce }}</option>                                      
                    </select>
                </div>
            </div>
            <div class="mb-2 mt-4" style="width:100%; height:90%; clear:left;">
                <table id="supplySelect" class="table table-striped table-bordered align-middle" :style="[filteredInventory && filteredInventory.length > 8 ? {'overflow-y':'scroll'} : {}, {'width': '100%'}]">
                    <thead align="center">
                        <tr>
                            <th scope="col">Supplier Name</th>
                            <th scope="col">Supply Name</th>
                            <th scope="col">Supply Type</th>
                            <th scope="col">Supply For</th>                                    
                            <th scope="col">Reorder Level</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Unit</th>
                            <th scope="col">Date Updated</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <tr v-for="(supply, index) in filteredInventory" :key="index">
                            <td>{{ getSupplierName(supply) }}</td>
                            <td>{{ supply.supply_name }}</td>
                            <td>{{ supply.supply_type }}</td>
                            <td>{{ supply.supply_for }}</td>
                            <td>{{ supply.supply_reorderLevel }}</td>
                            <td>{{ supply.supply_qty }}</td>                            
                            <td>{{ supply.supply_unit }}</td>
                            <td>{{ supply.updated_at.split('T')[0] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="btn-toolbar pt-4" role="toolbar">
                <div class="btn-group me-3">
                <router-link to="/supplyInventory/stockInHistory"> <b-button variant="info" style="width:200px; height:60px" class="me-3">Stock In History</b-button> </router-link>
                <router-link to="/supplyInventory/stockOutHistory"> <b-button variant="info" style="width:200px; height:60px">Stock Out History</b-button> </router-link>
            </div>
            <div class="btn-group me-3">
                <router-link to="/supply/list"> <button class="btn btn-success" style="width:220px; height: 60px;"> See list of supply profiles </button> </router-link>
            </div>
        </div>
        </div>
    </div>
  </div>
  
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "SupplyInventory",
    created() {
        this.fetchSupplyInventory()
        .then(() => {
            this.readyApp()
        })        
    },
    data(){
        return{
           filter_supplier: null,
           filter_type: null,
           filter_for: null
        }
    },    
    methods: {
        ...mapActions(['readyApp', 'fetchSupplyInventory']),
        getSupplierName(supply){
            var supplierObj = this.getSupplyInventory.suppliers.filter((s) => {
                return parseInt(supply.supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        }
    },
    computed: {
        ...mapGetters(['getSupplyInventory']),
        filteredInventory(){
            var inventory = []            
            if(this.filter_supplier){
                inventory = this.getSupplyInventory.supplies.filter((s) => {
                    return parseInt(this.filter_supplier) === parseInt(s.id)
                })
                if(this.filter_type && this.filter_for){
                    inventory = inventory.filter((ss) => {
                        return this.filter_type === ss.supply_type && this.filter_for === ss.supply_for
                    })
                }
                else if(this.filter_type){
                    inventory = inventory.filter((ss) => {
                        return this.filter_type === ss.supply_type
                    })
                }
                else if(this.filter_for){
                    inventory = inventory.filter((ss) => {
                        return this.filter_for === ss.supply_for
                    })
                }
                return inventory
            }
            else{
                return this.getSupplyInventory.inventory
            }
        },
        getTypes(){
            var types = new Set()
            var arr = [];
            if(this.getSupplyInventory.supplies){
                this.getSupplyInventory.supplies.forEach((s) => {
                    types.add(s.supply_type)
                })                
                types.forEach((ss) => {
                    arr.push(ss)
                })                
            }
            return arr
        },
        getSupplyForFiltered(){
            var types = new Set()
            var arr = [];
            if(this.getSupplyInventory.supplies){
                this.getSupplyInventory.supplies.forEach((s) => {
                    types.add(s.supply_for)
                })                
                types.forEach((ss) => {
                    arr.push(ss)
                })                
            }
            return arr
        }
    }
  }
</script>

<style>

</style>
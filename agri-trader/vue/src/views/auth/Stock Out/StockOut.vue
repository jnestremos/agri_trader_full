<template>
    <div class="Stock Out">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
              <h3>Supply Inventory | Stock Out</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
          <div style="width:50%; height:100%" class="pb-5">
              <form class="d-flex flex-column justify-content-between mt-2 ms-2 w-100 h-100">
                  <div class="form-row mb-2">
                      <div class="col-lg-3 me-3">
                          <label for="stockOut_date" class="form-label me-4" >Date</label>
                          <input type="date" name="stockIn_Date" id="" class="form-control" v-model="dateToday" disabled placeholder="09/05/2022">
                      </div>
                      <div class="col-lg-3 me-3">
                          <label for="stockIn_purchaseOrderNum" class="form-label me-4" >Project ID:</label>
                          <input type="text" name="stockIn_Date" id="" class="form-control" placeholder="022654" :value="$route.params.id" disabled>
                      </div>
                  </div>
                  <div class="form-row mt-3">
                        <div class="col-lg-3 me-3">
                            <label for="stockInHitsory_supplierList" class="form-label me-4">Choose Supplier</label>
                            <select class="form-select" :disabled="!(getStockOut.inventory && getStockOut.inventory.length > 0)" id="supplier_name" @change="setSupplier($event)">
                                <option selected value="None">Select Supplier</option>
                                <option v-for="(supplier, index) in getStockOut.suppliers" :key="index" :value="supplier.id">{{ supplier.supplier_name }}</option>
                            </select>                    
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="stockInHistory_supplyType" class="form-label me-4" >Choose Supply Type</label>
                            <select class="form-select" id="supply_type" :disabled="filter_supplier == 'None'" @change="setSupplyType($event)">
                                <option selected value="None">Select Supply Type</option>
                                <option v-for="(type, index) in getTypes" :key="index" :value="type">{{ type }}</option>
                            </select>
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="stockInHistory_SupplyFor" class="form-label me-4">Choose Supply For</label>
                            <select class="form-select" id="supply_for" :disabled="filter_supplier == 'None'" @change="setSupplyFor($event)">
                                <option selected value="None">Select Supply Type</option> 
                                <option v-for="(produce, index) in getSupplyForFiltered" :key="index" :value="produce">{{ produce }}</option>             
                            </select>
                        </div>
                    </div>
                  <div class="mb-2 mt-4" :style="[filteredTable && filteredTable.length > 8 ? {'overflow-y': 'scroll'} : {}, {'width':'100%'}, {'height':'100%'}]">
                      <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:100%;">
                          <thead align="center">
                              <tr>
                                  <th scope="col">Select</th>
                                  <th scope="col">Supplier Name</th>
                                  <th scope="col">Supply Name</th>
                                  <th scope="col">Supply Type</th>
                                  <th scope="col">Supply For</th>                                    
                                  <th scope="col">Available Quantity</th>
                                  <th scope="col">Unit</th>
                              </tr>
                          </thead>
                          <tbody align="center">
                              <tr v-for="(item, index) in filteredTable" :key="index">
                                  <td><input :checked="checkItem(item.supply_id)" @change="addItem($event)" :value="item.supply_id" type="checkbox"></td>
                                  <td>{{ getSupplierName(item) }}</td>
                                  <td>{{ item.supply_name }}</td>
                                  <td>{{ item.supply_type }}</td>
                                  <td>{{ item.supply_for }}</td>
                                  <td>{{ item.supply_qty }}</td>
                                  <td>{{ item.supply_unit }}</td>
                              </tr>                              
                          </tbody>
                      </table>
                  </div>
                  <!-- <div class="form-row mt-2 mb-2">
                      <div class="col-sm-2">
                          <label for="orderSummary_quantityReceived" class="form-label me-4" >Amount</label>
                          <input type="text" name="orderSummary_totalAmount" id="" class="form-control" style="width: 40%">
                      </div>
                  </div> -->
                  <div class="form-row">
                      <div class="col-lg-3 me-3">
                          <label for="stockOut_transactedBy" class="form-label me-4" >Transacted By</label>
                          <input type="text" name="stockIn_transactedBy" id="" class="form-control" disabled :value="getName()">
                      </div>
                      <div class="col-lg-3 me-3">
                          <label for="stockOut_Remarks" class="form-label me-4">Remarks</label>
                          <input type="text" name="stockIn_transactedBy" id="" v-model="stockOut_remark" class="form-control">
                      </div>
                  </div>
                  <div class="btn-toolbar pt-4" role="toolbar">
                      <div class="btn-group me-3">
                        <b-button variant="success" class="fw-bold" @click="sendStock()" style="width:200px; height:60px" :disabled="!(filteredTable && filteredTable.length > 0)">Stock Out To Project</b-button>
                      </div>
                      <div class="btn-group me-3">
                        <b-button variant="secondary" v-b-modal.modal-1 :disabled="!(getStockOut.stockOut && getStockOut.stockOut.length > 0)" class="fw-bold" style="width:200px; height:60px">View All Items Used for Project</b-button>
                        <b-modal size="xl" hide-footer scrollable id="modal-1" :title="`All Supplies used for Project #${$route.params.id}`">
                            <table id="modal-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Supplier Name</th>
                                        <th>Supply Name</th>
                                        <th>Supply Type</th>
                                        <th>Supply For</th>
                                        <th>Quantity Used</th>
                                        <th>Unit</th>
                                        <th>Date Used</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in getStockOut.stockOut" :key="index">
                                        <td>{{ getSupplierName(item) }}</td>
                                        <td>{{ getSupplyName(item) }}</td>
                                        <td>{{ getSupplyType(item) }}</td>
                                        <td>{{ getSupplyFor(item) }}</td>
                                        <td>{{ item.supply_qty }}</td>
                                        <td>{{ item.supply_unit }}</td>
                                        <td>{{ item.created_at.split('T')[0] }}</td>
                                    </tr>   
                                </tbody>
                            </table>
                        </b-modal>
                      </div>
                  </div>
              </form>
          </div>
          <!-- width:90%; margin:0 auto; height:100%; overflow-y: scroll; -->
          <div style="width:50%; height:65%" class="pb-5">
            <h4 align="center" class="mt-3">Supplies Added for Project No. {{ $route.params.id }}</h4>
            <div :style="[getAddedItemsTable && getAddedItemsTable.length > 9 ? {'overflow-y': 'scroll'} : {}, {'width':'90%'}, {'margin':'0 auto'}, {'height': '100%'}]">                
                <table id="supplySelect" class="table table-striped table-success table-bordered align-middle" style="width:100%;">
                    <thead align="center">
                        <tr>
                            <th scope="col">Supplier Name</th>
                            <th scope="col">Supply Name</th>
                            <th scope="col">Supply Type</th>
                            <th scope="col">Supply For</th>                                    
                            <th scope="col">Quantity</th>
                            <th scope="col">Unit</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <tr v-for="(item, index) in getAddedItemsTable" :key="index">
                            <td>{{ getSupplierName(item) }}</td>
                            <td>{{ item.supply_name }}</td>
                            <td>{{ item.supply_type }}</td>
                            <td>{{ item.supply_for }}</td>
                            <td>{{ item.supply_qty }}</td>
                            <td>{{ item.supply_unit }}</td>
                        </tr>                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
  </template>
  
<script>
import { format } from 'date-fns/'
import { mapActions, mapGetters } from 'vuex'
import auth from '../../../store/modules/Auth/auth'
export default {
    name: "StockOut",
    created() {
        this.fetchStockOut(this.$route.params.id)
        .then(() => {
            var arrr = []
            var arr = []
            this.getStockOut.inventory.forEach((s) => {
                arr.push(false)
                arrr.push(s.supply_id)
            })
            this.addedItems_check = arr
            this.addedItems_idOrder = arrr
            this.readyApp()
        })
        .catch((err) => {
            console.error(err)
            this.$router.push({ path: `/projects/${this.$route.params.id}` })
        })        
    },
    data(){
        return{
            filter_supplier: 'None',
            filter_type: 'None',
            filter_for: 'None',
            dateToday: format(new Date(), 'yyyy-MM-dd'),
            addedItems_id: [],
            addedItems_qty: [],
            addedItems_check: null,
            addedItems_idOrder: null,
            stockOut_remark: null
        }
    },  
    watch: {
        'filter_supplier'(newVal){
            if(newVal == 'None'){
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
        ...mapActions(['readyApp', 'fetchStockOut', 'addStockOut']),
        addItem(e){
            var index = null;
            if(e.target.checked){
                var qty = prompt('Please type in the quantity needed for this supply')
                var itemObj = this.getStockOut.inventory.filter((i) => {
                    return parseInt(e.target.value) === parseInt(i.supply_id)
                })
                if(parseInt(qty)){
                    if(parseInt(qty) > parseInt(itemObj[0].supply_qty)){
                        alert("Error adding item!")
                        e.target.checked = false
                    }
                    else{
                        this.addedItems_id.push(e.target.value)
                        this.addedItems_qty.push(parseInt(qty))
                        index = this.addedItems_idOrder.indexOf(parseInt(e.target.value))
                        this.addedItems_check[index] = true
                    }
                }
                else{
                    e.target.checked = false
                }
            }
            else{
                index = this.addedItems_id.indexOf(e.target.value)
                this.addedItems_id.splice(index, 1)
                this.addedItems_qty.splice(index, 1)
                index = this.addedItems_idOrder.indexOf(parseInt(e.target.value))
                this.addedItems_check[index] = false
            }
        },
        checkItem(id){
            if(this.addedItems_idOrder){
                var index = this.addedItems_idOrder.indexOf(id)
                return this.addedItems_check[index]
            }            
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
        getName(){
            return auth.state.user.name
        },
        getSupplierName(item){
            var supplierObj = this.getStockOut.suppliers.filter((s) => {
                return parseInt(item.supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        },
        getSupplyName(item){
            var supplyObj = this.getStockOut.supplies.filter((s) => {
                return parseInt(item.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_name
        },
        getSupplyType(item){
            var supplyObj = this.getStockOut.supplies.filter((s) => {
                return parseInt(item.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_type
        },
        getSupplyFor(item){
            var supplyObj = this.getStockOut.supplies.filter((s) => {
                return parseInt(item.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_for
        },
        sendStock(){
            var data = {
                id: this.$route.params.id,
                supply_id: this.addedItems_id,
                supply_qty: this.addedItems_qty,
                stockOut_remark: this.stockOut_remark,
            }
            this.addStockOut(data)
            .then(() => {
                this.$toastr.s('Supply added to Project Successfully')
                setTimeout(() => {
                    location.reload()
                }, 5000)
            })
        }
    },
        computed:{
        ...mapGetters(['getStockOut']),
        filteredTable(){
            var table = [];
            if(this.filter_supplier != 'None'){
                table = this.getStockOut.inventory.filter((i) => {
                    return parseInt(this.filter_supplier) === parseInt(i.supplier_id)
                })
                if(this.filter_type != 'None' && this.filter_for != 'None'){
                    table = table.filter((r) => {
                        return this.filter_type === r.supply_type && this.filter_for === r.supply_for
                    })
                }
                else if(this.filter_type != 'None'){
                    table = table.filter((r) => {
                        return this.filter_type === r.supply_type
                    })
                }
                else if(this.filter_for != 'None'){
                    table = table.filter((r) => {
                        return this.filter_for === r.supply_for
                    })
                }
                return table
            }
            else{
                return this.getStockOut.inventory
            }
        },
        getTypes(){
            var types = new Set()
            var arr = [];
            if(this.getStockOut.supplies){
                this.getStockOut.supplies.forEach((s) => {
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
            if(this.getStockOut.supplies){
                this.getStockOut.supplies.forEach((s) => {
                    types.add(s.supply_for)
                })                
                types.forEach((ss) => {
                    arr.push(ss)
                })                
            }
            return arr
        },
        getAddedItemsTable(){
            var table = []
            var data = null
            this.addedItems_id.forEach((id, index) => {
                var supplyObj = this.getStockOut.supplies.filter((s) => {
                    return parseInt(id) === parseInt(s.id)
                })
                var supplierObj = this.getStockOut.suppliers.filter((ss) => {
                    return parseInt(supplyObj[0].supplier_id) === parseInt(ss.id)
                })
                data = {
                    supplier_id: supplierObj[0].id,
                    supply_name: supplyObj[0].supply_name,
                    supply_type: supplyObj[0].supply_type,
                    supply_for: supplyObj[0].supply_for,
                    supply_qty: this.addedItems_qty[index],
                    supply_unit: supplyObj[0].supply_unit,
                }
                table.push(data)
            })
            return table
        }
    }
}
</script>

<style scoped>
#modal-table, #modal-table th, #modal-table td{
    border-collapse: collapse;
    border-spacing: 0;
    border: 2px solid black
}
#modal-table th, #modal-table td{
    padding: 10px;
    text-align: center
}

</style>
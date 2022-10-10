<template>
    <div class="InitialPurchaseOrder">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Create Supply Purchase Order</h3>        
        </div>     
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index: 9;">
            <div style="width:100%; height:100%;" class="pb-5">
                <form class="d-flex flex-column justify-content-between w-100 h-100" @submit.prevent="">
                    <div class="form-row mb-2">
                        <div class="col-lg-2 me-2">
                            <label for="supplyOrder_date" class="form-label me-4" >Date</label>
                            <input type="date" name="supplyOrder_Date" id="" class="form-control" disabled v-model="dateToday">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="supplyOrder_purchaseOrderNum" class="form-label me-4" >Purchase Order No.:</label>
                            <input type="text" name="supplyOrder_purchaseOrderNum" id="" class="form-control" v-model="data.purchaseOrder_num" disabled placeholder="PO-123456">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="supplyOrder_purchaseOrderStatus" class="form-label me-4" >Purchase Order Status</label>
                            <input type="text" name="supplyOrder_purchaseOrderStatus" disabled v-model="data.purchaseOrder_status" id="" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-2 mb-2 me-3">
                            <label for="supplyOrder_Supplier" class="form-label me-4">Supplier</label>
                            <select class="form-select" id="supplier" @change="setSupplier($event)">
                                <option value="None">Select Supplier</option>
                                <option v-for="(supplier, index) in getFormPO.suppliers" :key="index" :value="supplier.id">{{ supplier.supplier_name }}</option>                                                                
                            </select>
                        </div>
                        <div class="col-lg-2 mb-2 me-3">
                            <label for="supplyOrder_SupplyType" class="form-label me-4" >Choose Supply Type</label>
                            <select class="form-select" @change="setSupplyType($event)" id="supply_type">
                                <option selected value="None">Select Supply Type</option>
                                <option value="Fertilizer">Fertilizer</option>                                
                                <option value="Insecticide">Insecticide</option>                                
                                <option value="Fungicide">Fungicide</option>                                
                                <option value="Herbicide">Herbicide</option>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-2 me-3">
                            <label for="supplyOrder_SupplyType" class="form-label me-4">Choose Supply For</label>
                            <select v-if="getFormPO.produces" class="form-select" @change="setSupplyFor($event)" id="supply_for">
                                <option selected value="None">Select Supply For</option> 
                                <option v-for="(produce, index) in getFormPO.produces" :key="index" :value="produce.prod_type">{{ produce.prod_type }}</option>                                
                            </select>
                        </div>                        
                    </div>                    
                    <div class="d-flex m-0 p-0" style="width:100%; height:60%;">
                        <div class="me-3" style="width:60%;">
                            <div class="w-100 h-100" style="overflow-y:scroll">
                                <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                                    <thead align="center">
                                        <tr>
                                            <th scope="col">Select</th>
                                            <th scope="col">Supplier</th>
                                            <th scope="col">Supply Name</th>
                                            <th scope="col">Supply Type</th>
                                            <th scope="col">Supply For</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">
                                         <tr v-for="(supply, index) in filteredSupplies" :key="index">
                                            <td><input type="checkbox" name="" id="" :value="supply.id" :checked="checkID(supply)" @change="setSupplyId($event)">{{index}}</td>
                                            <td>{{ getSupplierName(supply) }}</td>
                                            <td>{{ supply.supply_name }}</td>
                                            <td>{{ supply.supply_type }}</td>
                                            <td>{{ supply.supply_for }}</td>
                                            <td>{{ supply.supply_description }}</td>
                                            <td>{{ supply.supply_initialPrice }}</td>
                                            <td>{{ supply.supply_unit }}</td>
                                        </tr>                                                                          
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div style="width:40%;">
                            <div class="w-100 h-100" style="overflow-y:scroll">
                                <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                                    <thead align="center">
                                        <tr>
                                            <th scope="col">Supplier Name</th>
                                            <th scope="col">Supply Name</th>
                                            <th scope="col">Supply Type</th>
                                            <th scope="col">Supply For</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Unit</th>
                                            <th scope="col">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">
                                         <tr v-for="(supply, index) in getCart" :key="index">                                            
                                            <td>{{ getSupplierName(supply) }}</td>
                                            <td>{{ supply.supply_name }}</td>
                                            <td>{{ supply.supply_type }}</td>
                                            <td>{{ supply.supply_for }}</td>
                                            <td>{{ supply.supply_description }}</td>
                                            <td>{{ supply.purchaseOrder_qty }}</td>
                                            <td>{{ supply.supply_initialPrice }}</td>
                                            <td>{{ supply.purchaseOrder_unit }}</td>
                                            <td>{{ supply.purchaseOrder_subTotal }}</td>
                                        </tr>                                                                          
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">                       
                        <div class="col-lg-2 mb-2 me-3">
                            <label for="supplyOrder_transactionPrice" class="form-label me-4">Transaction Price</label>
                            <input type="text" name="supplyOrder_transactionPrice" id="" disabled v-model="totalBalance" class="form-control">
                        </div>
                    </div>                    
                    <div class="text-left">
                        <button :disabled="validateData" @click="sendData()" class="btn btn-success" style="width:200px;">Create PO Summary</button>
                    </div>
                </form>
            </div>
        </div> 
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { format } from 'date-fns'
export default {
    name: "InitialPurchaseOrder",
    created() {
        this.formForAddPO()
        .then(() => {
            this.data.purchaseOrder_num = 'PO-'+this.getFormPO.uuid
            var checks = []
            this.getFormPO.supplies.forEach(() => {
                checks.push(false)
            })
            this.checks = checks
            this.readyApp()
        })
        .catch((err) => {
            console.error(err)
            this.$router.push({ name: 'AddSupply' })
        })    
    },
    data() {
        return {
            data: {                
                supply_id: [], 
                purchaseOrder_num: null, 
                purchaseOrder_status: 'Pending', 
                purchaseOrder_qty: [], 
                purchaseOrder_unit: [],
                purchaseOrder_subTotal: [],   
            },
            filter_supplier: 'None',
            filter_type: 'None',
            filter_for: 'None',
            totalBalance: 0,
            checks: null,
            dateToday: format(new Date(), 'yyyy-MM-dd'),
        }        
    },
    // watch: {
    //     'data.supplier_id'(newVal, oldVal){
    //         console.log(newVal)
    //         console.log(oldVal)
    //         if(oldVal != 'None'){
    //             if(confirm('Changing your selected supplier will result in clearing out your cart!\n Would you like to proceed!')){
    //                 this.filter_type = 'None'
    //                 this.filter_for = 'None'
    //                 var supply_type = document.getElementById('supply_type')
    //                 var supply_for = document.getElementById('supply_for')
    //                 supply_type.value = 'None'
    //                 supply_for.value = 'None' 
    //                 this.data.supply_id = [];
    //                 this.data.purchaseOrder_qty= [], 
    //                 this.data.purchaseOrder_unit= [],
    //                 this.data.purchaseOrder_subTotal= [],   
    //                 this.totalBalance = 0
    //                 this.data.supplier_id = newVal
    //             }
    //             else{
    //                 console.log(1)
    //                 console.log(oldVal)
    //                 var supplier = document.getElementById('supplier')
    //                 supplier.value = oldVal
    //                 this.data.supplier_id = oldVal
    //             }     
    //         }                                    
    //     },
    // },
    methods: {
        ...mapActions(['readyApp', 'formForAddPO', 'initPO']), 
        setSupplier(e){
            this.filter_supplier = e.target.value
        },
        setSupplyType(e){
            this.filter_type = e.target.value
        },
        setSupplyFor(e){
            this.filter_for = e.target.value
        },
        setSupplyId(e){
            var qty;
            var supplyObj;
            var index = this.data.supply_id.findIndex((i) => parseInt(i) === parseInt(e.target.value))
            var indexx = this.getFormPO.supplies.findIndex((i) => parseInt(i.id) === parseInt(e.target.value))
            console.log(index)            
            if(index != -1){           
                var subValue = parseFloat(this.data.purchaseOrder_subTotal[index])
                this.totalBalance -= subValue
                this.data.supply_id.splice(index, 1)                 
                this.data.purchaseOrder_qty.splice(index, 1)                
                this.data.purchaseOrder_unit.splice(index, 1)                
                this.data.purchaseOrder_subTotal.splice(index, 1)   
                this.checks[indexx] = false
            }
            else{                            
                qty = parseInt(prompt('Please set your desired quantity'))
                console.log(qty != null && !isNaN(qty))
                if(qty != null && !isNaN(qty)){            
                    this.data.supply_id.push(e.target.value)
                    this.data.purchaseOrder_qty.push(qty)
                    supplyObj = this.filteredSupplies.filter((s) => {
                        return parseInt(e.target.value) === parseInt(s.id)
                    })
                    this.data.purchaseOrder_subTotal.push(supplyObj[0].supply_initialPrice * qty)
                    this.data.purchaseOrder_unit.push(supplyObj[0].supply_unit)
                    this.totalBalance += parseFloat(supplyObj[0].supply_initialPrice) * parseFloat(qty)
                    this.checks[indexx] = true
                }
                else{
                    e.target.checked = false
                }
            }
        },
        checkID(supply){  
            var index = this.getFormPO.supplies.findIndex((i) => parseInt(i.id) === parseInt(supply.id))                     
            return this.checks && this.checks[index] ? this.checks[index] : false
        
        },
        getSupplierName(supply){
            var supplierObj = this.getFormPO.suppliers.filter((s) => {
                return parseInt(supply.supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        },
        sendData(){  
            this.initPO(this.data)           
            this.$router.push({ name:'PurchaseOrderSummary' })
        }
    },
    computed: {   
        ...mapGetters(['getFormPO']),
        filteredSupplies(){
            var supplies = [];            
            if(this.filter_supplier != 'None'){
                supplies = this.getFormPO.supplies.filter((s) => {
                    return parseInt(this.filter_supplier) === parseInt(s.supplier_id)
                })
                // if(this.filter_type != 'None' && this.filter_for != 'None'){
                //     supplies = supplies.filter((s) => {
                //         return this.filter_type === s.supply_type && this.filter_for === s.supply_for
                //     })                    
                // }
                // else if(this.filter_type != 'None'){
                //     supplies = supplies.filter((s) => {
                //         return this.filter_type === s.supply_type
                //     })
                // }
                // else if(this.filter_for != 'None'){
                //     supplies = supplies.filter((s) => {
                //         return this.filter_for === s.supply_for
                //     })
                // }               
                return supplies
            }
            else{
                return this.getFormPO.supplies
            }
        },
        getTypes(){
            var types = [];
            var typeSet = new Set()
            this.getFormPO.supplies.forEach((s) => {
                typeSet.add(s.supply_for)
            })
            typeSet.forEach((t) => {
                types.push(t)
            })
            return types
        },
        getCart(){
            var cart = [];                       
            this.data.supply_id.forEach((id, i) => {
                var supplyObj = this.getFormPO.supplies.filter((s) => {
                    return parseInt(id) === parseInt(s.id)
                })
                var item = {
                    supplier_id: supplyObj[0].supplier_id,
                    id: id,
                    supply_name: supplyObj[0].supply_name,
                    supply_type: supplyObj[0].supply_type,
                    supply_for: supplyObj[0].supply_for,
                    supply_description: supplyObj[0].supply_description,
                    purchaseOrder_qty: this.data.purchaseOrder_qty[i],
                    supply_initialPrice: supplyObj[0].supply_initialPrice,
                    purchaseOrder_unit: this.data.purchaseOrder_unit[i],
                    purchaseOrder_subTotal: this.data.purchaseOrder_subTotal[i],                    
                }
                cart.push(item)
            })
            return cart
        },
        validateData(){           
           var supply_id = this.data.supply_id
           var purchaseOrder_num = this.data.purchaseOrder_num
           var purchaseOrder_status = this.data.purchaseOrder_status
           var purchaseOrder_qty = this.data.purchaseOrder_qty
           var purchaseOrder_unit = this.data.purchaseOrder_unit
           var purchaseOrder_subTotal = this.data.purchaseOrder_subTotal
           if(supply_id.length > 0 && purchaseOrder_num && purchaseOrder_status
            && purchaseOrder_qty.length > 0 && purchaseOrder_unit.length > 0 
            && purchaseOrder_subTotal.length > 0){
                return false
           }
           else{
                return true
           }            
        },        
    },

}
</script>

<style>


</style>
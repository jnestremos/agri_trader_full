<template>
    <div class="InitialPurchaseOrder">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Create Supply Purchase Order</h3>        
        </div> 
        <!-- <pre>{{supplyOrder.select}}</pre> -->
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index: 9;">
            <div style="width:85%; height:100%;" class="pb-5">
                <form class="d-flex flex-column justify-content-between" @submit.prevent="">
                    <div class="form-row mb-2">
                        <div class="col-lg-2 me-2">
                            <label for="supplyOrder_date" class="form-label me-4" >Date</label>
                            <input type="date" name="supplyOrder_Date" id="" class="form-control" v-model="supplyOrder.supplyOrder_date">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="supplyOrder_purchaseOrderNum" class="form-label me-4" >Purchase Order No.:</label>
                            <input type="text" name="supplyOrder_purchaseOrderNum" id="" class="form-control" placeholder="PO-123456">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="supplyOrder_purchaseOrderStatus" class="form-label me-4" >Purchase Order Status</label>
                            <input type="text" name="supplyOrder_purchaseOrderStatus" id="" class="form-control" v-model="supplyOrder.supplyOrder_Status">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-2 mb-2 me-3">
                            <label for="supplyOrder_SupplyType" class="form-label me-4" >Choose Supply Type</label>
                            <select class="form-select" @change="filterSupplyType($event)">
                                <option selected value="None">Select Supply Type</option>
                                <option value="Fertilizer">Fertilizer</option>
                                <option value="Insecticide">Insecticide</option>
                                <option value="Fungicide">Fungicide</option>
                                <option value="Herbicide">Herbicide</option>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-2 me-3" v-if="filter">
                            <label for="supplyOrder_SupplyType" class="form-label me-4">Choose Supply For</label>
                            <select class="form-select" @change="filterSupplyFor($event)" ref="supply_for">
                                <option selected value="None">Select Supply Type</option>
                                <option value="Mango">Mango</option>
                                <option value="Grapes">Grapes</option>
                                <option value="Rice">Rice</option>                                 
                            </select>
                        </div>
                        <div class="col-lg-2 mb-2 me-3" v-if="filter">
                            <label for="supplyOrder_Supplier" class="form-label me-4">Supplier</label>
                            <select class="form-select" @change="filterSupplierr($event)" id="supplier" ref="supplier">
                                <option value="None">Select Supplier</option>
                                <option value="Pacifica Agrivet">Pacifica Agrivet</option>
                                <option value="McJenski Agrivet Supply">McJenski Agrivet Supply</option>
                                <option value="Inson Agro Farm Supply">Inson Agro Farm Supply</option>                                 
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="supplyOrder_onHand" class="form-label me-3">Total On-hand Inventory</label>
                            <input type="number" name="supplyOrder_onHand" id="" class="form-control" placeholder="5" style="width:90px" readonly>
                        </div>
                    </div>
                    <!-- height:90%; -->
                    <!-- <pre>{{ supplyOrder }}</pre> -->
                    <div class="container-fluid m-0 p-0" style="width:100%; height:40vh; overflow-y: scroll;
                    ">
                        <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                            <thead align="center">
                                <tr>
                                    <th scope="col">Select</th>
                                    <th scope="col">Supplier</th>
                                    <th scope="col">Supply Type</th>
                                    <th scope="col">Supply For</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Unit</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                <tr v-for="(product, index) in tableProducts()" :key="index">
                                    <th><input type="checkbox" :value="product.id" :checked="product.selected" @change="addOrRemoveSupplyID($event)"></th>
                                    <th>{{ product.supplier }}</th>
                                    <th>{{ product.supply_type }}</th>
                                    <th>{{ product.supply_for }}</th>
                                    <th>{{ product.description }}</th>
                                    <th>{{ product.price }}</th>
                                    <th>{{ product.unit }}</th>
                                </tr>                                                                                                                              -->
                            </tbody>
                        </table>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-1 mb-2 me-3">
                            <label for="supplyOrder_purchaseQuantity" class="form-label me-4" >Quantity</label>
                            <input type="number" name="supplyOrder_purchaseQuantity" id="" class="form-control" v-model="getTotal" disabled>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-lg-2 mb-2 me-3">
                            <label for="supplyOrder_subTotal" class="form-label me-4" >Sub-Total</label>
                            <input type="text" name="supplyOrder_subTotal" id="" class="form-control" v-model="supplyOrder.supplyOrder_subTotal">
                        </div>
                        <div class="col-lg-2 mb-2 me-3">
                            <label for="supplyOrder_transactionPrice" class="form-label me-4" >Transaction Price</label>
                            <input type="text" name="supplyOrder_transactionPrice" id="" class="form-control" v-model="supplyOrder.supplyOrder_transactionPrice">
                        </div>
                    </div>
                    <div class="text-left">
                        <router-link to="/supplyOrder/orderSummary"><button class="btn btn-success" style="width:200px;">Create PO Summary</button></router-link>
                    </div>
                </form>
            </div>
        </div> 
    </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: "InitialPurchaseOrder",
    created() {
        this.readyApp()
    },
    methods: {
        addOrRemoveSupplyID(e){        
            // select = [1, 2]
            // e.target.value = 1
            var searchID = this.supplyOrder.select.filter((id) => {
                return parseInt(e.target.value) === parseInt(id)
            })
            // searchID = [1]
            if(searchID[0]){
                // [1, 2]
                var target = this.supplyOrder.select.indexOf(e.target.value)
                this.supplyOrder.select.splice(target, 1)
                this.supplyOrder.selectQty.splice(target, 1)
                // [2]
            }
            else{
                this.supplyOrder.select.push(e.target.value)
                let qty = prompt(`Set Quantity for Produce # ${e.target.value}`);
                if (qty != null && parseInt(qty)) {
                    this.supplyOrder.selectQty.push(parseInt(qty))
                }                
            }
            var selectID = this.products.filter((p) => {
                return parseInt(e.target.value) === parseInt(p.id)
            })
            selectID[0].selected = !selectID[0].selected
        },        
        filterSupplyType(e){           
            if(e.target.value != 'None'){
                this.filter = e.target.value                
            }
            else{
                this.filter = null
            }
            var supplyFor = this.$refs.supply_for
            this.filterFor = null        
            if(supplyFor){
                supplyFor.value = 'None'
            }                
        },
        filterSupplyFor(e){
            if(e.target.value != 'None'){
                this.filterFor = e.target.value
            }
            else{
                this.filterFor = null
            }
        },
        filterSupplierr(e){
            if(e.target.value != 'None'){
                this.filterSupplier = e.target.value                
            }
            else{
                this.filterSupplier = null
            }
        },
        tableProducts(){
            if(!this.filter){
                return this.products
            }
            else{
                if(!this.filterFor){            
                    var supplier = document.getElementById('supplier')        
                    if(supplier){
                        supplier.value = 'None'
                        this.filterSupplier = null
                    }                    
                    return this.products.filter((p) => {
                        return p.supply_type === this.filter
                    })
                }
                else if(this.filterFor && this.filterSupplier){
                    return this.products.filter((p) => {
                        return p.supply_type === this.filter && p.supply_for === this.filterFor && p.supplier === this.filterSupplier
                    })
                }               
                else{
                    return this.products.filter((p) => {
                        return p.supply_type === this.filter && p.supply_for === this.filterFor
                    })
                }
                
            }
        },
        ...mapActions(['readyApp'])
    },
    computed: {        
        getTotal(){
            var total = 0
            this.supplyOrder.selectQty.forEach((qty) => {
                total += qty
            })
            return total
        }
    },
    data() {
        return {
            filter: null,
            filterFor: null,
            filterSupplier: null,
            supplyOrder: {
                supplyOrder_date: '',
                supplyOrder_purchaseOrderNo: '',
                supplyOrder_purchaseOrderStatus: '',
                supplyType: null,
                supplyOrder_purchaseQuantity: '',
                select: [],
                selectQty: [],
                
                
            },                    
            products: [
                    {
                        id: 1,
                        supplier: 'Pacifica Agrivet',
                        supply_type: 'Fertilizer',
                        supply_for: 'Mango',
                        description: 'Yara Mila UNIK 16 Nitrogen-based fertilizer',
                        price: '500.00',
                        unit: 'Sack',
                        selected: false
                    },
                    {
                        id: 2,
                        supplier: 'Inson Agro Farm Supply',
                        supply_type: 'Fertilizer',
                        supply_for: 'Mango',
                        description: 'Yara Mila UNIK 16 Nitrogen-based fertilizer',
                        price: '530.00',
                        unit: 'Sack',
                        selected: false
                    },
                    {
                        id: 3,
                        supplier: 'McJenski Agrivet Supply',
                        supply_type: 'Fertilizer',
                        supply_for: 'Mango',
                        description: 'Yara Mila UNIK 16 Nitrogen-based fertilizer',
                        price: '510.00',
                        unit: 'Sack',
                        selected: false
                    },
                    {
                        id: 4,
                        supplier: 'Kaumahan sa Sto. Tomas',
                        supply_type: 'Fertilizer',
                        supply_for: 'Grapes',
                        description: 'Yara Mila UNIK 16 Nitrogen-based fertilizer',
                        price: '550.00',
                        unit: 'Sack',
                        selected: false
                    },
                    {
                        id: 5,
                        supplier: 'Inson Agro Farm Supply',
                        supply_type: 'Herbicide',
                        supply_for: 'Rice',
                        description: 'Sygenta Syngenta Direk 800 Pre Emergent Selective Herbicide',
                        price: '978.00',
                        unit: 'bottle',
                        selected: false
                    },
                    {
                        id: 6,
                        supplier: 'Pacifica Agrivet',
                        supply_type: 'Fungicide',
                        supply_for: 'Mango',
                        description: 'Tantor 250 SC Mango Anthracnose',
                        price: '2250.00',
                        unit: 'Bottle',
                        selected: false
                    },
                    {
                        id: 7,
                        supplier: 'McJenski Agrivet Supply',
                        supply_type: 'Herbicide',
                        supply_for: 'Rice',
                        description: 'Sygenta Syngenta Direk 800 Pre Emergent Selective Herbicide For Rice',
                        price: '920.00',
                        unit: 'bottle',
                        selected: false
                    },
                ]
        }
        
    },
}
</script>

<style>


</style>
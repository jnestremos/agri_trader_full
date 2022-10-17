<template>
    <div class="produceInventory">
        <div class="container-fluid d-flex pt-4" style="width:100%; height:35%; margin: 0">
            <div style="width:50%">
                <h3>Produce Inventory</h3>
                <h5 class="mt-5" v-if="getProduceInventory.farm && getProduceInventory.farm_owner">{{ getProduceInventory.farm.farm_name }} - {{ getProduceInventory.farm_owner.owner_firstName + ' ' + getProduceInventory.farm_owner.owner_lastName }}</h5>                
                <h5 class="mt-4" v-if="getProduceInventory.produce">Produce: {{ getProduceInventory.produce.prod_name + ' ' + getProduceInventory.produce.prod_type }}</h5>                
                <div class="d-flex align-items-baseline mt-4">
                    <h5 class="me-3">Date Harvested: </h5>
                    <input v-if="getProduceInventory.produce_yields" type="date" name="" id="" style="width:150px" class="form-control me-3" disabled :value="getProduceInventory.produce_yields[0].produce_yield_dateHarvestFrom">
                    <input v-if="getProduceInventory.produce_yields" type="date" name="" id="" style="width:150px" class="form-control" disabled :value="getProduceInventory.produce_yields[0].produce_yield_dateHarvestTo">
                </div>                
            </div>                      
            <div style="width:50%">
                <h3>Quantity Harvested</h3>
                <div class="d-flex w-100 mt-5">
                    <h5 style="width:50%">Actual Quantity:</h5>
                    <h5 style="width:50%">Quantity On-Hand:</h5>
                </div>                
                <div class="d-flex w-100 mt-2" v-for="(y, index) in getProduceInventory.produce_yields" :key="index">
                    <div class="d-flex w-50 align-items-baseline">
                        <h5 class="me-3" v-if="getProduceInventory.produce_yields.length > 1">{{ y.produce_yield_class }}:</h5>
                        <input type="number" name="" id="" style="width:150px" class="form-control me-3" disabled :value="y.produce_yield_qtyHarvested">
                        <h5 class="me-3">kgs</h5>
                    </div>
                    <div class="d-flex w-50 align-items-baseline">                        
                        <input type="number" name="" id="" style="width:150px" class="form-control me-3" disabled :value="getRemaining(y)">
                        <h5 class="me-3">kgs</h5>
                        <button class="btn btn-danger" :disabled="getRemaining(y) == 0" @click="declareUnsold(y)">Declare as Unsold</button>
                    </div>                    
                </div>            
            </div>                      
        </div>
        <div style="width:100%; height:65%; overflow-y: scroll;">
            <div class="h-100 w-100 p-2">
                <table width="100%" style="background:lightgreen;">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Order Grade</th>
                            <th>Order Type</th>
                            <th>Order Qty</th>                            
                            <th>Qty on Hand</th>
                            <th>Sale Price</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(sale, index) in getProduceInventory.sales" :key="index">
                            <td>{{ sale.bid_order_id }}</td>
                            <td>{{ getOrderDate(sale) }}</td>
                            <td>{{ getOrderClass(sale) }}</td>
                            <td :style="sale.sale_type && sale.sale_type == 'Unsold Goods' ? {'color':'red'} : {}">{{ sale.sale_type }}</td>
                            <td>{{ sale.sale_qty }} kg/s</td>
                            <td>{{ sale.sale_stockLeft }} kg/s</td>
                            <td>Php {{ sale.sale_price.toFixed(2) }}</td>
                            <td>Php {{ sale.sale_total.toFixed(2) }}</td>
                        </tr>                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'ProduceInventory',
    data(){
        return {
            errors: null
        }
    },
    created(){
        this.fetchProduceInventory(this.$route.params.id)
        .then(() => {
            this.readyApp()
        })
        .catch((err) => {
            console.error(err)
        })
        
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProduceInventory', 'addUnsold']),
        declareUnsold(y){
            let qty = prompt('Please set a qty')
            var prodInventoryObj = this.getProduceInventory.produce_inventories.filter((i) => {
                return parseInt(y.id) === parseInt(i.produce_yield_id)
            })
            if(!qty || isNaN(qty) || parseInt(qty) > prodInventoryObj[0].produce_inventory_qtyOnHand){
                alert("Invalid Qty Placed!")   
            }
            else{
                let price = prompt('Please set a price for goods unsold')
                if(!price || isNaN(price)){
                    alert("Invalid Price Placed!")    
                }
                else{
                    var data = {
                        id: y.id,
                        qty: qty,
                        price: price,
                    }
                    this.addUnsold(data)
                    .then(() => {
                        location.reload()
                    })
                    .catch((err) => {
                        console.log(err)
                        this.errors = err.response.data.errors
                        for(var error in this.errors){
                            this.$toastr.e(this.errors[error][0])
                        }                        
                    })
                }
            }            
        },
        getRemaining(y){
            var prodInventoryObj = this.getProduceInventory.produce_inventories.filter((i) => {
                return parseInt(y.id) === parseInt(i.produce_yield_id)
            })
            return prodInventoryObj[0].produce_inventory_qtyOnHand
        },
        getOrderDate(sale){
            if(sale.bid_order_id){
                var bidOrderObj = this.getProduceInventory.bid_orders.filter((o) => {
                    return parseInt(sale.bid_order_id) === parseInt(o.id)
                })
                return bidOrderObj[0].created_at.split('T')[0]
            }
            else{
                return sale.created_at.split('T')[0]
            }         
        },
        getOrderClass(sale){
            if(sale.bid_order_id){
                var bidOrderObj = this.getProduceInventory.bid_orders.filter((o) => {
                    return parseInt(sale.bid_order_id) === parseInt(o.id)
                })
                return bidOrderObj[0].order_grade
            }
            else{
                var prodInventoryObj = this.getProduceInventory.produce_inventories.filter((i) => {
                    return parseInt(sale.produce_inventory_id) === parseInt(i.id)
                })
                var yieldObj = this.getProduceInventory.produce_yields.filter((y) => {
                    return parseInt(prodInventoryObj[0].produce_yield_id) === parseInt(y.id)
                })
                return yieldObj[0].produce_yield_class
            }     
        }
    },
    computed: {
        ...mapGetters(['getProduceInventory']),       
    }
}
</script>

<style scoped>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    text-align: center;
}
td{
    padding: 15px;
}
</style>
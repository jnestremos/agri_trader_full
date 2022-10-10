<template>
    <div class="PurchaseOrderDashboard">
        <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply Purchase Order | Status</h3>
            <div class="d-flex">
                <router-link to="/supplyOrder/refunds"><button class="btn btn-success text-right me-3">See Refunds</button></router-link>
                <router-link to="/supplyOrder/add"><button class="btn btn-success text-right me-3">Add Purchase Order</button></router-link>
                <router-link to="/supplyOrder/returnsDashboard"><button class="btn btn-success text-right">See Purchase Returns</button></router-link>                
            </div>            
        </div>
            <div class="container-fluid w-100 d-flex" style="height:90%; position: relative;">
                <div class="w-100" v-if="getPODashboard.purchaseOrders_filtered && getPODashboard.purchaseOrders_filtered.length > 0">
                    <div class="form-row mb-5" v-for="(order, index) in filtered" :key="index">
                        <div class="col-4" v-for="(o, i) in order" :key="i">
                            <div class="card mt-3 ms-3" style="height: 90%; background-color: #E0EDCA; border-radius: 20px;">
                                <div class="card-body" style="position:relative">
                                    <div @click="triggerModal(o)" style="position:absolute; height:100%; width:100%; z-index:9; top:0; left:0; cursor: pointer;"></div>
                                    <label class="card-title font-weight-bold">{{ getSupplierName(o) }}</label> <!-- name of supply -->
                                    <label class="d-flex align-items-baseline">Purchase Order No. <p class="ms-3">{{ o.purchaseOrder_num }}</p></label>
                                    <label class="d-flex align-items-baseline">Number of Products: <p class="ms-3">{{ o.qty }}</p></label>                                    
                                    <label class="d-flex align-items-baseline">Total Balance: <p class="ms-3">{{ getTotalBalance(o) }}</p></label>
                                    <label class="d-flex align-items-baseline">Status: <p class="ms-3 font-weight-bold" style="color:red">{{ o.purchaseOrder_status }}</p></label>
                                </div>
                            </div>
                            <b-modal size="xl" :hide-footer="checkStatus(o.purchaseOrder_status)" scrollable :id="`modal-${o.purchaseOrder_num}`" :title="`Purchase Order No. ${o.purchaseOrder_num}`">
                                <div class="w-100 h-100">
                                    <div class="d-flex w-100 h-100">
                                        <div class="h-100" :style="[getOrders(o).length > 9 ? {'overflow-y': 'scroll'} : {}, {'width':'60%'}]">
                                            <table style="width:100%; background:lightgray">
                                                <thead>
                                                    <tr>
                                                        <th>Supplier Name</th>
                                                        <th>Supply ID</th>
                                                        <th>Supply Name</th>
                                                        <th>Supply Qty</th>
                                                        <th>Supply Unit</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(order, index) in getOrders(o)" :key="index">
                                                        <td>{{ getSupplierName(order) }}</td>
                                                        <td>{{ order.supply_id }}</td>
                                                        <td>{{ getSupplyName(order) }}</td>
                                                        <td>{{ order.purchaseOrder_qty }}</td>
                                                        <td>{{ order.purchaseOrder_unit }}</td>
                                                        <td>{{ order.purchaseOrder_subTotal.toFixed(2) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="h-100 p-4" style="width:40%;">
                                            <!-- <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                                <h5>Supplier Name: {{ getSupplierName(o) }}</h5>                                                
                                            </div> -->
                                            <!-- <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                                <h5>Payment Method: {{ getPaymentMethod(o) }}</h5>                                                
                                            </div>
                                            <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                                <h5>Account Name: {{ getAccName(o) }}</h5>                                                
                                            </div>
                                            <div v-if="getAccNum(o)" class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                                <h5>Account Number: {{ getAccNum(o) }}</h5>                                                
                                            </div> -->
                                            <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                                <h5>Total Balance: {{ getTotalBalance(o) }}</h5>                                                
                                            </div>
                                            <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                                <h5>Paid Balance: {{ getPaidBalance(o) }}</h5>                                                
                                            </div>
                                            <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                                <h5>Remaining Balance: {{ getRemainingBalance(o) }}</h5>                                                
                                            </div>                                            
                                            <div class="d-flex align-items-baseline">
                                                <h5 class="me-3">Status:</h5>
                                                <select v-if="o.purchaseOrder_status == 'Pending'" name="" id="" class="form-select" style="width:150px" @change="setStatus($event)">
                                                    <option :selected="o.purchaseOrder_status == 'Pending'" value="Pending">Pending</option>
                                                    <option :selected="o.purchaseOrder_status == 'For Delivery'" value="For Delivery">For Delivery</option>
                                                </select>
                                                <input v-else type="text" name="" disabled id="" class="form-control" :value="o.purchaseOrder_status" style="width:300px">
                                            </div>
                                            <div class="w-100 mt-5">
                                                <b-carousel class="mb-2" id="carousel-1" :interval="0"
                                                controls indicators background="#ababab" style="text-shadow: 1px 1px 2px #333; width:100%; height:100%;">                                                
                                                    <b-carousel-slide v-for="(image, index) in getImages(o)" :key="index" style="height:30vh;">
                                                        <template #img>
                                                            <img class="d-block img-fluid w-100" style="width:100%; height:100%; object-fit:cover" :src="getPODashboard.purchaseOrder_accs && image ? require(`../../../../../public/storage/proof_of_payments/${image}`) : ''">
                                                        </template>                                                        
                                                    </b-carousel-slide>                                                             
                                                </b-carousel>                                                
                                            </div>
                                        </div>  
                                    </div>
                                </div>
                                <template #modal-footer="{ok}">
                                    <b-button variant="primary" v-if="o.purchaseOrder_status == 'Pending'" :disabled="data.purchaseOrder_status == 'Pending' || !data.purchaseOrder_status" @click="ok()">Update Status</b-button>
                                    <b-button variant="primary" v-else-if="o.purchaseOrder_status == 'For Delivery'" :disabled="data.purchaseOrder_status == 'Pending' || !data.purchaseOrder_status" @click="ok()">Pay Outstanding Balance</b-button>                                    
                                    <b-button variant="primary" v-else-if="o.purchaseOrder_status == 'Delivered'" :disabled="data.purchaseOrder_status == 'Pending' || !data.purchaseOrder_status" @click="ok()">Create Receiving Report</b-button>                                    
                                    <b-button variant="primary" v-else-if="o.purchaseOrder_status == 'Pending for Return/Refund Approval'" :disabled="!data.purchaseOrder_status" @click="ok()" >View Receiving Report</b-button>                                                                                                            
                                </template>
                            </b-modal>
                        </div>                        
                    </div>                   
                </div>
                <div class="" style="top:42%; left:42%; position: absolute" v-else>
                    <h2 class="text-black">No Purchase Orders Added!</h2>
                </div>
                <div class="" style="position:absolute; right:5%; bottom:5%" v-if="getPODashboard.total > 6">
                    <p class="text-center">Page {{ getPODashboard.current_page }} out of {{ getPODashboard.last_page }}</p>
                    <ul class="pagination" id="pagination">
                        <li :class="[getPODashboard.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>
                        <li :class="[getPODashboard.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getPODashboard.links" :key="index" @click="showPage(page.label)">
                            <a class="page-link">{{page.label}}</a>
                        </li>                
                        <li :class="[getPODashboard.current_page != getPODashboard.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
                    </ul>
                </div>                      
            </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: "PurchaseOrderDashboard",
    created() {
        this.fetchPODashboard()
       .then(() => {
            if(this.getPODashboard.current_page){ 
                var query = `page=${this.getPODashboard.current_page}`               
                this.fetchPODashboard(query)                
            }
            this.filtered = this.filteredPOArray()         
            this.readyApp()
       })  
    },
    data(){
        return {
            filtered: null,
            data: {
                purchaseOrder_num: null,
                purchaseOrder_status: null,
                purchaseOrder_totalBalance: null,
                purchaseOrder_dpAmount: null,
                purchaseOrder_balance: null,
                purchaseOrder_paymentMethod: null,
                purchaseOrder_bankName: null,
                purchaseOrder_accNum: null,
                purchaseOrder_accName: null,
            }
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchPODashboard', 'updatePOStatus', 'initPO']),
        getSupplierName(order){
            var supplierObj = this.getPODashboard.suppliers.filter((s) => {
                return parseInt(order.supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        },
        checkStatus(status){
            if(status == 'Pending' || status == 'For Delivery' 
            || status == 'Delivered'){
                return false
            }
            else{
                return true
            }            
        },
        getTotalBalance(order){
            var supplyPaymentObj = this.getPODashboard.purchaseOrder_accs.filter((a) => {
                return order.purchaseOrder_num === a.purchaseOrder_num
            })
            return supplyPaymentObj[0].purchaseOrder_totalBalance.toFixed(2)
        },
        getPaidBalance(order){
            var supplyPaymentObj = this.getPODashboard.purchaseOrder_accs.filter((a) => {
                return order.purchaseOrder_num === a.purchaseOrder_num
            })
            if(supplyPaymentObj[0].purchaseOrder_dpAmount == 0){
                return supplyPaymentObj[0].purchaseOrder_dpAmount.toFixed(2) + ' (Reorder for Defects)'
            }
            return supplyPaymentObj[0].purchaseOrder_dpAmount.toFixed(2)
        },
        getRemainingBalance(order){
            var supplyPaymentObj = this.getPODashboard.purchaseOrder_accs.filter((a) => {
                return order.purchaseOrder_num === a.purchaseOrder_num
            })
            return supplyPaymentObj[0].purchaseOrder_balance.toFixed(2)
        },
        getPaymentMethod(order){
            var accObj = this.getPODashboard.purchaseOrder_accs.filter((a) => {
                return order.purchaseOrder_num === a.purchaseOrder_num
            })
            return accObj[0].purchaseOrder_bankName 
            ? accObj[0].purchaseOrder_bankName + ` (${accObj[0].purchaseOrder_paymentMethod})`
            : accObj[0].purchaseOrder_paymentMethod
        },
        getAccName(order){
            var accObj = this.getPODashboard.purchaseOrder_accs.filter((a) => {
                return order.purchaseOrder_num === a.purchaseOrder_num
            })
            return accObj[0].purchaseOrder_accName
        },
        getAccNum(order){
            var accObj = this.getPODashboard.purchaseOrder_accs.filter((a) => {
                return order.purchaseOrder_num === a.purchaseOrder_num
            })
            return accObj[0].purchaseOrder_accNum
        },
        triggerModal(order){
            this.data.purchaseOrder_num = order.purchaseOrder_num
            this.data.purchaseOrder_status = order.purchaseOrder_status
            var accObj = this.getPODashboard.purchaseOrder_accs.filter((a) => {
                return order.purchaseOrder_num === a.purchaseOrder_num
            })
            this.data.purchaseOrder_totalBalance = accObj[0].purchaseOrder_totalBalance
            this.data.purchaseOrder_dpAmount = accObj[0].purchaseOrder_dpAmount
            this.data.purchaseOrder_balance = accObj[0].purchaseOrder_balance
            this.data.purchaseOrder_paymentMethod = accObj[0].purchaseOrder_paymentMethod,
            this.data.purchaseOrder_bankName = accObj[0].purchaseOrder_bankName,
            this.data.purchaseOrder_accNum = accObj[0].purchaseOrder_accNum,
            this.data.purchaseOrder_accName = accObj[0].purchaseOrder_accName,
            this.$bvModal.show(`modal-${order.purchaseOrder_num}`)
        },
        setStatus(e){
            this.data.purchaseOrder_status = e.target.value
        },
        getOrders(order){
            var orderObj = this.getPODashboard.purchaseOrders.filter((o) => {
                return order.purchaseOrder_num === o.purchaseOrder_num
            })
            return orderObj
        },
        getImages(order){
            var accObj = this.getPODashboard.purchaseOrder_accs.filter((o) => {
                return order.purchaseOrder_num === o.purchaseOrder_num
            })
            return accObj[0].purchaseOrder_images
        },
        getSupplyName(order){
            var supplyObj = this.getPODashboard.supplies.filter((s) => {
                return parseInt(order.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_name + ' ' + supplyObj[0].supply_type
        },
        filteredPOArray(){
            var filtered = [];
            var arr = this.getPODashboard.purchaseOrders_filtered;             
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        },
        showPrevious(){
            var query = this.getPODashboard.prev_page_url.split('?')[1]
            this.fetchPODashboard(query)
            .then(() => {
                this.filtered = this.filteredPOArray()   
            })            
        },
        showNext(){
            var query = this.getPODashboard.next_page_url.split('?')[1]
            this.fetchPODashboard(query)
            .then(() => {
                this.filtered = this.filteredPOArray()   
            })  
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchPODashboard(query)
            .then(() => {
                this.filtered = this.filteredPOArray()   
            })
        }
    },
    mounted(){
        this.$root.$on('bv::modal::hide', (bvEvent, modalID) => {            
            if(bvEvent.trigger == 'headerclose'){
                this.data.purchaseOrder_num = null;
                this.data.purchaseOrder_status = null;
                this.data.purchase_totalBalance = null
                this.data.purchase_dpAmount = null
                this.data.purchase_balance = null
                this.data.purchaseOrder_paymentMethod = null
                this.data.purchaseOrder_bankName = null
                this.data.purchaseOrder_accNum = null
                this.data.purchaseOrder_accName = null
            }
            else if(bvEvent.trigger == 'ok'){
                var arr = modalID.split('-')
                var id = arr[1] + '-' + arr[2]
                var orderObj = this.getPODashboard.purchaseOrders_filtered.filter((o) => {
                    return o.purchaseOrder_num === id
                })
                if(orderObj[0].purchaseOrder_status == 'Pending'){
                    this.updatePOStatus(orderObj[0].purchaseOrder_num)
                    .then(() => {
                        this.$toastr.s('Purchase Order Added Successfully!')
                        setTimeout(() => {
                            location.reload()
                        }, 5000)
                    })
                }
                else if(orderObj[0].purchaseOrder_status == 'For Delivery'){  
                    this.initPO(this.data)                  
                    this.$router.push({ name: 'PaymentDashboard' })
                }
                else if(orderObj[0].purchaseOrder_status == 'Delivered'){
                    this.$router.push({ path: `/stockIn/${orderObj[0].purchaseOrder_num}` })
                }
                else if(orderObj[0].purchaseOrder_status == 'Pending for Return/Refund Approval'){
                    this.$router.push({ path: `/receiving/report/${orderObj[0].purchaseOrder_num}` })
                }
                
            }
        })        
    },
    computed:{
        ...mapGetters(['getPODashboard'])
    }
}
</script>

<style scoped>
table, th, td {
    border:2px solid black;
    border-collapse: collapse;
    border-spacing: 0;
}
th, td{
    padding: 10px;
    text-align:center;
}
</style>
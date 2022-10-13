<template>
    <div class="SupplyPurchaseOrderReports">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Supply Purchase Order Report</h3>        
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Purchase Order Number</label>
                  <select class="form-select" @change="setOrder($event)">
                      <option value="None">Select PO #</option>
                      <option v-for="(order, index) in getPurchaseOrderReport.purchaseOrders_filtered" :key="index" :value="order.purchaseOrder_num">{{ order.purchaseOrder_num }}</option>
                  </select>
              </div>
          </div>
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">From</label>
                <input type="date" class="form-control" v-model="filter_dateFrom">
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">To</label>
                <input type="date" class="form-control" v-model="filter_dateTo">
            </div>
          </div>
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Purchase Order No.</th>                                                    
                          <th scope="col">Items Ordered</th>
                          <th scope="col">Remaining Balance</th>
                          <th scope="col">Total Amount</th>
                          <th scope="col">Status</th>
                          <th scope="col">Date Issued</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr v-for="(order, index) in filteredTable" :key="index" @click="triggerModal(order)">
                        <td>{{ order.purchaseOrder_num }}</td>
                        <td>{{ order.qty }}</td>
                        <td>{{ getRemainingBalance(order) }}</td>
                        <td>{{ getTotal(order) }}</td>
                        <td>{{ order.purchaseOrder_status }}</td>
                        <td>{{ order.created_at.split('T')[0] }}</td>

                        <b-modal size="xl" :hide-footer="checkStatus(order.purchaseOrder_status)" scrollable :id="`modal-${order.purchaseOrder_num}`" :title="`Purchase Order No. ${order.purchaseOrder_num}`">
                            <div class="w-100 h-100">
                                <div class="d-flex w-100 h-100">
                                    <div class="h-100" :style="[getOrders(order).length > 9 ? {'overflow-y': 'scroll'} : {}, {'width':'60%'}]">
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
                                                <tr v-for="(orderr, index) in getOrders(order)" :key="index">
                                                    <td>{{ getSupplierName(orderr) }}</td>
                                                    <td>{{ orderr.supply_id }}</td>
                                                    <td>{{ getSupplyName(orderr) }}</td>
                                                    <td>{{ orderr.purchaseOrder_qty }}</td>
                                                    <td>{{ orderr.purchaseOrder_unit }}</td>
                                                    <td>{{ orderr.purchaseOrder_subTotal }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="h-100 p-4" style="width:40%;">                                        
                                        <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                            <h5>Total Balance: {{ getTotal(order) }}</h5>                                                
                                        </div>
                                        <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                            <h5>Paid Balance: {{ getPaidBalance(order) }}</h5>                                                
                                        </div>
                                        <div class="d-flex justify-content-between align-items-baseline w-100 mb-5">
                                            <h5>Remaining Balance: {{ getRemainingBalance(order) }}</h5>                                                
                                        </div>                                            
                                        <div class="d-flex align-items-baseline">
                                            <h5 class="me-3">Status:</h5>
                                            <select v-if="order.purchaseOrder_status == 'Pending'" name="" id="" class="form-select" style="width:150px" @change="setStatus($event)">
                                                <option :selected="order.purchaseOrder_status == 'Pending'" value="Pending">Pending</option>
                                                <option :selected="order.purchaseOrder_status == 'For Delivery'" value="For Delivery">For Delivery</option>
                                            </select>
                                            <input v-else type="text" name="" disabled id="" class="form-control" :value="order.purchaseOrder_status" style="width:300px">
                                        </div>
                                        <div class="w-100 mt-5">
                                            <b-carousel class="mb-2" id="carousel-1" :interval="0"
                                            controls indicators background="#ababab" style="text-shadow: 1px 1px 2px #333; width:100%; height:100%;">                                                
                                                <b-carousel-slide v-for="(image, index) in getImages(order)" :key="index" style="height:30vh;">
                                                    <template #img>
                                                        <img class="d-block img-fluid w-100" style="width:100%; height:100%; object-fit:cover" :src="getPurchaseOrderReport.purchaseOrder_accs && image ? require(`../../../../../public/storage/proof_of_payments/${image}`) : ''">
                                                    </template>                                                        
                                                </b-carousel-slide>                                                             
                                            </b-carousel>                                                
                                        </div>
                                    </div>  
                                </div>
                            </div>
                            <template #modal-footer="{ok}">
                                <b-button variant="primary" v-if="order.purchaseOrder_status == 'Pending'" :disabled="data.purchaseOrder_status == 'Pending' || !data.purchaseOrder_status" @click="ok()">Update Status</b-button>
                                <b-button variant="primary" v-else-if="order.purchaseOrder_status == 'For Delivery'" :disabled="data.purchaseOrder_status == 'Pending' || !data.purchaseOrder_status" @click="ok()">Pay Outstanding Balance</b-button>                                    
                                <b-button variant="primary" v-else-if="order.purchaseOrder_status == 'Delivered'" :disabled="data.purchaseOrder_status == 'Pending' || !data.purchaseOrder_status" @click="ok()">Create Receiving Report</b-button>                                    
                                <b-button variant="primary" v-else-if="order.purchaseOrder_status == 'Pending for Return/Refund Approval'" :disabled="!data.purchaseOrder_status" @click="ok()" >View Receiving Report</b-button>                                                                                                            
                            </template>
                        </b-modal>                        
                    </tr>                                                                               
                  </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </template>
  
<script>
import { add, format, sub } from 'date-fns';
import { mapActions, mapGetters } from 'vuex';
export default {
    name: "SupplyPurchaseOrderReports",
    created() {
    this.fetchPurchaseOrderReport()
    .then(() => {
        if(this.getPurchaseOrderReport.purchaseOrders_filtered 
        && this.getPurchaseOrderReport.purchaseOrders_filtered.length > 0){
            var orders = this.getPurchaseOrderReport.purchaseOrders_filtered.sort((a, b) => {
                return new Date(a.created_at) - new Date(b.created_at)
            })
            this.filter_dateFrom = format(new Date(orders[0].created_at), 'yyyy-MM-dd')
            this.filter_dateTo = format(new Date(orders[orders.length - 1].created_at), 'yyyy-MM-dd')
        }
        this.readyApp()            
    })
    },
    data(){
    return {
        filter_order: 'None',
        filter_dateFrom: null,
        filter_dateTo: null,
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
watch: {
    filter_dateFrom(newVal){
        if(newVal >= this.filter_dateTo){
            this.filter_dateFrom = format(sub(new Date(newVal), {
                days: 1
            }), 'yyyy-MM-dd')
        }
    },
    filter_dateTo(newVal){
        if(newVal <= this.filter_dateFrom){
            this.filter_dateTo = format(add(new Date(newVal), {
                days: 1
            }), 'yyyy-MM-dd')
        }
    }
},
methods: {
    ...mapActions(['readyApp', 'fetchPurchaseOrderReport', 'updatePOStatus', 'initPO']),
    triggerModal(order){
        this.data.purchaseOrder_num = order.purchaseOrder_num
        this.data.purchaseOrder_status = order.purchaseOrder_status
        var accObj = this.getPurchaseOrderReport.purchaseOrder_accs.filter((a) => {
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
    setOrder(e){
        this.filter_order = e.target.value
    },
    getRemainingBalance(order){
        var accObj = this.getPurchaseOrderReport.purchaseOrder_accs.filter((o) => {
            return order.purchaseOrder_num === o.purchaseOrder_num
        })
        return accObj[0].purchaseOrder_balance
    },
    getTotal(order){
        var accObj = this.getPurchaseOrderReport.purchaseOrder_accs.filter((o) => {
            return order.purchaseOrder_num === o.purchaseOrder_num
        })
        return accObj[0].purchaseOrder_totalBalance
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
    getOrders(order){
        var orderObj = this.getPurchaseOrderReport.purchaseOrders.filter((o) => {
            return order.purchaseOrder_num === o.purchaseOrder_num
        })
        return orderObj
    },
    getSupplierName(order){
        var supplierObj = this.getPurchaseOrderReport.suppliers.filter((s) => {
            return parseInt(order.supplier_id) === parseInt(s.id)
        })
        return supplierObj[0].supplier_name
    },
    getSupplyName(order){
        var supplyObj = this.getPurchaseOrderReport.supplies.filter((s) => {
            return parseInt(order.supply_id) === parseInt(s.id)
        })
        return supplyObj[0].supply_name + ' ' + supplyObj[0].supply_type
    },
    getPaidBalance(order){
        var supplyPaymentObj = this.getPurchaseOrderReport.purchaseOrder_accs.filter((a) => {
            return order.purchaseOrder_num === a.purchaseOrder_num
        })
        if(supplyPaymentObj[0].purchaseOrder_dpAmount == 0){
            return supplyPaymentObj[0].purchaseOrder_dpAmount + ' (Reorder for Defects)'
        }
        return supplyPaymentObj[0].purchaseOrder_dpAmount
    },  
    getImages(order){
        var accObj = this.getPurchaseOrderReport.purchaseOrder_accs.filter((o) => {
            return order.purchaseOrder_num === o.purchaseOrder_num
        })
        return accObj[0].purchaseOrder_images
    },     
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
            var orderObj = this.getPurchaseOrderReport.purchaseOrders_filtered.filter((o) => {
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
computed: {
    ...mapGetters(['getPurchaseOrderReport']),
    filteredTable(){
        var table = []
        if(this.getPurchaseOrderReport.purchaseOrders_filtered){
            table = this.getPurchaseOrderReport.purchaseOrders_filtered.filter((o) => {
                return format(new Date(o.created_at), 'yyyy-MM-dd') >= this.filter_dateFrom
                && format(new Date(o.created_at), 'yyyy-MM-dd') <= this.filter_dateTo
            })
            if(this.filter_order != 'None'){
                table = table.filter((o) => {
                    return this.filter_order === o.purchaseOrder_num
                })
                return table
            }
            else{
                return table
            }
        }
        return table
    }
    }
}
</script>


<style scoped>
table, th, td {    
    border-collapse: collapse;
    border-spacing: 0;
}
th, td{
    border:2px solid black;
    padding: 10px;
    text-align:center;
}  
</style>
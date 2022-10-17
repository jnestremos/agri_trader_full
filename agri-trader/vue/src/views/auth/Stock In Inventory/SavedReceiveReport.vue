<template>
  <div class="SavedReceiveReport">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply | Return/Refund</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
            <form class="d-flex flex-column justify-content-between mt-2 ms-2" style="height:20%">
                <div class="form-row mb-2">
                    <div class="col-lg-3 me-3">
                        <label for="stockIn_date" class="form-label me-4" >Date</label>
                        <input type="date" name="stockIn_Date" id="" class="form-control" placeholder="09/05/2022" v-model="dateToday" disabled>
                    </div>
                    <div class="col-lg-3 me-3">
                        <label for="stockIn_purchaseOrderNum" class="form-label me-4" >Purchase Order No.:</label>
                        <input type="text" name="stockIn_purchaseOrderNum" id="" disabled class="form-control"  placeholder="PO-1234567" v-model="data.purchaseOrder_num">
                    </div>
                </div>                
                <div class="mb-2" style="width:100% height:90%; clear:left;">
                    <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:100%;">
                        <thead align="center">
                            <tr>
                                <th scope="col">Return</th>
                                <th scope="col">Refund</th>
                                <th scope="col">Supply Name</th>
                                <th scope="col">Supply Type</th>
                                <th scope="col">Supply For</th>                                    
                                <th scope="col">Quantity Ordered</th>
                                <th scope="col">Unit</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Received</th>
                                <th scope="col">Defective</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <tr v-for="(record, index) in getPOForRR.orders" :key="index">
                                <td><input type="radio" :name="'type'+index" checked @change="setReturn($event)" :value="record.supply_id"></td>
                                <td><input type="radio" :name="'type'+index" @change="setRefund($event)" :value="record.supply_id"></td>
                                <td>{{ getSupplyName(record) }}</td>
                                <td>{{ getSupplyType(record) }}</td>
                                <td>{{ getSupplyFor(record) }}</td>
                                <td>{{ record.purchaseOrder_qtyGood + record.purchaseOrder_qtyDefect }}</td>
                                <td>{{ record.purchaseOrder_unit }}</td>
                                <td>{{ record.purchaseOrder_subTotal }}</td>
                                <td><i>{{ record.purchaseOrder_qtyGood }}</i></td>
                                <td><i>{{ record.purchaseOrder_qtyDefect }}</i></td>
                            </tr>                            
                        </tbody>
                    </table>
                </div>
                <div class="form-row">
                    <div class="col-lg-3 me-3">
                        <label for="stockIn_transactedBy" class="form-label me-4" >Transacted By</label>
                        <input type="text" name="stockIn_transactedBy" id="" class="form-control" disabled v-model="name">
                    </div>
                    <div class="col-lg-3 me-3">
                        <label for="stockIn_Remarks" class="form-label me-4" >Remarks for Refund</label>
                        <input v-if="refund_ids" type="text" :disabled="!refund_ids.length > 0" name="stockIn_transactedBy" id="" class="form-control" v-model="data.refund_remark">
                    </div>
                    <div class="col-lg-3 me-3">
                        <label for="stockIn_Remarks" class="form-label me-4" >Remarks for Return</label>
                    <input v-if="return_ids" type="text" :disabled="!return_ids.length > 0" name="stockIn_transactedBy" id="" class="form-control" v-model="data.return_remark">
                    </div>
                </div>
                <div class="btn-toolbar pt-4" role="toolbar">
                    <div class="btn-group me-3" v-if="return_ids && refund_ids && return_ids.length > 0 && refund_ids.length > 0">
                        <b-button variant="success" style="width:200px; height:60px" @click="sendReturnRefund()">Return and Exchange Item / Refund</b-button>
                    </div>
                    <div class="btn-group me-3" v-else-if="return_ids && return_ids.length > 0">
                        <b-button variant="success" style="width:200px; height:60px" @click="sendReturn()">Return and Exchange Item</b-button>
                    </div>
                    <div class="btn-group me-3" v-else-if="refund_ids && refund_ids.length > 0">
                        <b-button variant="success" style="width:200px; height:60px" @click="sendRefund()">Refund</b-button>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
  </div>
</template>

<script>
import { format } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "SavedReceiveReport",
    created() {
        this.fetchPOforUpdateRR(this.$route.params.id)
        .then(() => {
            this.data.purchaseOrder_num = this.$route.params.id                       
            var return_ids = [];
            var refund_ids = [];
            this.getPOForRR.orders.forEach((o) => {                
                return_ids.push(o.supply_id)                    
            })
            this.return_ids = return_ids
            this.refund_ids = refund_ids
            this.name = this.getName
            this.readyApp()
        })
        .catch((err) => {
            console.error(err)
            this.$router.push({ name: 'PurchaseOrderDashboard' })
        })
    },
    data(){
        return{
            data: {
                purchaseOrder_num: null,
                supply_id: [],
                // purchaseOrder_qtyGood: null,
                purchaseOrder_qtyDefect: [],
                purchaseOrder_unit: [],
                purchaseOrder_subTotal:[],
                refund_remark: null,
                return_remark: null,
            },
            dateToday: format(new Date(), 'yyyy-MM-dd'),
            name: null,
            return_ids: null,
            refund_ids: null,
        }
    }, 
    methods: {
        ...mapActions(['readyApp', 'fetchPOforUpdateRR', 'issueReturn', 'issueRefund']),
        getSupplyName(order){
            var supplyObj = this.getPOForRR.supplies.filter((s) => {
                return parseInt(order.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_name
        },
        getSupplyType(order){
            var supplyObj = this.getPOForRR.supplies.filter((s) => {
                return parseInt(order.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_type
        },
        getSupplyFor(order){
            var supplyObj = this.getPOForRR.supplies.filter((s) => {
                return parseInt(order.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_for
        },
        setRefund(e){
            this.return_ids.splice(this.return_ids.indexOf(parseInt(e.target.value)), 1)
            this.refund_ids.push(parseInt(e.target.value))
            if(this.return_ids.length == 0){
                this.data.return_remark = null
            }
        },
        setReturn(e){
            this.refund_ids.splice(this.refund_ids.indexOf(parseInt(e.target.value)), 1)
            this.return_ids.push(parseInt(e.target.value))
            if(this.refund_ids.length == 0){
                this.data.refund_remark = null
            }
        },
        sendReturnRefund(){
            // 'issueReturn', 'issueRefund'
            if(!this.data.return_remark || !this.data.refund_remark){
                if(!this.data.return_remark && this.data.refund_remark){
                    this.$toastr.e('Remarks for return is required!')
                }
                else if(this.data.return_remark && !this.data.refund_remark){
                    this.$toastr.e('Remarks for refund is required!')
                }
                else{
                    this.$toastr.e('Remarks for return and refund is required!')
                }
            }
            else{
                this.data.supply_id = this.return_ids
                var orderObj = null
                this.data.supply_id.forEach((id) => {
                    orderObj = this.getPOForRR.orders.filter((o) => {
                        return this.data.purchaseOrder_num === o.purchaseOrder_num && parseInt(id) === parseInt(o.supply_id)
                    })
                    this.data.purchaseOrder_qtyDefect.push(orderObj[0].purchaseOrder_qtyDefect)
                    this.data.purchaseOrder_unit.push(orderObj[0].purchaseOrder_unit)
                    this.data.purchaseOrder_subTotal.push(orderObj[0].purchaseOrder_subTotal / orderObj[0].purchaseOrder_qtyDefect)                
                })
                this.issueReturn(this.data)
                .then(() => {
                    this.data.supply_id = this.refund_ids
                    this.data.purchaseOrder_qtyDefect = []
                    this.data.purchaseOrder_unit = []
                    this.data.purchaseOrder_subTotal = []
                    this.data.supply_id.forEach((id) => {
                        orderObj = this.getPOForRR.orders.filter((o) => {
                            return this.data.purchaseOrder_num === o.purchaseOrder_num && parseInt(id) === parseInt(o.supply_id)
                        })
                        this.data.purchaseOrder_qtyDefect.push(orderObj[0].purchaseOrder_qtyDefect)
                        this.data.purchaseOrder_unit.push(orderObj[0].purchaseOrder_unit)
                        this.data.purchaseOrder_subTotal.push(orderObj[0].purchaseOrder_subTotal / orderObj[0].purchaseOrder_qtyDefect)                
                    })
                    this.issueRefund(this.data)
                    .then(() => {
                        this.$toastr.s('Refund and Return Requests Successful!')
                        setTimeout(() => {
                            this.$router.push({ name: 'PurchaseOrderDashboard' })
                        }, 5000)
                    })
                    .catch((err) => {
                        console.error(err)
                    })

                })
                .catch((err) => {
                    console.error(err)
                })
            }                    
        },
        sendReturn(){
            this.data.supply_id = this.return_ids
            var orderObj = null
            this.data.supply_id.forEach((id) => {
                orderObj = this.getPOForRR.orders.filter((o) => {
                    return this.data.purchaseOrder_num === o.purchaseOrder_num && parseInt(id) === parseInt(o.supply_id)
                })
                this.data.purchaseOrder_qtyDefect.push(orderObj[0].purchaseOrder_qtyDefect)
                this.data.purchaseOrder_unit.push(orderObj[0].purchaseOrder_unit)
                this.data.purchaseOrder_subTotal.push(orderObj[0].purchaseOrder_subTotal / orderObj[0].purchaseOrder_qtyDefect)                
            })
            this.issueReturn(this.data)
            .then(() => {
                this.$toastr.s('Return Request Successful!')
                setTimeout(() => {
                    this.$router.push({ name: 'PurchaseOrderDashboard' })
                }, 5000)
            })
            .catch((err) => {
                console.error(err)
            })
        },
        sendRefund(){
            this.data.supply_id = this.refund_ids
            var orderObj = null
            this.data.supply_id.forEach((id) => {
                orderObj = this.getPOForRR.orders.filter((o) => {
                    return this.data.purchaseOrder_num === o.purchaseOrder_num && parseInt(id) === parseInt(o.supply_id)
                })
                this.data.purchaseOrder_qtyDefect.push(orderObj[0].purchaseOrder_qtyDefect)
                this.data.purchaseOrder_unit.push(orderObj[0].purchaseOrder_unit)
                this.data.purchaseOrder_subTotal.push(orderObj[0].purchaseOrder_subTotal / orderObj[0].purchaseOrder_qtyDefect)                
            })
            this.issueRefund(this.data)
            .then(() => {
                this.$toastr.s('Refund Request Successful!')
                setTimeout(() => {
                    this.$router.push({ name: 'PurchaseOrderDashboard' })
                }, 5000)
            })
            .catch((err) => {
                console.error(err)
            })
        }
    },
    computed: {
        ...mapGetters(['getPOForRR', 'getName'])
    }
}
</script>

<style>

</style>
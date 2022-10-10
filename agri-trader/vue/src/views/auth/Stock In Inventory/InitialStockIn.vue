<template>
  <div class="InitialStockIn">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply | Receive from Supply Purchase Order</h3>
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
                        <!-- <input type="text" name="stockIn_purchaseOrderNum" id="" disabled class="form-control"  placeholder="PO-1234567"> -->
                        <input type="text" name="" id="" class="form-control" style="width:350px;" disabled v-model="data.purchaseOrder_num">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <p class="font-weight-bold" v-if="getPOForRR.supplier" style="font-size:110%">Supplier Name: {{ getPOForRR.supplier.supplier_name }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="font-weight-bold" v-if="getPOForRR.supplier_contact_person" style="font-size:110%">Contact Person: {{ getPOForRR.supplier_contact_person.contact_firstName + ' ' + getPOForRR.supplier_contact_person.contact_lastName }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="font-weight-bold" v-if="getPOForRR.supplier_contact" style="font-size:110%">Contact Number: {{ getPOForRR.supplier_contact.supplier_phoneNumber }}</p>
                    </div>
                </div>
                <div class="mb-2" style="width:100% height:90%; clear:left;">
                    <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:100%;">
                        <thead align="center">
                            <tr>                                
                                <th scope="col">Supplier Name</th>
                                <th scope="col">Supply Name</th>
                                <th scope="col">Supply Type</th>
                                <th scope="col">Supply For</th>                                    
                                <th scope="col">Quantity Ordered</th>
                                <th scope="col">Unit</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Received</th>
                                <th scope="col">Defective / For Return</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <tr v-for="(record, index) in getPOForRR.orders" :key="index">                            
                                <td>{{ getSupplierName(record) }}</td>
                                <td>{{ getSupplyName(record) }}</td>
                                <td>{{ getSupplyType(record) }}</td>
                                <td>{{ getSupplyFor(record) }}</td>
                                <td>{{ record.purchaseOrder_qty }}</td>
                                <td>{{ record.purchaseOrder_unit }}</td>
                                <td>{{ record.purchaseOrder_subTotal }}</td>
                                <td v-if="data.purchaseOrder_qtyGood && data.purchaseOrder_qtyGood.length > 0">
                                    <input type="number"  class="form-control" style="width:150px" v-model="data.purchaseOrder_qtyGood[index]" disabled>
                                </td>
                                <td v-if="data.purchaseOrder_qtyDefect && data.purchaseOrder_qtyDefect.length > 0">
                                    <input type="number" :max="record.purchaseOrder_qty" min="0" onkeydown="return false" class="form-control" style="width:150px" v-model="data.purchaseOrder_qtyDefect[index]">
                                </td>
                            </tr>                            
                        </tbody>
                    </table>
                </div>
                <div class="form-row mt-2 mb-2">
                    <div class="col-sm-2">
                        <label for="orderSummary_quantityReceived" class="form-label me-4" >Total Amount</label>
                        <input v-if="data.purchaseOrder_subTotal && data.purchaseOrder_subTotal.length > 0" type="text" name="orderSummary_totalAmount" id="" class="form-control" style="width: 40%" disabled :value="data.purchaseOrder_subTotal.reduce((partialSum, a) => partialSum + a, 0)">
                    </div>                                       
                </div>
                <div class="form-row">
                    <div class="col-lg-3 me-3">
                        <label for="stockIn_transactedBy" class="form-label me-4" >Transacted By</label>
                        <input type="text" name="stockIn_transactedBy" id="" class="form-control" disabled v-model="name">
                    </div>
                    <div class="col-lg-3 me-3">
                        <label for="stockIn_Remarks" class="form-label me-4" >Remarks</label>
                        <input type="text" name="stockIn_transactedBy" id="" class="form-control" v-model="data.report_remark">
                    </div>
                </div>
                <div class="btn-toolbar pt-4" role="toolbar">
                    <div class="btn-group me-3">
                        <b-button variant="success" style="width:200px; height:60px" @click="addReport()">Stock In</b-button>
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
    name: "InitialStockIn",
    created() {
        this.fetchPOForRR(this.$route.params.id)
        .then(() => {
            this.data.purchaseOrder_num = this.$route.params.id
            var supply_id = []
            var purchaseOrder_qtyGood = []
            var purchaseOrder_qtyDefect = []
            var purchaseOrder_unit = []
            var purchaseOrder_subTotal = []
            this.getPOForRR.orders.forEach((o) => {
                supply_id.push(o.supply_id)
                purchaseOrder_qtyGood.push(o.purchaseOrder_qty)
                purchaseOrder_qtyDefect.push(0)
                purchaseOrder_unit.push(o.purchaseOrder_unit)
                purchaseOrder_subTotal.push(o.purchaseOrder_subTotal)
            })
            this.data.supply_id = supply_id
            this.data.purchaseOrder_qtyGood = purchaseOrder_qtyGood
            this.data.purchaseOrder_qtyDefect = purchaseOrder_qtyDefect
            this.data.purchaseOrder_unit = purchaseOrder_unit
            this.data.purchaseOrder_subTotal = purchaseOrder_subTotal
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
                supply_id: null,
                purchaseOrder_qtyGood: null,
                purchaseOrder_qtyDefect: null,
                purchaseOrder_unit: null,
                purchaseOrder_subTotal:null,
                report_remark: null,
            },
            dateToday: format(new Date(), 'yyyy-MM-dd'),
            name: null
        }
    },
    watch: {
        'data.purchaseOrder_qtyDefect'(newVal){
            var totalQtys = [];
            this.getPOForRR.orders.forEach((order) => {
                totalQtys.push(order.purchaseOrder_qty)
            })
            newVal.forEach((q, index) => {
                this.data.purchaseOrder_qtyGood[index] = totalQtys[index] - q
            })
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchPOForRR', 'sendRR']),
        getSupplierName(order){
            var supplierObj = this.getPOForRR.suppliers.filter((s) => {
                return parseInt(order.supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        },
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
        addReport(){
            this.sendRR(this.data)
            .then(() => {
                this.$toastr.s('Report Added Successfully!')
                setTimeout(() => {
                    var total = this.data.purchaseOrder_qtyDefect.reduce((partialSum, a) => partialSum + a, 0)
                    console.log(total)
                    if(total > 0){
                        this.$router.push({ path: `/receiving/report/${this.data.purchaseOrder_num}` })
                    }
                    else{
                        this.$router.push({ name: 'PurchaseOrderDashboard' })
                    }
                    
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
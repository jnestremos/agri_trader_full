<template>
    <div class="PurchaseOrderSummary">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply Purchase Order | Summary</h3>
        </div>
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
            <div style="width:85%; height:65%" class="pb-5">
                <form class="d-flex flex-column justify content between mt-2" style="height:20%">
                    <div class="form-row mb-2">
                        <div class="col-lg-2 me-2">
                            <label for="orderSummary_date" class="form-label me-4" >Date</label>
                            <input type="text" name="orderSummary_Date" id="" class="form-control" disabled v-model="dateToday" placeholder="09/05/2022">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_purchaseOrderNum" class="form-label me-4" >Purchase Order No.:</label>
                            <input type="text" name="orderSummary_purchaseOrderNum" id="" disabled class="form-control" v-model="data.purchaseOrder_num" placeholder="PO-1234567">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_purchaseOrderStatus" class="form-label me-4" >Purchase Order Status</label>
                            <input type="text" name="orderSummary_purchaseOrderStatus" id="" disabled class="form-control" placeholder="Pending">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="font-weight-bold" style="font-size:110%">Supplier Name: Pacifica Agrivet</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="font-weight-bold" style="font-size:110%">Contact Person: Juan dela Cruz</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="font-weight-bold" style="font-size:110%">Contact Number: 09123456789</p>
                        </div>
                    </div>
                    <div class="mb-2" style="width:100% height:90%; clear:left;">
                        <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:100%;">
                            <thead align="center">
                                <tr>
                                    <th scope="col">Supply ID</th>
                                    <th scope="col">Supply Name</th>
                                    <th scope="col">Supply Type</th>
                                    <th scope="col">Supply For</th>                                    
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Price per unit</th>
                                    <th scope="col">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody align="center" v-if="supplies">
                                <tr v-for="(supply, index) in getSummaryTable" :key="index">
                                    <td>{{ supply.id }}</td>                                    
                                    <td>{{ supply.supply_name }}</td>                                    
                                    <td>{{ supply.supply_type }}</td>                                    
                                    <td>{{ supply.supply_for }}</td>   
                                    <td>{{ supply.purchaseOrder_qty }}</td>                                 
                                    <td>{{ supply.purchaseOrder_unit }}</td>                                 
                                    <td>{{ supply.supply_initialPrice }}</td>                                    
                                    <td>{{ supply.purchaseOrder_subTotal }}</td>                             
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_totalAmount" class="form-label me-4" >Total Amount</label>
                            <input type="text" name="orderSummary_totalAmount" id="" class="form-control" placeholder="Php 7,500.00" disabled>
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_transactedBy" class="form-label me-4" >Transacted By</label>
                            <input type="text" name="orderSummary_transactedBy" id="" class="form-control" disabled v-model="orderSummary.transactedBy">
                        </div>
                    </div>
                    <div class="btn-toolbar pt-4" role="toolbar">
                        <div class="btn-group me-3">
                            <b-button variant="success" style="width:200px; height:60px" @click="triggerModal()" v-if="!orderSummary.paymentMethod">Select Payment Option</b-button>
                            <b-button variant="success" style="width:200px; height:60px" v-else>Proceed to Payment</b-button>
                            <b-modal id="paymentModal" title="Payment Options" size="lg">
                                <div class="form-row text-center">
                                    <div class="col">                                        
                                        <input class="form-check-input" type="checkbox" ref="bank" value="Bank" :checked="orderSummary.paymentType == 'Bank'" @change="setPaymentMethod($event)"/>Bank/Wire Transfer
                                    </div>
                                    <div class="col">
                                        <input class="form-check-input" type="checkbox" :checked="orderSummary.paymentType == 'Cash'" ref="cash" value="Cash" @change="setPaymentMethod($event)"/>Cash
                                    </div>
                                    <div class="col">
                                        <input class="form-check-input" type="checkbox" :checked="orderSummary.paymentType == 'Others'" ref="others" value="Others" @change="setPaymentMethod($event)"/>Others
                                    </div>
                                </div>
                                <div class="form-row" v-if="orderSummary.paymentType == 'Bank'">
                                        <label for="orderSummary_transactedBy" class="form-label me-4 mt-3" >Account Name</label>
                                        <input type="text" name="orderSummary_transactedBy" id="" class="form-control" v-model="orderSummary.paymentAccountName">
                                        <label for="orderSummary_transactedBy" class="form-label me-4 mt-3" >Banking Institution</label>
                                        <select class="form-select" @change="setBank($event)">
                                            <option selected value="None">Select Bank</option>
                                            <option value="BDO">BDO Unibank</option>
                                            <option value="BPI">BPI</option>
                                            <option value="LBP">LandBank</option>
                                        </select>
                                        <label for="orderSummary_transactedBy" class="form-label me-4 mt-3" >Account Name</label>
                                        <input type="text" name="orderSummary_transactedBy" id="" class="form-control" v-model="orderSummary.paymentAccountNum">
                                </div>
                                <div class="form-row text-center" v-if="orderSummary.paymentType == 'Cash'">
                                        <div class="col-md-12 mt-4 center-block text-center" style="font-size:larger">Proceed to Payment</div>  
                                </div>
                                <div class="form-row" v-if="orderSummary.paymentType == 'Others'">
                                        <label for="supplyOrder_SupplyType" class="form-label me-4 mt-3" >Choose Payment Option</label>
                                        <select class="form-select" @change="setWallet($event)">
                                            <option selected value="None">Select Payment Type</option>
                                            <option value="GCash">GCash</option>
                                            <option value="PayMaya/MAYA">PayMaya/MAYA</option>
                                        </select>
                                    <label for="orderSummary_transactedBy" class="form-label me-4 mt-3" >Account Name</label>
                                    <input type="text" name="orderSummary_transactedBy" id="" class="form-control" v-model="orderSummary.paymentOtherAccountName">
                                    <label for="orderSummary_transactedBy" class="form-label me-4 mt-3" >Account Number</label>
                                    <input type="text" name="orderSummary_transactedBy" id="" class="form-control" v-model="orderSummary.paymentOtherAccountNum">
                                </div>
                            </b-modal>
                        </div>
                        <div class="btn-group me-3">
                            <router-link to="/supplyOrder/payment"><b-button variant="success" style="width:200px; height:60px">Proceed to Payment</b-button></router-link>
                        </div>
                        <div class="btn-group me-3">
                            <router-link to="/supplyOrder/add"><b-button variant="secondary" style="width:200px; height:60px">Back to Supplier List</b-button></router-link>
                        </div>
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
    name: "PurchaseOrderSummary",
    created() {
        if(!this.$route.query.supplier_id || !this.$route.query.supply_id
        || !this.$route.query.purchaseOrder_num || !this.$route.query.purchaseOrder_status
        || !this.$route.query.purchaseOrder_qty || !this.$route.query.purchaseOrder_unit
        || !this.$route.query.purchaseOrder_subTotal){
            this.$router.push({ name: 'InitialPurchaseOrder' })
        } 
        this.formForAddPO() 
        .then(() => {
            this.orderSummary.transactedBy = this.getName        
            this.supplies = this.getFormPO.supplies
            this.readyApp()
        })         
    },
    beforeRouteEnter(to, from, next){
        if(from.name != 'InitialPurchaseOrder'){
            next({ name: 'InitialPurchaseOrder' })
        }
        else{
            next()
        }
    },
    data() {
        return {
            orderSummary: {
                transactedBy: null,
                paymentType: null,
                paymentAccountName: '',
                paymentAccountNum: '',
                paymentBankName: '',
                paymentOtherAccountName: '',
                paymentOtherAccountNum: '',
                paymentOtherWallet: '',
            },
            data: {
                supplier_id: this.$route.query.supplier_id,
                supply_id: this.$route.query.supply_id, 
                purchaseOrder_num: this.$route.query.purchaseOrder_num, 
                purchaseOrder_status: this.$route.query.purchaseOrder_status, 
                purchaseOrder_qty: this.$route.query.purchaseOrder_qty, 
                purchaseOrder_unit: this.$route.query.purchaseOrder_unit,
                purchaseOrder_subTotal: this.$route.query.purchaseOrder_subTotal,   
            },
            supplies: null,
            dateToday: format(new Date(), 'yyyy-MM-dd')
        }
    },
    watch: {
        'orderSummary.paymentType'(newVal){
            if(newVal == 'Bank'){
                this.$refs.cash.checked = false
                this.$refs.others.checked = false
                this.orderSummary.paymentOtherAccountName = null
                this.orderSummary.paymentOtherAccountNum = null
                this.orderSummary.paymentOtherWallet = 'None'
            }
            else if(newVal == 'Cash'){
                this.$refs.bank.checked = false
                this.$refs.others.checked = false
                this.orderSummary.paymentAccountName = null
                this.orderSummary.paymentAccountNum = null
                this.orderSummary.paymentOtherAccountName = null
                this.orderSummary.paymentOtherAccountNum = null
                this.orderSummary.paymentBankName = 'None'
                this.orderSummary.paymentOtherWallet = 'None'
            }
            else if(newVal == 'Others'){
                this.$refs.bank.checked = false
                this.$refs.cash.checked = false
                this.orderSummary.paymentAccountName = null
                this.orderSummary.paymentAccountNum = null
                this.orderSummary.paymentBankName = 'None'
            }            
        }
    },
    methods: {
        setPaymentMethod(e){   
            if(e.target.checked){
                this.orderSummary.paymentType = e.target.value
                    
            }   
            else{
                this.orderSummary.paymentType = null
            }                  
                   
        },
        setBank(e){
            this.orderSummary.paymentBankName = e.target.value
        },
        setWallet(e){
            this.orderSummary.paymentOtherWallet = e.target.value
        },
        triggerModal(){
            this.$bvModal.show("paymentModal")                                 
        },
        ...mapActions(['readyApp', 'formForAddPO'])
    },
    computed: {
        ...mapGetters(['getName', 'getFormPO']),
        getSummaryTable(){
            var items = []
            var item = null
            this.data.supply_id.forEach((s, i) => {
                var supplyObj = this.supplies.filter((ss) => {
                    return parseInt(ss.id) === parseInt(s)
                })
                item = {
                    id: s,
                    supply_name: supplyObj[0].supply_name,
                    supply_type: supplyObj[0].supply_type,
                    supply_for: supplyObj[0].supply_for,
                    supply_initialPrice: supplyObj[0].supply_initialPrice,
                    purchaseOrder_qty: this.data.purchaseOrder_qty[i],
                    purchaseOrder_unit: this.data.purchaseOrder_unit[i],
                    purchaseOrder_subTotal: this.data.purchaseOrder_subTotal[i],
                }
                items.push(item)
            })
            return items
        }
    }
}
</script>

<style>
 
</style>
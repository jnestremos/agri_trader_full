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
                            <input type="text" name="orderSummary_Date" id="" class="form-control" placeholder="09/05/2022">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_purchaseOrderNum" class="form-label me-4" >Purchase Order No.:</label>
                            <input type="text" name="orderSummary_purchaseOrderNum" id="" class="form-control" placeholder="PO-1234567">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_purchaseOrderStatus" class="form-label me-4" >Purchase Order Status</label>
                            <input type="text" name="orderSummary_purchaseOrderStatus" id="" class="form-control" placeholder="Pending">
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
                                    <th scope="col">Supply Type</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Price per unit</th>
                                    <th scope="col">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                <tr>
                                    <th>Fertilizer</th>
                                    <th>15</th>
                                    <th>Sack</th>
                                    <th>Php 500</th>
                                    <th>Php 7,500.00</th>
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
                            <input type="text" name="orderSummary_transactedBy" id="" class="form-control" v-model="orderSummary.transactedBy">
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
                                        <input class="form-check-input" type="checkbox" :checked="orderSummary.paymentType == 'Payment upon Delivery'" ref="paymentDelivery" value="Payment upon Delivery" @change="setPaymentMethod($event)"/>Payment upon Delivery
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
                                <div class="form-row text-center" v-if="orderSummary.paymentType == 'Payment upon Delivery'">
                                        <div class="col-md-12 mt-4 center-block text-center" style="font-size:larger">Refer to the Delivery Address of the Trader for Payment</div>  
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
export default {
    name: "PurchaseOrderSummary",
    data() {
        return {
            orderSummary: {
                transactedBy: '',
                paymentType: null,
                paymentAccountName: '',
                paymentAccountNum: '',
                paymentBankName: '',
                paymentOtherAccountName: '',
                paymentOtherAccountNum: '',
                paymentOtherWallet: '',
            }
        }
    },
    watch: {
        'orderSummary.paymentType'(newVal){
            if(newVal == 'Bank'){
                this.$refs.paymentDelivery.checked = false
                this.$refs.others.checked = false
                this.orderSummary.paymentOtherAccountName = null
                this.orderSummary.paymentOtherAccountNum = null
                this.orderSummary.paymentOtherWallet = 'None'
            }
            else if(newVal == 'Payment upon Delivery'){
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
                this.$refs.paymentDelivery.checked = false
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
        }
    },
}
</script>

<style>
 
</style>
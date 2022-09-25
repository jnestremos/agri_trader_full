<template>
        <div class="PurchaseOrderPayment">
            <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
                <h3>Supply Purchase Order | Payment</h3>        
            </div>
            <!-- <pre> {{ orderPayment }}</pre> -->
            <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
                <div style="width:85%; height:85%;" class="pb-5">
                    <form class="d-flex flex-column justify-content-between" @submit.prevent="">
                        <div class="form-row mb-2">
                            <div class="col-lg-2 me-2 mt-3">
                                <label for="supplyOrder_date" class="form-label me-4 font-weight-bold" style="font-size: large;">Date</label>
                                <input type="date" name="supplyOrder_Date" id="" class="form-control" v-model="orderPayment.date">
                            </div>
                            <div class="col-lg-3 me-4 mt-3">
                                <label for="supplyOrder_purchaseOrderNum" class="form-label me-4 font-weight-bold" style="font-size: large;" >Purchase Order No.:</label>
                                <input type="text" name="supplyOrder_purchaseOrderNum" id="" class="form-control" placeholder="PO-123456" disabled>
                            </div>
                        </div>
                        <div class="row mt-3 mb-3">
                            <label class="font-weight-bold" style="font-size:larger">Type of Payment</label>
                        </div>
                        <div class="form-row text-left mb-3">
                            <div class="col-lg-2 ms-3">
                                <input class="form-check-input" type="checkbox" :checked="orderPayment.paymentType == 'Partial'" value="Partial" ref="partial" @change="setPaymentType($event)"/>Partial / Downpayment
                            </div>
                            <div class="col-lg-2 ms-3">
                                <input class="form-check-input" type="checkbox" :checked="orderPayment.paymentType == 'Payment upon Receiving'" value="Payment upon Receiving" ref="uponReceiving" @change="setPaymentType($event)"/>Payment upon Receiving
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-md-2">
                                <label for="payment_totalPrice" class="form-label"> Total Price </label>
                                <input type="text" name="payment_totalPrice" id="" class="form-control" placeholder="7,500.00" disabled>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-lg-2">
                                <label for="payment_downpaymentAmount" class="form-label"> Downpayment Amount </label>
                                <input type="text" name="payment_totalPrice" id="" class="form-control" v-model="orderPayment.downpaymentAmount">
                            </div>
                            <div class="col-sm-2 mb-2">
                                <label for="payment_totalPrice" class="form-label"> Percentage </label>
                                <input type="number" name="payment_totalPrice" id="" class="form-control" v-model="orderPayment.downpaymentPercent">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-lg-2">
                                <label for="payment_downpaymentAmount" class="form-label"> Balance </label>
                                <input type="text" name="payment_totalPrice" id="" class="form-control" v-model="orderPayment.balance">
                            </div>
                        </div>
                        <div class="btn-toolbar pt-4" role="toolbar">
                            <div class="btn-group me-3 mt-3">
                                <router-link to="/supplyOrder/orderSummary"><button class="btn btn-secondary" style="width:200px; height: 60px;">Return to PO Summary</button></router-link>
                            </div>
                            <div class="btn-group me-3 mt-3">
                                <router-link to="/supplyOrder/statusDashboard"><button class="btn btn-success" style="width:200px; height: 60px;">Confirm Payment</button></router-link>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: "PurchaseOrderPayment",
    created() {
        this.readyApp()
    },
    data(){
        return{
            orderPayment: {
                date: '',
                downpaymentAmount: '',
                downpaymentPercent: '',
                paymentType: null,
                balance: '',
            }
        }
    },
    watch: {
        'orderPayment.paymentType'(newVal){
            if(newVal == 'Partial'){
                this.$refs.uponReceiving.checked = false
            }
            else if(newVal == 'Payment upon Receiving'){
                this.$refs.partial.checked = false
            }
        }
    },
    methods: {
        setPaymentType(e){
            if(e.target.checked){
                this.orderPayment.paymentType = e.target.value
            }
            else{
                this.orderPayment.paymentType = null;
            }
        },
        ...mapActions(['readyApp'])
    }
}
</script>

<style>

</style>
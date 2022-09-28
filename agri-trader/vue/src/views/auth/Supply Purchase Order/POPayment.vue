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
                                <input type="date" name="supplyOrder_Date" id="" class="form-control" v-model="dateToday" disabled>
                            </div>
                            <div class="col-lg-3 me-4 mt-3">
                                <label for="supplyOrder_purchaseOrderNum" class="form-label me-4 font-weight-bold" style="font-size: large;" >Purchase Order No.:</label>
                                <input type="text" name="supplyOrder_purchaseOrderNum" id="" class="form-control" placeholder="PO-123456" disabled v-model="data.purchaseOrder_num">
                            </div>
                        </div>
                        <div class="row mt-3 mb-3">
                            <label class="font-weight-bold" style="font-size:larger">Type of Payment</label>
                        </div>
                        <div class="form-row text-left mb-3">
                            <div class="col-lg-2 ms-3">
                                <input class="form-check-input" type="checkbox" :checked="data.purchaseOrder_paymentType == 'Partial'" value="Partial" ref="partial" @change="setPaymentType($event)"/>Partial / Downpayment
                            </div>
                            <div class="col-lg-2 ms-3">
                                <input class="form-check-input" type="checkbox" :checked="data.purchaseOrder_paymentType == 'Full'" value="Full" ref="full" @change="setPaymentType($event)"/>Full Payment
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-md-2">
                                <label for="payment_totalPrice" class="form-label"> Total Price </label>
                                <input type="text" name="payment_totalPrice" id="" class="form-control" placeholder="7,500.00" disabled v-model="data.purchaseOrder_totalBalance">
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-lg-2">
                                <label for="payment_downpaymentAmount" class="form-label"> Downpayment Amount </label>
                                <input type="number" name="payment_totalPrice" id="" class="form-control" :disabled="!(data.purchaseOrder_paymentType == 'Partial')" v-model="data.purchaseOrder_dpAmount">
                            </div>
                            <div class="col-sm-2 mb-2">
                                <label for="payment_totalPrice" class="form-label"> Percentage </label>
                                <input type="number" name="payment_totalPrice" id="" class="form-control" :disabled="!(data.purchaseOrder_paymentType == 'Partial')" v-model="data.purchaseOrder_percentage" step=".1">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-lg-2">
                                <label for="payment_downpaymentAmount" class="form-label"> Balance </label>
                                <input type="text" name="payment_totalPrice" id="" class="form-control" v-model="data.purchaseOrder_balance" disabled>
                            </div>
                        </div>
                        <div class="btn-toolbar pt-4" role="toolbar">
                            <div class="btn-group me-3 mt-3">
                                <button class="btn btn-secondary" style="width:200px; height: 60px;" @click="backToPO()">Return to PO Summary</button>
                            </div>
                            <div class="btn-group me-3 mt-3">
                                <button class="btn btn-success" :disabled="validateData" @click="sendPayment()" style="width:200px; height: 60px;">Confirm Payment</button>
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
    name: "PurchaseOrderPayment",
    created() {    
        if(!this.getPO.supply_id){
            this.$router.push({ name: 'InitialPurchaseOrder' })
        }
        else{
            this.data.supplier_id = this.getPO.supplier_id,
            this.data.supply_id = this.getPO.supply_id, 
            this.data.purchaseOrder_num = this.getPO.purchaseOrder_num, 
            this.data.purchaseOrder_status = this.getPO.purchaseOrder_status, 
            this.data.purchaseOrder_qty = this.getPO.purchaseOrder_qty, 
            this.data.purchaseOrder_unit = this.getPO.purchaseOrder_unit,
            this.data.purchaseOrder_subTotal = this.getPO.purchaseOrder_subTotal, 
            this.data.purchaseOrder_totalBalance = this.getPO.purchaseOrder_totalBalance,
            this.data.purchaseOrder_paymentMethod= this.getPO.purchaseOrder_paymentMethod,
            this.data.purchaseOrder_bankName= this.getPO.purchaseOrder_bankName,
            this.data.purchaseOrder_accNum= this.getPO.purchaseOrder_accNum,
            this.data.purchaseOrder_accName= this.getPO.purchaseOrder_accName, 
            this.readyApp()
        }    

    },
    data(){
        return{
            orderPayment: {
                date: '',
                downpaymentAmount: '',
                downpaymentPercent: '',
                paymentType: null,
                balance: '',
            },
            data: {
                supplier_id: null,
                supply_id: null, 
                purchaseOrder_num: null, 
                purchaseOrder_status: null, 
                purchaseOrder_qty: null, 
                purchaseOrder_unit: null,
                purchaseOrder_subTotal: null,
                purchaseOrder_totalBalance: null,
                purchaseOrder_paymentMethod: null,
                purchaseOrder_bankName: null,
                purchaseOrder_accNum: null,
                purchaseOrder_accName: null, 
                purchaseOrder_dpAmount: null,
                purchaseOrder_percentage: null,
                purchaseOrder_balance: null,
                purchaseOrder_paymentType: null,
            },
            dateToday: format(new Date(), 'yyyy-MM-dd'),
        }
    },
    watch: {
        'data.purchaseOrder_paymentType'(newVal){
            if(newVal == 'Partial'){
                this.data.purchaseOrder_percentage = 0.5
                this.data.purchaseOrder_dpAmount = this.data.purchaseOrder_totalBalance * 0.5
                this.$refs.full.checked = false
            }
            else if(newVal == 'Full'){
                this.data.purchaseOrder_percentage = 1.0
                this.data.purchaseOrder_dpAmount = this.data.purchaseOrder_totalBalance                
                this.$refs.partial.checked = false
            }
            this.data.purchaseOrder_balance = 0
        },
        'data.purchaseOrder_dpAmount'(newVal){
            newVal = Math.abs(newVal)
            if(newVal > this.data.purchaseOrder_totalBalance){
                this.data.purchaseOrder_dpAmount = parseFloat(this.data.purchaseOrder_totalBalance * 0.5)
                this.data.purchaseOrder_balance = this.data.purchaseOrder_totalBalance - newVal
            }
            else{
                this.data.purchaseOrder_percentage = parseFloat(newVal / this.data.purchaseOrder_totalBalance).toFixed(2)
                this.data.purchaseOrder_balance = this.data.purchaseOrder_totalBalance - newVal
            }
        },
        'data.purchaseOrder_percentage'(newVal){
            newVal = Math.abs(newVal)
            if(newVal > 1){
                newVal = 0.5
                this.data.purchaseOrder_dpAmount = parseFloat(this.data.purchaseOrder_totalBalance * newVal)
                this.data.purchaseOrder_balance = this.data.purchaseOrder_totalBalance - this.data.purchaseOrder_dpAmount
            }
            else{
                this.data.purchaseOrder_dpAmount = parseFloat(this.data.purchaseOrder_totalBalance * newVal)
                this.data.purchaseOrder_balance = this.data.purchaseOrder_totalBalance - this.data.purchaseOrder_dpAmount
            }        
        }
    },
    methods: {
        setPaymentType(e){
            if(e.target.checked){
                this.data.purchaseOrder_paymentType = e.target.value
            }
            else{
                this.data.purchaseOrder_paymentType = null;
            }
        },    
        sendPayment(){
            this.addPO(this.data)
            .then(() => {
                this.$toastr.s('Purchase Order Sent Successfully!')
                setTimeout(() => {
                    this.$router.push({ name: 'InitialPurchaseOrder' })
                }, 5000)
            })
            .catch((err) => {
                console.error(err)
            })
        },  
        backToPO(){
            delete this.data.purchaseOrder_dpAmount
            delete this.data.purchaseOrder_balance
            delete this.data.purchaseOrder_paymentType
            delete this.data.purchaseOrder_percentage
            this.initPO(this.data)
            this.$router.push({ name: 'PurchaseOrderSummary' })
        }, 
        ...mapActions(['readyApp', 'initPO', 'addPO'])
    },
    computed:{
        ...mapGetters(['getPO']),
        validateData(){
            if((this.data.purchaseOrder_paymentType && this.data.purchaseOrder_totalBalance
            && this.data.purchaseOrder_dpAmount && this.data.purchaseOrder_percentage
            && this.data.purchaseOrder_balance) 
            || (this.data.purchaseOrder_paymentType == 'Full' && this.data.purchaseOrder_dpAmount > 0)
            && this.data.purchaseOrder_percentage > 0){
                return false
            }
            else{
                return true
            }
        }
    }
}
</script>

<style>

</style>
<template>
  <div class="refundDetails">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Refund</h3>
    </div>
    <div class="container-fluid w-100 p-3 m-0" style="height:90%; position:relative">
        <div class="d-flex justify-content-between align-items-baseline w-100">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Status:</h5>
                <p v-if="getOrder.bidOrder">{{ getStatus() }}</p>                
            </div>
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Negotiated Price:</h5>
                <h5 class="me-3">Php</h5>
                <h5 v-if="getOrder.bidOrder" class="me-3">{{ getOrder.bidOrder.order_negotiatedPrice.toFixed(2) }}</h5>                
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Bid:</h5>
                <p v-if="getOrder.contract">{{ getOrder.contract.farm_name }} {{ getOrder.on_hand_bid ? 'On-Hand' : 'Project' }}</p>                
            </div>
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">First Payment:</h5>
                <h5 class="me-3">Php</h5>
                <h5 v-if="getOrder.bidOrder" class="me-3">{{ getOrder.bidOrder.order_dpAmount.toFixed(2) }}</h5>                
                <h5 v-if="getOrder.bidOrder" class="me-3">{{ getPercentage + '%' }}</h5>                
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Distributor:</h5>
                <p v-if="getOrder.distributor">{{ getOrder.distributor.distributor_firstName + ' ' + getOrder.distributor.distributor_lastName }}</p>                
            </div>
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Date of First Payment:</h5>        
                <h5 v-if="getOrder.bidOrder" class="me-3">{{ getOrder.bidOrder.order_dpDueDate }}</h5>                
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Produce:</h5>
                <p v-if="getOrder.produce">{{ getOrder.produce.prod_name + ' ' + getOrder.produce.prod_type }} {{ getOrder.bidOrder.order_grade ? 'Class ' + getOrder.bidOrder.order_grade : '' }}</p>
            </div>
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Total (Original):</h5>        
                <picture v-if="getOrder.project_bid || getOrder.on_hand_bid" class="me-3">{{ getOrder.project_bid ? getOrder.project_bid.project_bid_total : getOrder.on_hand_bid.on_hand_bid_total.toFixed(2) }}</picture>                
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Dist #:</h5>
                <p v-if="getOrder.distributor">{{ getOrder.distributor_contactNum[0].distributor_contactNum }}</p>
            </div>
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Total (Updated):</h5>        
                <p v-if="getOrder.project_bid ||  getOrder.on_hand_bid">{{ getTotal }}</p>                
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Trader's Price:</h5>
                <p v-if="getOrder.contract">{{ getOrder.bidOrder.order_traderPrice.toFixed(2) }}</p>                
            </div>            
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Payment Method:</h5>
                <select v-if="getOrder.bid_order_acc && getOrder.bid_order_acc.bid_order_acc_type != 'Refund'" name="" id="" class="form-select" style="width:150px" @change="setPaymentMethod($event)">
                    <option value="Cash" selected>Cash</option>
                    <option value="Bank">Bank</option>
                </select>
                <p v-else>{{ getOrder.bid_order_acc.bid_order_acc_paymentMethod }}</p>
            </div>            
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Asking Price:</h5>
                <p v-if="getOrder.bidOrder">{{ getOrder.bidOrder.order_initialPrice ? getOrder.bidOrder.order_initialPrice.toFixed(2) : '' }}</p>                
            </div>            
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Account Name:</h5>
                <input v-if="getOrder.bid_order_acc && getOrder.bid_order_acc.bid_order_acc_type != 'Refund'" type="text" class="form-control" style="width:150px" v-model="data.bid_order_acc_accName">
                <p v-else>{{ getOrder.bid_order_acc.bid_order_acc_accName }}</p>
            </div>                                   
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Estimated Harvest Qty:</h5>
                <p v-if="getOrder.contract">{{ getOrder.contract.contract_estimatedHarvest ? getOrder.contract.contract_estimatedHarvest.toFixed(2) + ' kgs' : ''}}</p>
            </div>
            <div v-if="data.bid_order_acc_paymentMethod == 'Cash'" class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Amount:</h5>
                <h5 class="me-3">Php</h5>
                <h5 v-if="getOrder.refund" class="me-3">{{ getOrder.refund.refund_amount.toFixed(2) }}</h5>                
                <h5 v-if="getOrder.refund" class="me-3">{{ getOrder.refund.refund_percentage * 100 + '%' }}</h5> 
            </div>
            <div v-else class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Bank Name:</h5>
                <input type="text" class="form-control" style="width:150px" v-model="data.bid_order_acc_bankName">
            </div>            
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Quantity Needed:</h5>
                <p v-if="getOrder.project_bid || getOrder.on_hand_bid">{{ getOrder.project_bid ? getOrder.project_bid.project_bid_minQty.toFixed(2) + ' - ' + getOrder.project_bid.project_bid_maxQty.toFixed(2) + ' kg/s' : getOrder.on_hand_bid.on_hand_bid_qty.toFixed(2) + ' kg/s' }}</p>            
            </div>
            <div v-if="data.bid_order_acc_paymentMethod == 'Bank'" class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Account #:</h5>
                <input type="text" class="form-control" style="width:150px" v-model="data.bid_order_acc_accNum">
            </div>
            <div v-else class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Date Paid:</h5>
                <input v-if="getOrder.bid_order_acc && getOrder.bid_order_acc.bid_order_acc_type != 'Refund'" type="date" class="form-control" style="width:150px" v-model="data.bid_order_acc_datePaid">
                <p v-else>{{ getOrder.bid_order_acc.bid_order_acc_datePaid }}</p>
            </div>       
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Date Placed:</h5>
                <p v-if="getOrder.refund">{{ getOrder.refund.created_at.split('T')[0] }}</p>                
            </div> 
            <div v-if="data.bid_order_acc_paymentMethod == 'Bank'" class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Amount:</h5>
                <h5 class="me-3">Php</h5>
                <h5 v-if="getOrder.bidOrder" class="me-3">{{ getOrder.bidOrder.order_dpAmount.toFixed(2) }}</h5>                
                <h5 v-if="getOrder.bidOrder" class="me-3">{{ getPercentage + '%' }}</h5> 
            </div>           
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">                
                <h5 class="me-3">{{ getOrder.project_bid ? 'Expected Date of Harvest:' : 'Last Date of Harvest:' }}</h5>
                <p v-if="getOrder.project || getOrder.produce_yield">{{ getOrder.project_bid ? getProgressDate : getOrder.produce_yield.produce_yield_dateHarvestTo }}</p>                
            </div>
            <div v-if="data.bid_order_acc_paymentMethod == 'Bank'" class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Date Paid:</h5>
                <input type="date" class="form-control" style="width:150px" v-model="data.bid_order_acc_datePaid">
            </div>            
        </div>
        <div class="d-flex justify-content-between align-items-baseline w-100 mt-4">
            <div class="d-flex align-items-baseline w-50">
                <h5 class="me-3">Expected Dates Needed:</h5>
                <p v-if="getOrder.bidOrder">{{ getOrder.bidOrder.order_dateNeededFrom + ' - ' + getOrder.bidOrder.order_dateNeededTo }}</p>                
            </div>            
        </div>
        <button @click="refundApprove()" v-if="getOrder.bidOrder && getOrder.bidOrder.bid_order_status_id == 7 && getOrder.bid_order_acc.bid_order_acc_type != 'Refund'" class="btn btn-success" style="position:absolute; bottom:3%; right:10%;">Approve Refund</button>        
    </div> 
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { add, format } from 'date-fns'
export default {
    name: "RefundDetails",
    created(){
        this.fetchOrder(this.$route.params.id)
        .then(() => {
            if(this.getOrder.bidOrder.order_negotiatedPrice){
                this.data.order_negotiatedPrice = this.getOrder.bidOrder.order_negotiatedPrice.toFixed(2)
            }
            else{
                this.data.order_negotiatedPrice = this.getOrder.bidOrder.order_initialPrice.toFixed(2)
            }
            this.data.bid_order_acc_amount = this.getOrder.refund.refund_amount.toFixed(2)
            this.data.id = this.getOrder.bidOrder.id
            this.readyApp()
        })
        .catch((err) => {
            console.log(err)
            this.$router.push({ name: 'AllBidOrders' })
        })        
    },
    data(){
        return {
            errors: null,
            data: {
                id: null,
                order_negotiatedPrice: null,
                bid_order_acc_paymentMethod: 'Cash',
                bid_order_acc_type: 'Refund',
                bid_order_acc_amount: null,
                bid_order_acc_bankName: null,
                bid_order_acc_accNum: null,
                bid_order_acc_accName: null,
                bid_order_acc_remarks: null,
                bid_order_acc_datePaid: new Date().toISOString().split('T')[0]
            }
            // test again from the start to check flow
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchOrder', 'approveRefund']),
        getStatus(){
            if(this.getOrder.bidOrder.bid_order_status_id == 1){                
                return 'For Approval'
            }
            else if(this.getOrder.bidOrder.bid_order_status_id == 2){
                return 'To Be Confirmed by Distributor'
            }
            else if(this.getOrder.bidOrder.bid_order_status_id == 3){
                return 'First Payment Pending'
            }
            else if(this.getOrder.bidOrder.bid_order_status_id == 4){
                return 'First Payment Paid'
            }
            else if(this.getOrder.bidOrder.bid_order_status_id == 5){
                return 'To be Delivered'
            }
            else if(this.getOrder.bidOrder.bid_order_status_id == 6){
                return 'Confirm Final Payment'
            }
            else if(this.getOrder.bidOrder.bid_order_status_id == 7){
                return 'Return for Refund Pending'
            }
            else if(this.getOrder.bidOrder.bid_order_status_id == 8){
                return 'Confirmation for Refund'
            }            
        },
        setPaymentMethod(e){
            this.data.bid_order_acc_paymentMethod = e.target.value
            this.data.bid_order_acc_bankName = null
            this.data.bid_order_acc_accNum = null
        },
        refundApprove(){
            this.approveRefund(this.data)
            .then(() => {
                this.$toastr.s('Refund Approved Successfully!')
                setTimeout(() => {                    
                    this.$router.push({ name: 'AllBidOrders' })
                }, 5000)
                
            })
            .catch((err) => {
                this.errors = err.response.data.errors
                for(var error in this.errors){
                    this.$toastr.e(this.errors[error][0])
                }                
            })
        }
    },
    computed: {
        ...mapGetters(['getOrder']),
        getTotal(){
            var total = null;
            if(this.getOrder.project_bid){
                total = (this.data.order_negotiatedPrice * this.getOrder.project_bid.project_bid_minQty).toFixed(2) + ' - ' + (this.data.order_negotiatedPrice * this.getOrder.project_bid.project_bid_maxQty).toFixed(2)
                return total
            }
            else{
                total = (this.data.order_negotiatedPrice * this.getOrder.on_hand_bid.on_hand_bid_qty).toFixed(2)                
                return total
            } 
        },
        getProgressDate(){
            if(!this.getOrder.project.project_harvestableEnd){
                var harvestDate = add(new Date(this.getOrder.project.project_commenceDate), {
                    weeks: 1
                })
                var formattedDate = format(harvestDate, 'yyyy-MM-dd');       
                return formattedDate                    
            }
            else{
                return this.getOrder.project.project_harvestableEnd
            }
        },
        getPercentage() {
            var total = null;
            if(this.getOrder.project_bid){
                total = (this.getOrder.project_bid.project_bid_maxQty * this.data.order_negotiatedPrice).toFixed(2)
            }
            else{
                total = (this.getOrder.on_hand_bid.on_hand_bid_qty * this.data.order_negotiatedPrice).toFixed(2)
            }
            return parseFloat((parseFloat(this.getOrder.bidOrder.order_dpAmount) / total) * 100)
        },
    }
}
</script>

<style>

</style>
<template>
  <div class="bidOrderDetails">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Bidding Confirmation</h3>
    </div>        
    <form @submit.prevent="sendOrder()" action="" class="container-fluid w-100 d-flex p-1 m-0" style="height:90%; position:relative">      
        <div class="container-fluid m-0 p-3 d-flex flex-column h-100 justify-content-between" style="width:50%;">
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Status:</h5>
                <p v-if="getOrder.bidOrder">{{ getStatus() }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Bid:</h5>
                <p v-if="getOrder.contract">{{ getOrder.contract.farm_name }} {{ getOrder.on_hand_bid ? 'On-Hand' : 'Project' }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Distributor:</h5>
                <p v-if="getOrder.distributor">{{ getOrder.distributor.distributor_firstName + ' ' + getOrder.distributor.distributor_lastName }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Produce:</h5>
                <p v-if="getOrder.produce">{{ getOrder.produce.prod_name + ' ' + getOrder.produce.prod_type }} {{ getOrder.bidOrder.order_grade ? 'Class ' + getOrder.bidOrder.order_grade : '' }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Dist #:</h5>
                <p v-if="getOrder.distributor">{{ getOrder.distributor_contactNum[0].distributor_contactNum }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Trader's Price:</h5>
                <p v-if="getOrder.contract">{{ getOrder.bidOrder.order_traderPrice.toFixed(2) }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Asking Price:</h5>
                <p v-if="getOrder.bidOrder">{{ getOrder.bidOrder.order_initialPrice ? getOrder.bidOrder.order_initialPrice.toFixed(2) : '' }}</p>
            </div>
            <div class="d-flex align-items-baseline" v-if="getOrder.project_bid">
                <h5 class="me-3">Estimated Harvest Qty:</h5>
                <p v-if="getOrder.contract">{{ getOrder.contract.contract_estimatedHarvest ? getOrder.contract.contract_estimatedHarvest.toFixed(2) + ' kgs' : ''}}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Quantity Needed:</h5>
                <p v-if="getOrder.project_bid || getOrder.on_hand_bid">{{ getOrder.project_bid ? getOrder.project_bid.project_bid_minQty.toFixed(2) + ' - ' + getOrder.project_bid.project_bid_maxQty.toFixed(2) + ' kg/s' : getOrder.on_hand_bid.on_hand_bid_qty.toFixed(2) + ' kg/s' }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Date Placed:</h5>
                <p v-if="getOrder.bidOrder">{{ getOrder.bidOrder.created_at.split('T')[0] }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">{{ getOrder.project_bid ? 'Expected Date of Harvest:' : 'Last Date of Harvest:' }}</h5>
                <p v-if="getOrder.project || getOrder.produce_yield">{{ getOrder.project_bid ? getProgressDate : getOrder.produce_yield.produce_yield_dateHarvestTo }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Expected Dates Needed:</h5>
                <p v-if="getOrder.bidOrder">{{ getOrder.bidOrder.order_dateNeededFrom + ' - ' + getOrder.bidOrder.order_dateNeededTo }}</p>
            </div>
        </div>        
        <div class="container-fluid m-0 p-3 d-flex flex-column justify-content-between" style="width:50%; height:49%;">
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Negotiated Price:</h5>
                <h5 class="me-3">Php</h5>
                <input type="number" v-if="bid_order_status_id == 1" class="form-control" style="width:150px;" min="0" v-model="data.order_negotiatedPrice" @keyup="resetAmountPercentage($event)" onkeydown="return false" @change="resetAmountPercentage($event)">
                <h5 v-else-if="getOrder.bidOrder" class="me-3">{{ getOrder.bidOrder.order_negotiatedPrice ?  getOrder.bidOrder.order_negotiatedPrice.toFixed(2) : '' }}</h5>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">First Payment:</h5>
                <h5 class="me-3">Php</h5>                
                <input v-if="(getOrder.project_bid || getOrder.on_hand_bid) && bid_order_status_id == 1" id="amount" type="number" class="form-control me-3" style="width:150px;" step="0.5" v-model="data.order_dpAmount" onkeydown="return false" @keyup="validateAmount($event)">
                <h5 v-else-if="getOrder.bidOrder" class="me-3">{{ getOrder.bidOrder.order_dpAmount ? getOrder.bidOrder.order_dpAmount.toFixed(2) : '' }}</h5>                
                <input v-if="bid_order_status_id == 1" type="number" class="form-control" id="percentage" style="width:150px;" min="0" max="100" value="50.00" @keyup="validatePercent($event)" onkeydown="return false">
                <h5 v-else-if="getOrder.bidOrder" class="me-3">{{ getPercentage + '%' }}</h5>
            </div>
            <div class="d-flex align-items-baseline" v-if="getOrder.bidOrder">
                <h5 class="me-3">{{ 'Due Date of First Payment:' }}</h5>                                
                <p>{{ getOrder.bidOrder.order_dpDueDate ? getOrder.bidOrder.order_dpDueDate : data.order_dpDueDate }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Total (Original):</h5>
                <p v-if="getOrder.project_bid || getOrder.on_hand_bid">{{ getOrder.project_bid ? getOrder.project_bid.project_bid_total : getOrder.on_hand_bid.on_hand_bid_total.toFixed(2) }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Total (Updated):</h5>
                <p v-if="getOrder.project_bid ||  getOrder.on_hand_bid">{{ getTotal }}</p>
                <input v-if="getOrder.project_bid" type="text" name="" id="" hidden v-model="data.project_bid_total">
                <input v-else type="text" name="" id="" hidden v-model="data.on_hand_bid_total">
            </div>
            <div class="d-flex align-items-baseline" v-if="getOrder.bidOrder && getOrder.bidOrder.order_finalPrice">
                <h5 class="me-3">Final Negotiated Price:</h5>
                <p>Php {{ getOrder.bidOrder.order_finalPrice.toFixed(2) }}</p>            
            </div>
            <div class="d-flex align-items-baseline" v-if="getOrder.bidOrder && getOrder.bidOrder.order_finalPrice">
                <h5 class="me-3">Quantity to be Given:</h5>
                <p>{{ getOrder.bidOrder.order_finalQty }} kgs</p>            
            </div>
            <div v-if="bid_order_status_id > 2">
                <div class="d-flex align-items-baseline" v-if="getOrder.bid_order_acc && getAccName && getOrder.bid_order_acc.bid_order_acc_paymentMethod == 'Bank'">
                    <h5 class="me-3">Bank Name:</h5>
                    <p>{{ getOrder.bid_order_acc.bid_order_acc_bankName }}</p>                
                </div>               
                <div class="d-flex align-items-baseline" v-if="getOrder.bid_order_acc && getAccName && getOrder.bid_order_acc.bid_order_acc_paymentMethod == 'Bank'">
                    <h5 class="me-3">Account Number:</h5>
                    <p>{{ getOrder.bid_order_acc.bid_order_acc_accNum }}</p>                
                </div>               
                <div class="d-flex align-items-baseline" v-if="getOrder.bid_order_acc && getAccName">
                    <h5 class="me-3">Payment Transacted By:</h5>
                    <p>{{ getAccName }}</p>                
                </div>               
                <div class="d-flex align-items-baseline" v-if="getOrder.bid_order_acc && getAccName">
                    <h5 class="me-3">Payment Type:</h5>
                    <p>{{ getOrder.bid_order_acc.bid_order_acc_type }}</p>                
                </div>
                <div class="d-flex align-items-baseline" v-if="getOrder.bid_order_acc && getAccName">
                    <h5 class="me-3">Payment Method:</h5>
                    <p>{{ getOrder.bid_order_acc.bid_order_acc_paymentMethod }}</p>                
                </div>
                <div class="d-flex align-items-baseline" v-if="getOrder.bid_order_acc && getAccName" >
                    <h5 class="me-3">Account:</h5>
                    <p style="word-spacing:15px">{{ getOrder.bid_order_acc.bid_order_acc_amount + " " }} {{ bid_order_status_id != 5 ? getPercentage + '%' : '' }}</p>                
                </div>
                <div class="d-flex align-items-baseline" v-if="getOrder.bid_order_acc && getAccName">
                    <h5 class="me-3">Date Paid:</h5>
                    <p>{{ getOrder.bid_order_acc.bid_order_acc_datePaid }}</p>                
                </div>                
            </div>
            <!-- <div clas s="d-flex align-items-baseline">
                <h5 class="me-3">Name of Trader:</h5>
                <p>Joshua Estremos</p>
            </div> -->
        </div> 
        <input v-if="(bid_order_status_id == 1 ||  bid_order_status_id == 3 || bid_order_status_id == 6)" type="submit" class="btn btn-success" :value="bid_order_status_id == 1 ? 'Post' : bid_order_status_id == 3 || bid_order_status_id == 6 ? 'Confirm' : ''" style="position:absolute; bottom:3%; right:10%;">               
        <div style="position:absolute; bottom:3%; right:10%;">
            <router-link v-if="getOrder.refund" :to="`/bid/orders/${$route.params.id}/refund`"><button :class="getOrder.on_hand_bid ? 'btn btn-success me-2' : 'btn btn-success'">See Refund Form</button></router-link>
            <button v-if="getOrder.on_hand_bid && bid_order_status_id == 4" class="btn btn-success" @click="$router.push({ path: `/delivery/${getOrder.bidOrder.id}`, 
            query: {
                finalPrice: data.order_finalPrice,
                finalQty: data.order_finalQty,
                produce: data.produce_trader_id
            }})">Deliver
            </button> 
        </div>

    </form>
        
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { add, format } from 'date-fns'
export default {
    name: 'BidOrderDetails',
    created(){
        this.fetchOrder(this.$route.params.id)
        .then(() => {      
            this.bid_order_status_id = this.getOrder.bidOrder.bid_order_status_id  
            if(this.getOrder.bidOrder.order_negotiatedPrice){
                this.data.order_negotiatedPrice = this.getOrder.bidOrder.order_negotiatedPrice.toFixed(2)
            }
            else{
                this.data.order_negotiatedPrice = this.getOrder.bidOrder.order_initialPrice.toFixed(2)
            }
            this.data.order_dpDueDate = this.getOrder.bidOrder.order_dpDueDate                        
            this.data.order_datePlaced = this.getOrder.bidOrder.created_at
            var total = null;
            var totall = null;
            if(this.getOrder.project_bid){
                total = (this.data.order_negotiatedPrice * this.getOrder.project_bid.project_bid_minQty).toFixed(2) + ' - ' + (this.data.order_negotiatedPrice * this.getOrder.project_bid.project_bid_maxQty).toFixed(2)                
                totall = (this.getOrder.project_bid.project_bid_maxQty * this.data.order_negotiatedPrice).toFixed(2)
                this.data.project_bid_total = total
            }
            else{
                total = (this.data.order_negotiatedPrice * this.getOrder.on_hand_bid.on_hand_bid_qty).toFixed(2)  
                totall = (this.getOrder.on_hand_bid.on_hand_bid_qty * this.data.order_negotiatedPrice).toFixed(2)
                this.data.on_hand_bid_total = total
                if(this.bid_order_status_id == 4){
                    this.data.order_finalPrice = this.data.order_negotiatedPrice
                    this.data.order_finalQty = this.getOrder.on_hand_bid.on_hand_bid_qty
                    this.data.produce_trader_id = this.getOrder.bidOrder.produce_trader_id
                }
            }                              
            this.data.order_dpAmount = parseFloat(totall - (totall * 0.5)).toFixed(2)     
            var dpDueDate = add(new Date(), {
                weeks: 2
            })
            var formattedDate = format(dpDueDate, 'yyyy-MM-dd')
            this.data.order_dpDueDate = formattedDate
            this.readyApp()
        })
        .catch((err) => {
            console.log(err)
            this.$router.push({ name: 'AllBidOrders' })
        })
    },
    methods:{
        ...mapActions([
            'readyApp', 
            'fetchOrder', 
            'approveProjectBid',
            'approveOnHandBid',
            'approveFirstPayment', 
            'approveFinalPayment',
            
        ]),
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
        validateAmount(e){           
            var total = null;
            if(this.getOrder.project_bid){                
                total = (this.getOrder.project_bid.project_bid_maxQty * this.data.order_negotiatedPrice).toFixed(2)
            }
            else{
                total = (this.getOrder.on_hand_bid.on_hand_bid_qty * this.data.order_negotiatedPrice).toFixed(2)
            } 
            var percentage = document.getElementById('percentage')                        
            if(parseFloat(e.target.value) <= 0 || e.target.value == ''){                               
                e.target.value = (total - (total * 0.5)).toFixed(2)
                // this.data.on_hand_bid_total = e.target.value
                percentage.value = 50.00
            }
            else if(parseFloat((parseFloat(e.target.value) / total) * 100).toFixed(2) > 100){                 
                e.target.value = parseFloat(total).toFixed(2)
                // this.data.on_hand_bid_total = e.target.value
                percentage.value = '100.00'                
            }
            else{                                           
                percentage.value = parseFloat((parseFloat(e.target.value) / total) * 100).toFixed(2)                                
            }
            this.data.order_dpAmount = parseFloat(e.target.value).toFixed(2)
               
        },
        validatePercent(e){        
            var total = null;
            if(this.getOrder.project_bid){
                total = (this.getOrder.project_bid.project_bid_maxQty * this.data.order_negotiatedPrice).toFixed(2)
            }
            else{
                total = (this.getOrder.on_hand_bid.on_hand_bid_qty * this.data.order_negotiatedPrice).toFixed(2)
            }
            var amount = document.getElementById('amount')
            if(parseFloat(e.target.value) <= 0 || e.target.value == ''){                
                e.target.value = '50.00'
                amount.value = (total - (total * 0.5)).toFixed(2)
            }
            else if(parseFloat(e.target.value) > 100){
                e.target.value = '100.00'
                amount.value = parseFloat(total).toFixed(2)
            }
            else{                                                            
                amount.value = parseFloat(total) - parseFloat(parseFloat(total - (total * parseFloat(e.target.value / 100)))).toFixed(2)                                
            } 
            this.data.order_dpAmount = parseFloat(amount.value).toFixed(2)           
        },
        resetAmountPercentage(e){                       
            var total = null;
            var totall = null;
            if(parseFloat(e.target.value) <= 0 || e.target.value == ''){
                this.data.order_negotiatedPrice = this.getOrder.bidOrder.order_initialPrice.toFixed(2)
            } 
            if(this.getOrder.project_bid){
                total = (this.getOrder.project_bid.project_bid_maxQty * this.data.order_negotiatedPrice).toFixed(2)
                totall =(this.getOrder.project_bid.project_bid_minQty * this.data.order_negotiatedPrice).toFixed(2)
                this.data.project_bid_total = totall + ' - ' + total
            }
            else{
                total = (this.getOrder.on_hand_bid.on_hand_bid_qty * this.data.order_negotiatedPrice).toFixed(2)
                this.data.on_hand_bid_total = total
            }
            var amount = document.getElementById('amount')                   
            var percentage = document.getElementById('percentage')                                      
            amount.value = (total - (total * 0.5)).toFixed(2)
            percentage.value = '50.00'
            this.data.order_dpAmount = parseFloat(amount.value).toFixed(2)                                                    
        },         
        sendOrder(){
            if(this.bid_order_status_id == 3){
                this.approveFirstPayment(this.$route.params.id)
                .then(() => {
                    this.$router.push({ name: 'AllBidOrders' })
                })
                .catch((err) => {
                    this.errors = err.response.data.errors
                    for(var error in this.errors){
                        this.$toastr.e(this.errors[error][0])
                    }                
                })                
            }
            else if(this.bid_order_status_id == 1){
                if(this.getOrder.project_bid){
                    this.approveProjectBid(this.data)
                    .then(() => {
                        this.$router.push({ name: 'AllBidOrders' })
                    })
                    .catch((err) => {
                        this.errors = err.response.data.errors
                        for(var error in this.errors){
                            this.$toastr.e(this.errors[error][0])
                        }                
                    })
                }
                else{
                    this.approveOnHandBid(this.data)
                    .then(() => {
                        this.$router.push({ name: 'AllBidOrders' })
                    })
                    .catch((err) => {
                        this.errors = err.response.data.errors
                        for(var error in this.errors){
                            this.$toastr.e(this.errors[error][0])
                        }                
                    })
                }
            }
            else if(this.bid_order_status_id == 6){
                this.approveFinalPayment(this.$route.params.id)
                .then(() => {
                    this.$router.push({ name: 'AllBidOrders' })
                })
                .catch((err) => {
                    console.error(err)
                    this.errors = err.response.data.errors
                    for(var error in this.errors){
                        this.$toastr.e(this.errors[error][0])
                    }                
                })
            }
        }        
    },
    computed: {
        ...mapGetters(['getOrder']),
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
        getInitialPayment(){            
            var total = null;
            if(this.getOrder.project_bid){
                total = (this.getOrder.project_bid.project_bid_maxQty * this.data.order_negotiatedPrice).toFixed(2)                
            }
            else{
                total = (this.getOrder.on_hand_bid.on_hand_bid_qty * this.data.order_negotiatedPrice).toFixed(2)
            } 
            return (total - (total * 0.5)).toFixed(2)           
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
        getAccName(){
            if(((this.bid_order_status_id == 5 || this.bid_order_status_id == 6) && this.getOrder.bid_order_acc.bid_order_acc_type == 'Final Payment')
            || ((this.bid_order_status_id == 3 || this.bid_order_status_id == 4) && this.getOrder.bid_order_acc.bid_order_acc_type == 'First Payment')){
                return this.getOrder.bid_order_acc.bid_order_acc_accName
            }
            else{
                return null
            }
        },             
    },
    data(){
        return{
            bid_order_status_id: null,
            data: {
                id: this.$route.params.id,
                order_negotiatedPrice: null,
                order_datePlaced: null,
                project_bid_total: null,
                order_dpDueDate: null,
                order_dpAmount: null,
                on_hand_bid_total: null,
                produce_trader_id: null,
                order_finalPrice: null,
                order_finalQty: null
            },
            errors: null
        }
    }
}
</script>

<style>

</style>
<template>
  <div class="bidOrderDetails">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>{{ bid_order_status_id == 1 || bid_order_status_id == 2 ? 'Bidding Confirmation' : bid_order_status_id == 4 && data.order_finalPrice && data.order_finalQty && data.produce_trader_id ? 'Delivery Details' : bid_order_status_id == 7 || bid_order_status_id == 8 ? 'Refund Details' :  'Bidding Details'}}</h3>
    </div>
    <form class="ps-3 pe-4 w-100" @submit.prevent="sendOrder()">
        <div class="d-flex align-items-baseline justify-content-between w-100">
            <h5 v-if="getOrder.distributor">Distributor: {{ getOrder.distributor.distributor_firstName + ' ' + getOrder.distributor.distributor_lastName }}</h5>        
            <h5 v-if="getOrder.produce">Produce: {{ getOrder.produce.prod_name + ' ' + getOrder.produce.prod_type }} {{ getOrder.bidOrder.order_grade ? 'Class ' + getOrder.bidOrder.order_grade : '' }}</h5>
        </div>        
        <div class="d-flex align-items-baseline justify-content-between w-100 mt-2">
            <h5 v-if="getOrder.contract">Bid: {{ getOrder.contract.farm_name }} {{ getOrder.on_hand_bid ? 'On-Hand' : 'Project' }}</h5>        
            <h5 v-if="getOrder.bidOrder">Status: {{ getStatus() }}</h5>
        </div>        
        <div class="d-flex align-items-baseline justify-content-between w-100 mt-2">
            <h5 v-if="getOrder.distributor">Dist #: {{ getOrder.distributor_contactNum[0].distributor_contactNum }}</h5>        
            <h5 v-if="getOrder.project || getOrder.produce_yield">{{ getOrder.produce_yield ? 'Last Date of Harvest:' : 'Expected Date of Harvest:' }} {{ getOrder.produce_yield ? getOrder.produce_yield.produce_yield_dateHarvestTo : getProgressDate }}</h5>
        </div>        
        <div class="d-flex align-items-baseline justify-content-between w-100 mt-2">
            <h5 v-if="getOrder.bidOrder">Order Placed: {{ getOrder.bidOrder.created_at.split('T')[0] }}</h5>        
            <h5 v-if="getOrder.bidOrder">Expected Dates Needed: {{ getOrder.bidOrder.order_dateNeededFrom + ' - ' + getOrder.bidOrder.order_dateNeededTo }}</h5>
        </div>
        <div class="d-flex align-items-baseline justify-content-between w-100 mt-2">
            <h5 v-if="getOrder.dist_address">Dist Address: {{ getOrder.dist_address.distributor_address + ' ' + getOrder.dist_address.distributor_province + ' ' + getOrder.dist_address.distributor_zipcode }}</h5>        
            <h5 v-if="getOrder.delivery">Delivery Dispatch Date: {{ getOrder.delivery.delivery_date }}</h5>
        </div>
        <table width="100%" class="mt-3 mb-4">
            <thead>
                <tr>   
                    <th>Transaction #</th>                
                    <th>Type</th>
                    <th>Mode Of Payment</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>Bank Name</th>
                    <th>Amount</th>
                    <th>Date Paid</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(acc, index) in getOrder.bid_order_acc" :key="index"> 
                    <td>{{ acc.id }}</td>               
                    <td>{{ acc.bid_order_acc_type }}</td>
                    <td>{{ acc.bid_order_acc_paymentMethod }}</td>
                    <td>{{ acc.bid_order_acc_accName }}</td>
                    <td>{{ acc.bid_order_acc_accNum }}</td>
                    <td>{{ acc.bid_order_acc_bankName }}</td>
                    <td>{{ acc.bid_order_acc_amount }}</td>
                    <td>{{ acc.bid_order_acc_datePaid }}</td>
                </tr>
                <tr>                    
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>                    
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>                   
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>                   
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>                    
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <div v-if="getOrder.bidOrder && getOrder.bidOrder.order_finalPrice" class="d-flex align-items-baseline justify-content-between w-100">
            <h5>Final Price: {{ getOrder.bidOrder.order_finalPrice.toFixed(2) }}</h5>
        </div>
        <!-- <div v-if="bid_order_status_id == 7" class="d-flex align-items-baseline justify-content-between w-100">
            <h5 v-if="getOrder.bidOrder">First Payment: {{ getOrder.bidOrder.order_dpAmount.toFixed(2) }}</h5>
        </div>         -->
        <div v-if="bid_order_status_id != 1 || bid_order_status_id != 2 || bid_order_status_id != 3" :class="getOrder.bidOrder && getOrder.bidOrder.order_finalPrice ? 'd-flex align-items-baseline justify-content-between w-100 mt-2' : 'd-flex align-items-baseline justify-content-between w-100'">
            <h5 v-if="getOrder.bidOrder && getOrder.bidOrder.order_negotiatedPrice" class="me-3">Negotiated Price: {{ getOrder.bidOrder.order_negotiatedPrice.toFixed(2) }}</h5>           
            <h5 v-if="bid_order_status_id == 4 && data.order_finalPrice && data.order_finalQty && data.produce_trader_id">Final Price: {{ data.order_finalPrice }}</h5>            
            <div v-if="bid_order_status_id == 7 && getOrder.refund && getOrder.bid_order_acc[0].bid_order_acc_type != 'Refund'" class="d-flex align-items-baseline">
                <h5 class="me-3">Payment Method:</h5>
                <select v-if="bid_order_status_id == 7" name="" id="" class="form-select" style="width:150px" @change="setPaymentMethod($event)">
                    <option value="Cash" selected>Cash</option>
                    <option value="Bank">Bank</option>
                </select>
                <p v-else>{{ getOrder.bid_order_acc[0].bid_order_acc_paymentMethod }}</p>
            </div>
        </div>
        <div :class="bid_order_status_id != 1 || bid_order_status_id != 2 || bid_order_status_id != 3 ? 'd-flex align-items-baseline justify-content-between w-100 mt-2' : 'd-flex align-items-baseline justify-content-between w-100'">
            <h5 v-if="getOrder.contract">Trader's Price: {{ getOrder.bidOrder.order_traderPrice.toFixed(2) }}</h5>        
            <div v-if="bid_order_status_id != 3 && bid_order_status_id != 4 && bid_order_status_id != 5 && bid_order_status_id != 6 && bid_order_status_id != 7" class="d-flex align-items-baseline">
                <h5 class="me-3">Negotiated Price:</h5>
                <input type="number" v-if="bid_order_status_id == 1" name="" id="" class="form-control" style="width:150px;" min="0" step=".5" :disabled="getOrder.on_hand_bid" v-model="data.order_negotiatedPrice" @keyup="resetAmountPercentage($event)" onkeydown="return false" @change="resetAmountPercentage($event)">
                <h5 v-else-if="getOrder.bidOrder && getOrder.bidOrder.order_negotiatedPrice" class="me-3">{{ getOrder.bidOrder.order_negotiatedPrice ?  getOrder.bidOrder.order_negotiatedPrice.toFixed(2) : '' }}</h5>
            </div>
            <h5 v-if="bid_order_status_id == 4 && data.order_finalPrice && data.order_finalQty && data.produce_trader_id">Quantity to be Received: {{ data.order_finalQty + ' kg/s' }}</h5>            
            <div v-if="bid_order_status_id == 7 && getOrder.refund && data.bid_order_acc_paymentMethod == 'Bank' && getOrder.bid_order_acc[0].bid_order_acc_type != 'Refund'" class="d-flex align-items-baseline">
                <h5 class="me-3">Bank Name:</h5>
                <input type="text" class="form-control" style="width:150px" v-model="data.bid_order_acc_bankName">
            </div>
        </div>
        <div class="d-flex align-items-baseline justify-content-between w-100 mt-3">
            <h5 v-if="getOrder.bidOrder && getOrder.bidOrder.order_initialPrice">Asking Price: {{ getOrder.bidOrder.order_initialPrice ? getOrder.bidOrder.order_initialPrice.toFixed(2) : '' }}</h5>        
            <div v-if="bid_order_status_id != 3 && bid_order_status_id != 4 && bid_order_status_id != 5 && bid_order_status_id != 6 && bid_order_status_id != 7" class="d-flex align-items-baseline">
                <h5 class="me-3">First Payment:</h5>
                <input v-if="(getOrder.project_bid || getOrder.on_hand_bid) && bid_order_status_id == 1" id="amount" type="number" disabled class="form-control me-3" style="width:150px;" step="0.5" v-model="data.order_dpAmount" onkeydown="return false" @keyup="validateAmount($event)">
                <h5 v-else-if="getOrder.bidOrder && getOrder.bidOrder.order_dpAmount" class="me-3">{{ getOrder.bidOrder.order_dpAmount ? getOrder.bidOrder.order_dpAmount.toFixed(2) : '' }}</h5>                
                <input v-if="bid_order_status_id == 1" type="number" class="form-control" id="percentage" disabled style="width:100px;" min="0" max="100" value="50.00" @keyup="validatePercent($event)" onkeydown="return false">
                <h5 v-else-if="getOrder.bidOrder" class="me-3">{{ getPercentage + '%' }}</h5>
            </div>
            <!-- (bid_order_status_id == 5 && getOrder.on_hand_bid) || -->
            <h5 v-if="(bid_order_status_id == 4 && $route.query.finalPrice || bid_order_status_id == 4 && getOrder.on_hand_bid)">Total Amount: {{ getOrder.on_hand_bid ? getOrder.on_hand_bid.on_hand_bid_total.toFixed(2) : data.order_finalTotal }}</h5>
            <div v-if="bid_order_status_id == 7 && getOrder.refund && data.bid_order_acc_paymentMethod == 'Bank' && getOrder.bid_order_acc[0].bid_order_acc_type != 'Refund'" class="d-flex align-items-baseline">
                <h5 class="me-3">Account Number:</h5>
                <input type="text" class="form-control" style="width:150px" v-model="data.bid_order_acc_accNum">
            </div>
        </div>
        <div class="d-flex align-items-baseline justify-content-between w-100 mt-3">
            <h5 v-if="getOrder.contract && getOrder.contract.contract_estimatedHarvest">Estimated Harvest Qty: {{ getOrder.contract.contract_estimatedHarvest ? getOrder.contract.contract_estimatedHarvest.toFixed(2) + ' kgs' : ''}}</h5>        
            <div v-if="bid_order_status_id != 3 && bid_order_status_id != 4 && bid_order_status_id != 5 && bid_order_status_id != 6 && bid_order_status_id != 7" class="d-flex align-items-baseline">
                <h5 v-if="bid_order_status_id == 1 || bid_order_status_id == 2" class="me-3">Due Date of First Payment:</h5>
                <h5 v-else class="me-3">Date of First Payment:</h5>
                <p v-if="getOrder.bid_order_acc">{{ getOrder.bid_order_acc.bid_order_acc_datePaid ? getOrder.bid_order_acc.bid_order_acc_datePaid : data.order_dpDueDate }}</p>
            </div>
            <h5 v-if="bid_order_status_id == 4 && data.order_finalPrice && data.order_finalQty && data.produce_trader_id && getDeliveryForm.bid_order && getDeliveryForm.bid_order.order_dpAmount || (bid_order_status_id == 4 && getOrder.on_hand_bid)">Paid Amount: {{ bid_order_status_id == 4 && getOrder.on_hand_bid ? getOrder.bidOrder.order_dpAmount.toFixed(2) :getDeliveryForm.bid_order.order_dpAmount.toFixed(2) }}</h5>
            <div v-if="bid_order_status_id == 7 && getOrder.refund && getOrder.bid_order_acc[0].bid_order_acc_type != 'Refund'" class="d-flex align-items-baseline">
                <h5 class="me-3">Account Name:</h5>
                <input type="text" class="form-control" style="width:150px" v-model="data.bid_order_acc_accName">
            </div>
        </div>
        <div v-if="data.produce_trader_id || (bid_order_status_id == 4 && getOrder.on_hand_bid)" class="d-flex align-items-baseline justify-content-between w-100 mt-3">            
            <h5 v-if="getDeliveryForm.produce_trader || (bid_order_status_id == 4 && getOrder.on_hand_bid)" class="me-3">Produce Selected: {{ getProduceName }}</h5> 
            <h5 v-if="getDeliveryForm.bid_order || (bid_order_status_id == 4 && getOrder.on_hand_bid)" class="pt-2" style="border-top: 2px solid black">Remaining Amount: {{ getRemaining }}</h5> 
        </div>
        <div class="d-flex align-items-baseline justify-content-between w-100 mt-3">
            <h5 v-if="((getOrder.project_bid && getOrder.project_bid.project_bid_minQty && getOrder.project_bid.project_bid_maxQty) || (getOrder.on_hand_bid && getOrder.on_hand_bid.on_hand_bid_qty))">
                {{ getOrder.bidOrder && getOrder.bidOrder.order_finalQty ? 'Quantity to be Given:' : 'Quantity Needed:' }} 
                {{ getOrder.bidOrder && getOrder.bidOrder.order_finalQty ? getOrder.bidOrder.order_finalQty.toFixed(2) + ' kg/s' : getOrder.project_bid ? getOrder.project_bid.project_bid_maxQty.toFixed(2) + ' kg/s' : getOrder.on_hand_bid.on_hand_bid_qty.toFixed(2) + ' kg/s' }}</h5>                    
            <div v-if="bid_order_status_id == 7 && getOrder.refund && getOrder.bid_order_acc[0].bid_order_acc_type != 'Refund'" class="d-flex align-items-baseline">
                <h5 class="me-3">Date Paid:</h5>
                <input type="date" class="form-control" style="width:150px" v-model="data.bid_order_acc_datePaid">
            </div>
            <div v-if="bid_order_status_id == 4 && data.order_finalPrice && data.order_finalQty && data.produce_trader_id" class="d-flex align-items-baseline">
                <h5 class="me-3">Delivery Send-off Date:</h5>
                <input type="date" name="" id="" v-model="data.delivery_date" onkeydown="return false" class="form-control" style="width:150px;">
            </div>
        </div>
        <div v-if="bid_order_status_id == 1 || bid_order_status_id == 2" class="d-flex align-items-baseline justify-content-between w-100 mt-3">
            <h5 v-if="getOrder.project_bid || (getOrder.on_hand_bid && getOrder.on_hand_bid.on_hand_bid_total)">Initial Total Balance: {{ getOrder.project_bid ? getOrder.project_bid.project_bid_total.split('-')[1] : getOrder.on_hand_bid.on_hand_bid_total.toFixed(2) }}</h5>                    
        </div>
        <div class="d-flex align-items-baseline justify-content-between w-100 mt-3">
            <h5 v-if="getOrder.project_bid || (getOrder.on_hand_bid && getOrder.on_hand_bid.on_hand_bid_total)">Updated Total Balance: {{ getOrder.bidOrder && getOrder.bidOrder.order_finalTotal ? getOrder.bidOrder.order_finalTotal.toFixed(2) : getTotal }}</h5>                    
            <div v-if="bid_order_status_id == 7 && getOrder.refund && getOrder.bid_order_acc[0].bid_order_acc_type != 'Refund'" class="d-flex align-items-baseline">
                <h5 v-if="getOrder.refund && getOrder.refund.refund_amount">Amount to be Refunded: {{ getOrder.refund.refund_amount.toFixed(2) }}</h5>
                <div v-else class="d-flex align-items-baseline">
                    <h5 class="me-3">Amount to be Refunded:</h5>
                    <input type="number" class="form-control" style="width:150px" v-model="data.bid_order_acc_amount" :max="getOrder.bidOrder.order_dpAmount" onkeydown="return false" step=".5" min="0">
                </div>                
            </div>
            
        </div>
        <div v-if="bid_order_status_id == 7 && getOrder.refund" class="d-flex align-items-baseline justify-content-between w-100 mt-3">
            <h5>Refund Requested: {{ getOrder.refund.created_at.split('T')[0] }}</h5>            
        </div>    
        <div v-if="bid_order_status_id == 7 && getOrder.refund && getOrder.refund.refund_numOfDays" class="d-flex align-items-baseline justify-content-between w-100 mt-3">
            <h5># of Days: {{ getOrder.refund.refund_numOfDays + ' days' }}</h5>
        </div>               
        <div v-if="getOrder.delivery && (bid_order_status_id == 5 || bid_order_status_id == 6)" class="d-flex align-items-baseline justify-content-between w-100 mt-3">
            <h5>{{ getOrder.delivery.delivery_receivedBy ? 'Received By: ' : 'Not Yet Received' }} {{ getOrder.delivery.delivery_receivedBy ? getOrder.delivery.delivery_receivedBy  + ' - ' + getOrder.delivery.delivery_contactNum : '' }}</h5>                  
        </div>
        <button v-if="bid_order_status_id != 4 && bid_order_status_id != 5 && bid_order_status_id != 2 && bid_order_status_id != 7 && (bid_order_status_id == 1 || bid_order_status_id == 3 && getOrder.bid_order_acc.length > 0 && getOrder.bid_order_acc[0].bid_order_acc_type == 'First Payment' || bid_order_status_id == 6 && getOrder.bid_order_acc.length > 0 && getOrder.bid_order_acc[0].bid_order_acc_type == 'Final Payment')" type="submit" class="btn btn-success px-5" style="position:absolute; right: 5%; bottom: 2%">{{ bid_order_status_id == 1 ? 'Post' : bid_order_status_id == 3 && getOrder.bid_order_acc.length > 0 || bid_order_status_id == 6 ? 'Confirm' : '' }}</button>
        <button type="button" v-if="bid_order_status_id != 2 && (bid_order_status_id == 7 || bid_order_status_id == 1 || bid_order_status_id == 3 && getOrder.bid_order_acc.length > 0 && getOrder.bid_order_acc[0].bid_order_acc_type == 'First Payment' || bid_order_status_id == 6 && getOrder.bid_order_acc.length > 0 && getOrder.bid_order_acc[0].bid_order_acc_type == 'Final Payment')" class="btn btn-secondary me-3" @click="cancelTransaction()" style="position:absolute; right: 13%; bottom: 2%">Cancel</button>
        <button class="btn btn-success px-5" @click="sendDelivery()" v-if="bid_order_status_id == 4 && data.order_finalPrice && data.order_finalQty && data.produce_trader_id" style="position:absolute; right: 5%; bottom: 2%">Post</button>
        <button v-if="bid_order_status_id == 7 && getOrder.bid_order_acc[0].bid_order_acc_type != 'Refund'" @click="refundApprove()" class="btn btn-success px-5" style="position:absolute; right: 5%; bottom: 2%">Post</button>
    </form>
  </div>
</template> 

<script>
import { mapActions, mapGetters } from 'vuex'
import { add, format } from 'date-fns'
export default {
    name: "BidOrderDetailss", // test on project bid from the start
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
            if(this.bid_order_status_id == 7 && this.getOrder.refund){
                if(this.getOrder.refund.refund_amount){
                    this.data.bid_order_acc_amount = this.getOrder.refund.refund_amount
                }
                else{
                    this.data.bid_order_acc_amount = this.getOrder.bidOrder.order_dpAmount.toFixed(2)
                }                
            }
            if((this.$route.query.finalPrice && this.$route.query.finalQty && this.$route.query.produce) || (this.bid_order_status_id == 4 && this.getOrder.on_hand_bid)){                
                var data;
                if(this.bid_order_status_id == 4 && this.getOrder.on_hand_bid){
                    data = {
                        id: this.getOrder.bidOrder.id,
                        produce_id: this.getOrder.bidOrder.produce_trader_id
                    }
                }
                else{
                    data = {
                        id: this.$route.params.id,
                        produce_id: this.$route.query.produce
                    }
                }                
                this.fetchDeliveryFormDetails(data)
                .then(() => {
                    this.data.produce_yield_dateHarvestTo = this.getDeliveryForm.yield.produce_yield_dateHarvestTo
                    var nameArr = this.getDeliveryForm.produce_trader.prod_name.split('')            
                    var prodClass = nameArr.splice(nameArr.indexOf('('), nameArr.length)
                    this.data.order_grade = prodClass[prodClass.length - 2]
                    this.data.order_finalTotal = parseFloat(this.$route.query.finalPrice * this.$route.query.finalQty).toFixed(2)
                    this.readyApp()
                })
                .catch((err) => {
                    console.log(err)
                    // this.$router.push({ name: 'AllBidOrders' })
                })
            }
            else{
                this.readyApp()
            }           
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
            'fetchDeliveryFormDetails', 
            'sendDeliveryFormDetails',
            'approveRefund',
            'cancelPaymentOrOrder'           
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
        },
        setPaymentMethod(e){
            this.data.bid_order_acc_paymentMethod = e.target.value
            this.data.bid_order_acc_bankName = null
            this.data.bid_order_acc_accNum = null
        },
        sendDelivery(){
            this.sendDeliveryFormDetails(this.data)
            .then(() => {
                this.$toastr.s('Delivery Form sent to Distributor!')
                setTimeout(() => {
                    this.$router.push({ name: 'AllBidOrders' })
                }, 5000)
            })
            .catch((err) => {
                console.error(err)
                this.errors = err.response.data.errors
                    for(var error in this.errors){
                    this.$toastr.e(this.errors[error][0])
                }
            })            
        },
        getTotall(){
            this.data.order_finalTotal = parseFloat(this.$route.query.finalPrice * this.$route.query.finalQty).toFixed(2)
            return parseFloat(this.$route.query.finalPrice * this.$route.query.finalQty).toFixed(2)
        }, 
        cancelTransaction(){                
            var data = {
                id: this.getOrder.bidOrder.id,
                bid_type: this.getOrder.project_bid ? 'Project Bid' 
                : this.getOrder.on_hand_bid ? 'On Hand Bid' : null
            }        
            this.cancelPaymentOrOrder(data)
            .then(() => {
                this.$router.push({ name: 'AllBidOrders' })
            })
        }       
    },
    computed: {
        ...mapGetters(['getOrder', 'getDeliveryForm']),
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
                total = (this.data.order_negotiatedPrice * this.getOrder.project_bid.project_bid_maxQty).toFixed(2)
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
        getProduceName(){
            if(this.getDeliveryForm.produce_trader){
                if(this.getDeliveryForm.produce_trader.produce_numOfGrades == 1){
                    return this.getDeliveryForm.produce_trader.prod_name + ' ' + this.getDeliveryForm.produce.prod_type            
                }
                else{                
                    var nameArr = this.getDeliveryForm.produce_trader.prod_name.split('')
                    var name = nameArr.splice(0, nameArr.indexOf('(') - 1)
                    var prodClass = nameArr.splice(nameArr.indexOf('('), nameArr.length)
                    console.log(prodClass)
                    return name.join('') + ' ' + this.getDeliveryForm.produce.prod_type + ' ' + prodClass.join('')                              
                }
            }
            else{
                if(this.getOrder.bidOrder.order_grade){
                    return this.getOrder.produce.prod_name + ' ' + this.getOrder.produce.prod_type + ` (Class ${this.getOrder.bidOrder.order_grade})`
                }
                else{
                    return this.getOrder.produce.prod_name + ' ' + this.getOrder.produce.prod_type
                }
            }
        },
        getRemaining(){
            var remaining
            if(this.bid_order_status_id == 4 && this.getOrder.on_hand_bid){
                remaining = parseFloat(this.getOrder.on_hand_bid.on_hand_bid_total - this.getOrder.bidOrder.order_dpAmount).toFixed(2)
                return remaining >= 0 ? remaining : parseFloat(0).toFixed(2)
            }
            else{
                remaining = parseFloat(this.getTotall() - parseFloat(this.getDeliveryForm.bid_order.order_dpAmount).toFixed(2))
                return remaining >= 0 ? remaining.toFixed(2) : parseFloat(0).toFixed(2)
            }            
        }             
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
                order_finalQty: this.$route.query.finalQty,
                order_finalPrice: this.$route.query.finalPrice,
                order_finalTotal: null,
                produce_yield_dateHarvestTo: null,
                delivery_date: null,
                produce_trader_id: this.$route.query.produce,
                order_grade: null,
                bid_order_acc_paymentMethod: 'Cash',
                bid_order_acc_type: 'Refund',
                bid_order_acc_amount: null,
                bid_order_acc_bankName: null,
                bid_order_acc_accNum: null,
                bid_order_acc_accName: null,
                bid_order_acc_remarks: null,
                bid_order_acc_datePaid: format(new Date(), 'yyyy-MM-dd')
            },
            errors: null
        }
    }
}
</script>

<style scoped>
th, td{
    text-align: center;
    border: 1px solid black;
    padding: 0 30px;
}
table{
    background:lightgrey
}
table, th, td{
    border-collapse: collapse;
    border-spacing: 0;
}
</style>
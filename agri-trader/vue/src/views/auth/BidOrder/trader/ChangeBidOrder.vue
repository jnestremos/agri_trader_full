<template>
  <div class="changeBidOrder">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Change of Bid Order</h3>
    </div>     
    <div class="container-fluid w-100 d-flex p-1 m-0" style="height:90%; position:relative">
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
                <p v-if="getOrder.produce">{{ getOrder.produce.prod_name }} {{ getOrder.bidOrder.order_grade ? 'Class ' + getOrder.bidOrder.order_grade : '' }}</p>
            </div> 
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Dist #:</h5>
                <p v-if="getOrder.distributor">{{ getOrder.distributor_contactNum[0].distributor_contactNum }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Trader's Price:</h5>
                <p v-if="getOrder.contract">{{ getOrder.contract.contract_estimatedPrice ? getOrder.contract.contract_estimatedPrice.toFixed(2) : '' }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Asking Price:</h5>
                <p v-if="getOrder.bidOrder">{{ getOrder.bidOrder.order_initialPrice ? getOrder.bidOrder.order_initialPrice.toFixed(2) : '' }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Estimated Harvest Qty:</h5>
                <p v-if="getOrder.contract">{{ getOrder.contract.contract_estimatedHarvest ? getOrder.contract.contract_estimatedHarvest.toFixed(2) + ' kgs' : ''}}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Quantity Needed:</h5>
                <p v-if="getOrder.project_bid || getOrder.on_hand_bid">{{ getOrder.project_bid ? getOrder.project_bid.project_bid_minQty.toFixed(2) + ' - ' + getOrder.project_bid.project_bid_maxQty.toFixed(2) + ' kgs' : getOrder.on_hand_bid.on_hand_bid_qty.toFixed(2) + ' kgs' }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Date Placed:</h5>
                <p v-if="getOrder.bidOrder">{{ getOrder.bidOrder.created_at.split('T')[0] }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Expected Date of Harvest:</h5>
                <p v-if="getOrder.project">{{ getProgressDate }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Expected Dates Needed:</h5>
                <p v-if="getOrder.bidOrder">{{ getOrder.bidOrder.order_dateNeededFrom + ' - ' + getOrder.bidOrder.order_dateNeededTo }}</p>
            </div>                                                                                  
        </div>
        <div class="container-fluid m-0 p-3 h-100 d-flex flex-column justify-content-between" style="width:50%;">
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Negotiated Price:</h5>
                <h5 class="me-3">Php</h5>
                <input type="number" class="form-control" style="width:150px;" min="0" v-model="data.order_negotiatedPrice" @change="setFinalPayment($event)" @keyup="setFinalPayment($event)">
                <!-- <h5 v-else-if="getOrder.bidOrder" class="me-3">{{ getOrder.bidOrder.order_negotiatedPrice ?  getOrder.bidOrder.order_negotiatedPrice.toFixed(2) : '' }}</h5> -->
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Final Payment:</h5>
                <h5 class="me-3">Php</h5>   
                <h5>{{ data.order_dpAmount }}</h5>             
                <!-- <input id="amount" type="number" class="form-control me-3" style="width:150px;" step="0.5" v-model="data.order_dpAmount" @keyup="validateAmount($event)">                                 -->
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Date of First Payment:</h5>                                
                <p v-if=" getOrder.bidOrder">{{ getOrder.bidOrder.order_dpDueDate ? getOrder.bidOrder.order_dpDueDate : data.order_dpDueDate }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Total (Original):</h5>
                <p v-if="getOrder.project_bid || getOrder.on_hand_bid">{{ getOrder.project_bid ? getOrder.project_bid.project_bid_total : getOrder.on_hand_bid_total }}</p>
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Total (Updated):</h5>
                <p v-if="getOrder.project_bid ||  getOrder.on_hand_bid">{{ getTotal }}</p>
                <input type="text" name="" id="" hidden v-model="data.project_bid_total">
            </div>
            <div v-if="bid_order_status_id > 2">
                <div class="d-flex align-items-baseline">
                    <h5 class="me-3">Payment Transacted By:</h5>
                    <p v-if="getOrder.bid_order_acc">{{ getOrder.bid_order_acc.bid_order_acc_accName }}</p>                
                </div>
                <div class="d-flex align-items-baseline">
                    <h5 class="me-3">Payment Method:</h5>
                    <p v-if="getOrder.bid_order_acc">{{ getOrder.bid_order_acc.bid_order_acc_paymentMethod }}</p>                
                </div>
                <div class="d-flex align-items-baseline">
                    <h5 class="me-3">Account:</h5>
                    <p v-if="getOrder.bid_order_acc" style="word-spacing:15px">{{ getOrder.bid_order_acc.bid_order_acc_amount + " " + getPercentage + '%' }}</p>                
                </div>
                <div class="d-flex align-items-baseline">
                    <h5 class="me-3">Date Paid:</h5>
                    <p v-if="getOrder.bid_order_acc">{{ getOrder.bid_order_acc.bid_order_acc_datePaid }}</p>                
                </div>                
            </div>
            <div class="d-flex align-items-baseline">
                <h5 class="me-3">Name of Trader:</h5>
                <p>Joshua Estremos</p>
            </div>
        </div> 
        <input v-if="bid_order_status_id != 2 && bid_order_status_id <= 3" type="submit" class="btn btn-success" :value="bid_order_status_id == 1 ? 'Post' : 'Confirm'" style="position:absolute; bottom:3%; right:10%;">                           
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { add, format } from 'date-fns'
export default {
    name: "ChangeBidOrder",
    data(){
        return{
            bid_order_status_id: null,
            data: {
                id: this.$route.params.id,
                order_negotiatedPrice: null,
                order_datePlaced: null,
                project_bid_total: null,
                order_dpDueDate: null,
                order_dpAmount: null
            },
            errors: null
        }
    },    
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
            }
            else{
                total = (this.data.order_negotiatedPrice * this.getOrder.on_hand_bid.on_hand_bid_qty).toFixed(2)  
                totall = (this.getOrder.on_hand_bid.on_hand_bid_qty * this.data.order_negotiatedPrice).toFixed(2)
            }
            this.data.project_bid_total = total        
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
    methods: {
        ...mapActions(['readyApp', 'fetchOrder']), 
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
        getPercentage() {
            var total = null;
            if(this.getOrder.project_bid){
                total = (this.getOrder.project_bid.project_bid_maxQty * this.data.order_negotiatedPrice).toFixed(2)
            }
            else{
                total = (this.getOrder.on_hand_bid.on_hand_bid_qty * this.data.order_negotiatedPrice).toFixed(2)
            }
            return parseFloat((parseFloat(this.getOrder.bidOrder.order_dpAmount) / total) * 100).toFixed(2)
        },                               
    }
}
</script>

<style>

</style>
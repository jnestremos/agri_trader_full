<template>
  <div class="deliveryDetails">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Delivery Form</h3>
    </div>
    <div action="" class="container-fluid w-100 p-3 m-0" style="height:90%; position:relative;">
        <div class="d-flex justify-content-between align-items-baseline mb-5" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3">Date of Harvest:</h5>
                <p v-if="getDeliveryForm.yield">{{ getDeliveryForm.yield.produce_yield_dateHarvestTo }}</p>
            </div>
            <div class="d-flex align-items-baseline" style="width:40%">
                <h5 class="me-3">Trader's Price:</h5>
                <p v-if="getDeliveryForm.contract">Php {{ getDeliveryForm.contract.contract_estimatedPrice.toFixed(2) }}</p>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline mb-5" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3">Bid:</h5>
                <p v-if="getDeliveryForm.project_bid && getDeliveryForm.farm">{{ getDeliveryForm.farm.farm_name }} Project</p>
                <p v-else-if="getDeliveryForm.on_hand_bid && getDeliveryForm.farm">{{ getDeliveryForm.farm.farm_name }} On-Hand</p>
            </div>
            <div class="d-flex align-items-baseline" style="width:40%">
                <h5 class="me-3">Asking Price:</h5>
                <p v-if="getDeliveryForm.bid_order">Php {{ getDeliveryForm.bid_order.order_initialPrice.toFixed(2) }}</p>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline mb-5" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3">Distributor:</h5>
                <p v-if="getDeliveryForm.distributor">{{ getDeliveryForm.distributor.distributor_firstName + ' ' + getDeliveryForm.distributor.distributor_lastName }}</p>
            </div>
            <div class="d-flex align-items-baseline" style="width:40%">
                <h5 class="me-3">Final Price:</h5>
                <p>Php {{ parseFloat($route.query.finalPrice).toFixed(2) }}</p>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline mb-5" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3">Dist. Address:</h5>
                <p v-if="getDeliveryForm.dist_address">{{ getDeliveryForm.dist_address.distributor_address + ' ' + getDeliveryForm.dist_address.distributor_province }}</p>
            </div>
            <div class="d-flex align-items-baseline" style="width:40%">
                <h5 class="me-3">Date of First Payment:</h5>
                <p v-if="getDeliveryForm.bid_order_acc">{{ getDeliveryForm.bid_order_acc.bid_order_acc_datePaid }}</p>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline mb-5" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3">Dist. Contact#:</h5>
                <p v-if="getDeliveryForm.dist_contactNum">{{ getDeliveryForm.dist_contactNum.distributor_contactNum }}</p>
            </div>
            <div class="d-flex align-items-baseline" style="width:40%">
                <h5 class="me-3">Total Amount:</h5>
                <p>Php {{ getTotal() }}</p>
            </div>        
        </div>
        <div class="d-flex justify-content-between align-items-baseline mb-5" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3">Produce:</h5>
                <p v-if="getDeliveryForm.produce_trader">{{ getProduceName }}</p>
            </div>
            <div class="d-flex align-items-baseline" style="width:40%">
                <h5 class="me-3">Paid Amount:</h5>
                <p v-if="getDeliveryForm.bid_order">Php {{ getDeliveryForm.bid_order.order_dpAmount.toFixed(2) }}</p>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline mb-5" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3" v-if="getDeliveryForm.project_bid">Estimated Harvest Qty:</h5>
                <p v-if="getDeliveryForm.contract && getDeliveryForm.project_bid">{{ getDeliveryForm.contract.contract_estimatedHarvest.toFixed(2) }} kgs</p>
            </div>
            <div class="d-flex align-items-baseline" style="width:40%">
                <h5 class="me-3">Remaining Amount:</h5>
                <p v-if="getDeliveryForm.bid_order">Php {{ getRemaining }}</p>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-baseline mb-5" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3">Quantity To Be Received:</h5>
                <p>{{ $route.query.finalQty }} kgs</p>
            </div> 
            <div class="d-flex align-items-baseline" style="width:40%">
                <h5 class="me-3">Delivery Send-off Date:</h5>
                <input type="date" name="" id="" v-model="data.delivery_date" onkeydown="return false" class="form-control" style="width:150px;">
            </div>                   
        </div>
        <div class="d-flex justify-content-between align-items-baseline" style="width:80%;">
            <div class="d-flex justify-content-between align-items-baseline">
                <h5 class="me-3">Dates Needed:</h5>
                <p v-if="getDeliveryForm.bid_order">{{ getDeliveryForm.bid_order.order_dateNeededFrom + ' - ' + getDeliveryForm.bid_order.order_dateNeededTo }}</p>
            </div> 
                
        </div>
        <button class="btn btn-success" style="position:absolute; bottom:3%; right:10%;" @click="sendDelivery()">Post</button>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'DeliveryDetails',
    created(){
        var data = {
            id: this.$route.params.id,
            produce_id: this.$route.query.produce
        }
        this.fetchDeliveryFormDetails(data)
        .then(() => {
            this.data.produce_yield_dateHarvestTo = this.getDeliveryForm.yield.produce_yield_dateHarvestTo
            var nameArr = this.getDeliveryForm.produce_trader.prod_name.split('')            
            var prodClass = nameArr.splice(nameArr.indexOf('('), nameArr.length)
            this.data.order_grade = prodClass[prodClass.length - 2]
            this.readyApp()
        })
    },
    data(){
        return {
            errors: null,
            data: {
                id: this.$route.params.id,
                order_finalQty: this.$route.query.finalQty,
                order_finalPrice: this.$route.query.finalPrice,
                order_finalTotal: null,
                produce_yield_dateHarvestTo: null,
                delivery_date: null,
                produce_trader_id: this.$route.query.produce,
                order_grade: null                
            }
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchDeliveryFormDetails', 'sendDeliveryFormDetails']),
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
        getTotal(){
            this.data.order_finalTotal = parseFloat(this.$route.query.finalPrice * this.$route.query.finalQty).toFixed(2)
            return parseFloat(this.$route.query.finalPrice * this.$route.query.finalQty).toFixed(2)
        },        
    },
    computed: {
        ...mapGetters(['getDeliveryForm']),
        getProduceName(){
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
        },
        getRemaining(){
            var remaining = parseFloat(this.getTotal() - parseFloat(this.getDeliveryForm.bid_order.order_dpAmount).toFixed(2))
            return remaining >= 0 ? remaining.toFixed(2) : parseFloat(0).toFixed(2)
        }
    }
}
</script>

<style>

</style>
<template>
  <div class="harvestDetails">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Harvest Overview</h3>        
    </div>     
    <div class="container-fluid p-0 m-0 w-100 d-flex" style="height:90%">
      <div class="w-50 h-100 px-3 py-2" style="position:relative">
        <h5 v-if="getHarvestDetails.farm">{{ getHarvestDetails.farm.farm_name }} - {{ getHarvestDetails.farm_owner.owner_firstName + ' ' + getHarvestDetails.farm_owner.owner_lastName }}</h5>
        <h5 v-if="getHarvestDetails.produce">Produce: {{ getHarvestDetails.produce.prod_name + ' ' + getHarvestDetails.produce.prod_type }}</h5>
        <h5 v-if="getHarvestDetails.produce_trader && getHarvestDetails.produce_trader.produce_numOfGrades > 1">Grades: A, B, C</h5>
        <h5 v-else>Grades: None</h5>
        <div class="d-flex justify-content-between align-items-baseline" style="width:65%">
          <h5>Date Harvested:</h5>
          <input type="date" v-model="data.produce_yield_dateHarvestFrom" onkeydown="return false" class="form-control me-3" style="width:150px;" :disabled="confirm">
          <input type="date" v-model="data.produce_yield_dateHarvestTo" onkeydown="return false" class="form-control" style="width:150px;" :disabled="confirm">
        </div>
        <h5 v-if="getHarvestDetails.produce_trader && getHarvestDetails.produce_trader.produce_numOfGrades > 1" class="mt-5">Quantity Harvested</h5>
        <div v-if="getHarvestDetails.produce_trader" :class="!(getHarvestDetails.produce_trader.produce_numOfGrades > 1) ? 'd-flex justify-content-between align-items-baseline mt-5':'d-flex justify-content-between align-items-baseline'" style="width:65%;">
          <h5 v-if="getHarvestDetails.produce_trader && getHarvestDetails.produce_trader.produce_numOfGrades > 1" class="w-50">By Grade:</h5>
          <h5 v-else class="w-50">Total Qty Harvested:</h5>
          <h5 class="w-50">Price (per kg):</h5>
        </div>
        <div v-if="getHarvestDetails.produce_trader && getHarvestDetails.produce_trader.produce_numOfGrades > 1">
          <div class="d-flex justify-content-between align-items-baseline mb-3" style="width:65%;">
            <div class="d-flex justify-content-between align-items-baseline" style="width:35%">
              <h5 class="me-3">A:</h5>
              <input type="number" v-model="data.produce_yield_qtyHarvested[0]" @keyup="setQty($event)" @change="setQty($event)" class="form-control me-3" style="width:150px;" :disabled="confirm">
              <h5>kgs</h5>
            </div>
            <div class="d-flex justify-content-between align-items-baseline" style="width:50%;">            
              <div class="d-flex align-items-baseline">
                <input type="number" v-model="data.produce_yield_price[0]" @keyup="setQty($event)" @change="setQty($event)" class="form-control me-3" style="width:150px;" :disabled="confirm">              
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-baseline mb-3" style="width:65%;">
            <div class="d-flex justify-content-between align-items-baseline" style="width:35%">
              <h5 class="me-3">B:</h5>
              <input type="number" v-model="data.produce_yield_qtyHarvested[1]" @keyup="setQty($event)" @change="setQty($event)" class="form-control me-3" style="width:150px;" :disabled="confirm">
              <h5>kgs</h5>
            </div>
            <div class="d-flex justify-content-between align-items-baseline" style="width:50%;">            
              <div class="d-flex align-items-baseline">
                <input type="number" v-model="data.produce_yield_price[1]" @keyup="setQty($event)" @change="setQty($event)" class="form-control me-3" style="width:150px;" :disabled="confirm">             
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-baseline mb-3" style="width:65%;">
            <div class="d-flex justify-content-between align-items-baseline" style="width:35%">
              <h5 class="me-3">C:</h5>
              <input type="number" v-model="data.produce_yield_qtyHarvested[2]" @keyup="setQty($event)" @change="setQty($event)" class="form-control me-3" style="width:150px;" :disabled="confirm">
              <h5>kgs</h5>
            </div>
            <div class="d-flex justify-content-between align-items-baseline" style="width:50%;">            
              <div class="d-flex align-items-baseline">
                <input type="number" v-model="data.produce_yield_price[2]" @keyup="setQty($event)" @change="setQty($event)" class="form-control me-3" style="width:150px;" :disabled="confirm">              
              </div>
            </div>
          </div>
        </div>
        <div v-else>
          <div class="d-flex justify-content-between align-items-baseline mb-3" style="width:65%;">
            <div class="d-flex justify-content-between align-items-baseline" style="width:35%">          
              <input type="number" v-model="data.produce_yield_qtyHarvested[0]" @keyup="setQty($event)" @change="setQty($event)" class="form-control me-3" style="width:150px;" :disabled="confirm">
              <h5>kgs</h5>
            </div>
            <div class="d-flex justify-content-between align-items-baseline" style="width:50%;">            
              <div class="d-flex align-items-baseline">
                <input type="number" v-model="data.produce_yield_price[0]" @keyup="setQty($event)" @change="setQty($event)" class="form-control me-3" style="width:150px;" :disabled="confirm">              
              </div>
            </div>
          </div>          
        </div>        
        <div class="d-flex align-items-baseline justify-content-between" style="width:60%; position:absolute; bottom:20%">
          <button class="btn btn-primary" :disabled="confirm" @click="sendHarvest()">Confirm Harvest</button>
          <button class="btn btn-primary" :disabled="!confirm">View Inventory</button>
        </div>
      </div>
      <div v-if="confirm" class="w-50 px-3 py-2" style="background:lightgray; height:90%; overflow-y:scroll">
        <table width="100%" style="margin: 0 auto; border-collapse: collapse; border-spacing:0;">
          <thead>
            <tr>
              <th>Distributor Name</th>
              <th>Class</th>
              <th>Price</th>
              <th>Demanded Quantity</th>
              <th>Date Demanded</th>
            </tr>
          </thead>
          <tbody>
            <tr @click="triggerModal(order)" v-for="(order, index) in getHarvestDetails.bid_orders" :key="index">
              <td style="border-left:2px solid black;">{{ getName(order) }}</td>
              <td>{{ order.order_grade ? order.order_grade : 'None' }}</td>
              <td>Php {{ order.order_negotiatedPrice.toFixed(2) }}</td>
              <td>{{ getQty(order) }} kgs</td>
              <td style="border-right:2px solid black;">{{ order.order_dateNeededFrom }} - {{ order.order_dateNeededTo }}</td>
              <template>
                <b-modal no-close-on-esc no-close-on-backdrop centered :id="`modal-${order.id}`" size="lg" title="Strawberry (Class A)">
                  <div style="width:100%;" class="p-1">
                    <div class="d-flex align-items-baseline justify-content-between mb-5" style="width:100%;">              
                      <div class="d-flex align-items-baseline w-50">
                        <h5 class="me-3">Date Placed:</h5>
                        <p>{{ order.created_at.split('T')[0] }}</p> 
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">
                        <div class="d-flex align-items-baseline">
                          <h5 class="me-3">Date Updated:</h5>
                          <p>{{ order.updated_at.split('T')[0] }}</p> 
                        </div>                  
                      </div>
                    </div>                    
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:100%;">              
                      <div class="d-flex align-items-baseline w-50">
                        <h5 class="me-3">Distributor Name:</h5>
                        <p>{{ getName(order) }}</p> 
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">
                        <div class="d-flex align-items-baseline">
                          <h5 class="me-3">Expected Date of Harvest:</h5>
                          <p>{{ getHarvestDetails.project_harvestableEnd }}</p> 
                        </div>                  
                      </div>
                    </div>                    
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:100%;">              
                      <div class="d-flex align-items-baseline w-50">
                        <h5 class="me-3">Contact Number:</h5>
                        <p>{{ getContactNum(order) }}</p> 
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">
                        <div class="d-flex align-items-baseline">
                          <h5 class="me-3">Email Address:</h5>
                          <p>{{ getEmail(order) }}</p> 
                        </div>                  
                      </div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:100%;">              
                      <div class="d-flex align-items-baseline w-50">
                        <h5 class="me-3">Asking Price:</h5>
                        <p>Php {{ order.order_initialPrice.toFixed(2) }}</p> 
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">                        
                        <h5 class="me-3">Quantity Needed:</h5>
                        <p style="word-spacing:10px;">{{ getQty(order) }} kgs</p>                                                                                                           
                      </div>
                    </div> 
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:60%;">
                      <h5>Expected Dates Needed:</h5>
                      <p>{{ order.order_dateNeededFrom }}</p>
                      <p>{{ order.order_dateNeededTo }}</p>
                    </div>                   
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:100%;">
                      <div class="d-flex align-items-baseline w-50">
                        <h5 class="me-3">Negotiated Price:</h5>
                        <p>Php {{ order.order_negotiatedPrice.toFixed(2) }}</p> 
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">                        
                        <h5 class="me-3">Minimum Price:</h5>
                        <p>Php {{ getMinPrice(order) }}</p>                                                                                                          
                      </div>
                    </div>                   
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:100%;">
                      <div class="d-flex align-items-baseline w-50">
                        <h5 class="me-3">Final Price:</h5>
                        <h5 class="me-3">Php</h5>
                        <input type="number" name="" v-model="data.order_finalPrice" @keyup="setPrice($event, order)" @change="setPrice($event, order)" id="" class="form-control" style="width:150px">
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">                        
                        <h5 class="me-3">Maximum Price:</h5>
                        <p>Php {{ getMaxPrice(order) }}</p>                                                                                                          
                      </div>
                    </div>                   
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:100%;">
                      <div class="d-flex align-items-baseline w-50">                        
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">                        
                        <h5 class="me-3">Quantity to be Given:</h5>
                        <input type="number" name="" id="" v-model="data.order_finalQty" @keyup="setQty($event, order)" @change="setQty($event, order)" class="form-control" style="width:150px">                                                                                                        
                      </div>
                    </div>                   
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:100%;">
                      <div class="d-flex align-items-baseline w-50">                        
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">                        
                        <h5 class="me-3">Total:</h5>
                        <p>Php {{ order_total.toFixed(2) }}</p>
                      </div>
                    </div>                   
                    <div class="d-flex justify-content-between align-items-baseline mb-2" style="width:50%;">
                      <h5>Initial First Payment:</h5>
                      <p>Php {{ order.order_dpAmount.toFixed(2) }} ({{ getPercentage(order) }}%)</p>
                    </div>                   
                    <div class="d-flex justify-content-between align-items-baseline mb-2" style="width:50%;">
                      <h5>Due Date of First Payment:</h5>
                      <p>{{ order.order_dpDueDate }}</p>
                    </div>                   
                    <div class="d-flex justify-content-between align-items-baseline mb-2" style="width:50%;">
                      <h5>Remaining Balance:</h5>
                      <p>Php {{ data.bid_order_acc_amount.toFixed(2) }}</p>
                    </div>                   
                  </div>
                  <template #modal-footer="{ok}">
                    <b-button size="md" variant="primary" @click="ok()" :disabled="!(data.bid_order_acc_amount >= 0)" id="okButton">
                      Proceed to Delivery
                    </b-button>                    
                  </template>
                </b-modal>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "HarvestDetails",
    created(){
        this.fetchHarvestDetails(this.$route.params.id)
        .then(() => {          
          if(this.getHarvestDetails.yield.length > 0){
            this.confirm = true
            this.data.produce_yield_dateHarvestFrom = this.getHarvestDetails.yield[0].produce_yield_dateHarvestFrom
            this.data.produce_yield_dateHarvestTo = this.getHarvestDetails.yield[0].produce_yield_dateHarvestTo
            if(this.getHarvestDetails.yield.length > 1){          
              this.data.produce_yield_qtyHarvested = [
                this.getHarvestDetails.yield[0].produce_yield_qtyHarvested,
                this.getHarvestDetails.yield[1].produce_yield_qtyHarvested,
                this.getHarvestDetails.yield[2].produce_yield_qtyHarvested
              ]
              this.data.produce_yield_price = [
                this.getHarvestDetails.yield[0].produce_yield_price,
                this.getHarvestDetails.yield[1].produce_yield_price,
                this.getHarvestDetails.yield[2].produce_yield_price
              ]
            }
            else{
              this.data.produce_yield_qtyHarvested = [
                this.getHarvestDetails.yield[0].produce_yield_qtyHarvested,                
              ]
              this.data.produce_yield_price = [
                this.getHarvestDetails.yield[0].produce_yield_price,                
              ]              
            }
          }
          else{
            this.confirm = false
          }
          this.data.project_id = this.$route.params.id
          this.data.produce_trader_id = this.getHarvestDetails.produce_trader.id
          this.data.project_harvestableEnd = this.getHarvestDetails.project_harvestableEnd
          this.readyApp()
        })
        .catch((err) => {
          console.error(err)
          this.$router.push({ name: 'AllProjects' })
        })
        
    },
    methods: {
        ...mapActions(['readyApp', 'fetchHarvestDetails', 'sendHarvestDetails']),
        triggerModal(order){
          this.$bvModal.show(`modal-${order.id}`)
          var qty = this.getQty(order).split(' - ')
          var maxPrice = parseFloat(qty[1] * parseFloat(order.order_negotiatedPrice))
          var remainingBal = maxPrice - order.order_dpAmount
          this.data.order_finalPrice = parseFloat(remainingBal / qty[1]).toFixed(2)   
          this.data.order_finalQty = qty[1]
          this.order_total = order.order_dpAmount
        },
        sendHarvest(){
          this.sendHarvestDetails(this.data)
          .then(() => {
            this.$toastr.s('Harvest Added Successfully!')
            setTimeout(() => {
              location.reload()
            }, 5000)
          })
          .catch((err) => {
            console.error(err)
            this.errors = err.response.data.errors
            for(var error in this.errors){
              this.$toastr.e(this.errors[error][0])
            } 
            this.data.produce_yield_qtyHarvested = [0,0,0]           
            this.data.produce_yield_price = [0,0,0]           
          })
        },
        setQty(e, order = null){
          if(e.target.value <= 0){
            e.target.value = 0            
          }
          if(order){
            var qty = this.getQty(order).split(' - ')             
            if(e.target.value < qty[0] || e.target.value > qty[1]){              
              this.data.order_finalQty = qty[1]
            }            
            this.order_total = this.data.order_finalQty * this.data.order_finalPrice
            this.data.bid_order_acc_amount = this.order_total - order.order_dpAmount
            if(this.data.bid_order_acc_amount < 0){              
              var maxPrice = parseFloat(qty[1] * parseFloat(order.order_negotiatedPrice))
              var remainingBal = maxPrice - order.order_dpAmount
              console.log(parseFloat(remainingBal / qty[1]).toFixed(2))
              this.data.order_finalPrice = parseFloat(remainingBal / qty[1]).toFixed(2)                             
              this.data.order_finalQty = qty[1] 
              this.data.bid_order_acc_amount = 0    
              this.order_total = order.order_dpAmount
            }
          }
          
        },
        setPrice(e, order){
          if(e.target.value <= 0){
            e.target.value = 0
          }
          this.order_total = e.target.value * this.data.order_finalQty
          this.data.bid_order_acc_amount = this.order_total - order.order_dpAmount
          console.log(this.data.bid_order_acc_amount < 0)
          if(this.data.bid_order_acc_amount < 0){
            var qty = this.getQty(order).split(' - ')
            var maxPrice = parseFloat(qty[1] * parseFloat(order.order_negotiatedPrice))
            var remainingBal = maxPrice - order.order_dpAmount
            console.log(parseFloat(remainingBal / qty[1]).toFixed(2))
            this.data.order_finalPrice = parseFloat(remainingBal / qty[1]).toFixed(2)
            this.data.order_finalQty = qty[1] 
            this.data.bid_order_acc_amount = 0 
            this.order_total = order.order_dpAmount                                           
          }
        },
        getName(order){
          var distObj = this.getHarvestDetails.distributors.filter((d) => {
            return parseInt(d.id) === parseInt(order.distributor_id)
          })
          return distObj[0].distributor_firstName + ' ' + distObj[0].distributor_lastName
        },
        getQty(order){
          if(this.getHarvestDetails.project_bids.length > 0){
            var projBidObj = this.getHarvestDetails.project_bids.filter((p) => {
              return parseInt(p.bid_order_id) === parseInt(order.id)
            })
            return projBidObj[0].project_bid_minQty + ' - ' + projBidObj[0].project_bid_maxQty
          }
          else if(this.getHarvestDetails.on_hand_bids.length > 0){
            var onHandBidObj = this.getHarvestDetails.on_hand_bids.filter((p) => {
              return parseInt(p.bid_order_id) === parseInt(order.id)
            }) 
            return onHandBidObj[0].on_hand_bid_qty           
          }           
        },
        getContactNum(order){
          var distObj = this.getHarvestDetails.distributors.filter((d) => {
            return parseInt(d.id) === parseInt(order.distributor_id)
          })
          var distContactNumObj = this.getHarvestDetails.dist_contactNums.filter((d) => {
            return parseInt(d.distributor_id) === parseInt(distObj[0].id)
          })
          return distContactNumObj[0].distributor_contactNum           
        },
        getEmail(order){
          var distObj = this.getHarvestDetails.distributors.filter((d) => {
            return parseInt(d.id) === parseInt(order.distributor_id)
          })
          return distObj[0].distributor_email        
        },
        getMinPrice(order){
          var qty = this.getQty(order).split(' - ')
          return parseFloat(qty[0] * parseFloat(order.order_negotiatedPrice)).toFixed(2)          
        },
        getMaxPrice(order){
          var qty = this.getQty(order).split(' - ')
          return parseFloat(qty[1] * parseFloat(order.order_negotiatedPrice)).toFixed(2)          
        },
        getPercentage(order){
          var qty = this.getQty(order).split(' - ')
          var totalPrice = parseFloat(qty[1] * parseFloat(order.order_negotiatedPrice))          
          return parseFloat(parseFloat(order.order_dpAmount / totalPrice) * 100).toFixed(2)
        },            
    },
    computed: {
      ...mapGetters(['getHarvestDetails'])
    },
    data(){
      return{
        confirm: null,
        errors: null,
        order_total: 0,
        data: {          
          project_id: null,
          produce_trader_id: null,
          project_harvestableEnd: null,
          produce_yield_qtyHarvested: [0,0,0],
          produce_yield_price: [0,0,0],
          produce_yield_dateHarvestFrom: null,
          produce_yield_dateHarvestTo: null,
          bid_order_acc_amount: 0,
          order_finalPrice: 0,
          order_finalQty: 0,                    
        }
      }
    }
}
</script>

<style scoped>
th, td{
  text-align: center;    
}
td{
   border-top:2px solid black;
   border-bottom:2px solid black;
   padding:2em;
}
tbody::before{
  height: 1em;
  display: table-row;
  content: '';
}
tr{
  background:transparent;
}
tbody tr:hover{
  transition: 0.5s ease-in;
  background:rgb(166, 159, 159)
}
</style>
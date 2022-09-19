<template>
  <div class="harvestDetails">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Harvest Overview</h3>        
    </div> 
    <div class="container-fluid p-0 m-0 w-100 d-flex" style="height:90%">
      <div class="w-50 h-100 px-3 py-2" style="position:relative">
        <h5 v-if="getHarvestDetails.farm">{{ getHarvestDetails.farm.farm_name }} - {{ getHarvestDetails.farm_owner.owner_firstName + ' ' + getHarvestDetails.farm_owner.owner_lastName }}</h5>
        <h5 v-if="getHarvestDetails.produce">Produce: {{ getHarvestDetails.produce.prod_name + ' ' + getHarvestDetails.produce.prod_type }}</h5>
        <h5 v-if="getHarvestDetails.produce_trader && getHarvestDetails.produce_trader[0].produce_numOfGrades > 1">Grades: A, B, C</h5>
        <h5 v-else>Grades: None</h5>
        <h5>Expected Date of Harvest: {{ data.project_harvestableEnd }}</h5>
        <div class="d-flex justify-content-between align-items-baseline" style="width:65%">
          <h5>Date Harvested:</h5>
          <input type="date" v-model="data.produce_yield_dateHarvestFrom" onkeydown="return false" class="form-control me-3" style="width:150px;" :disabled="confirm">
          <input type="date" v-model="data.produce_yield_dateHarvestTo" onkeydown="return false" class="form-control" style="width:150px;" :disabled="confirm">
        </div>
        <h5 v-if="getHarvestDetails.produce_trader && getHarvestDetails.produce_trader[0].produce_numOfGrades > 1" class="mt-5">Quantity Harvested</h5>
        <div v-if="getHarvestDetails.produce_trader" :class="!(getHarvestDetails.produce_trader[0].produce_numOfGrades > 1) ? 'd-flex justify-content-between align-items-baseline mt-5':'d-flex justify-content-between align-items-baseline'" style="width:65%;">
          <h5 v-if="getHarvestDetails.produce_trader && getHarvestDetails.produce_trader[0].produce_numOfGrades > 1" class="w-50">By Grade:</h5>
          <h5 v-else class="w-50">Total Qty Harvested:</h5>
          <h5 class="w-50">Price (per kg):</h5>
        </div>
        <div v-if="getHarvestDetails.produce_trader && getHarvestDetails.produce_trader[0].produce_numOfGrades > 1">
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
          <router-link :to="{ path: `/harvest/inventory/${$route.params.id}` }"><button class="btn btn-primary" :disabled="!confirm">View Inventory</button></router-link>
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
                <b-modal no-close-on-esc no-close-on-backdrop centered :id="`modal-${order.id}`" size="lg" :title="getProduceName(order)">
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
                        <p v-if="!newProduce">{{ data.order_finalPrice }}</p>
                        <input v-else type="number" name="" v-model="data.order_finalPrice" step="0.5" onkeydown="return false" @change="setPrice($event, order)" id="" class="form-control" style="width:150px">
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">                        
                        <h5 class="me-3">Maximum Price:</h5>
                        <p>Php {{ getMaxPrice(order) }}</p>                                                                                                          
                      </div>
                    </div>                   
                    <div class="d-flex align-items-baseline justify-content-between mb-2" style="width:100%;">
                      <div class="d-flex align-items-baseline w-50">   
                        <button v-if="!newProduce && getHarvestDetails.produce_list && getHarvestDetails.produce_list.length > 1" @click="initializeProduce()" class="btn btn-primary" style="width:200px">Change Buyer's Order</button>                       
                        <div class="d-flex align-items-baseline" v-else-if="newProduce && getHarvestDetails.produce_list && getHarvestDetails.produce_list.length > 0">
                          <h5 class="me-3">Select New Produce:</h5>
                          <select name="" id="" class="form-select" style="width:150px;" @change="setProduce($event)">
                            <option :value="produce.id" v-for="(produce, index) in getProduceList" :key="index">{{ produce.prod_name }}</option>
                          </select>
                        </div>                                    
                      </div>                                 
                      <div class="d-flex align-items-baseline w-50">                        
                        <h5 class="me-3">Quantity to be Given:</h5>
                        <!-- <p>{{ data.order_finalQty }}</p> -->
                        <input type="number" name="" id="" v-model="data.order_finalQty" @keyup="setQty($event, order)" min="1" @change="setQty($event, order)" onkeydown="return false" class="form-control" style="width:150px">                                                                                                        
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
                      <h5>Date of First Payment:</h5>
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
import { format } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "HarvestDetails",
    data(){
      return{
        confirm: null,
        errors: null,
        order_total: 0,
        id: null,
        maxPrice: null,
        newProduce: false,        
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
    },    
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
            var year = new Date().getFullYear()
            var month = parseInt(new Date().getMonth() + 1)
            var date = parseInt(new Date().getDate())
            this.data.produce_yield_dateHarvestFrom = format(new Date(year, month, date), 'yyyy-MM-dd')
            this.data.produce_yield_dateHarvestTo = format(new Date(year, month, date+1), 'yyyy-MM-dd')            
            this.confirm = false
          }
          this.data.project_id = this.$route.params.id
          if(this.getHarvestDetails.produce_list){
            if(this.getHarvestDetails.produce_list.length > 1){
              var arr = [];
              this.getHarvestDetails.produce_list.forEach((p) => {
                arr.push(p.id)
              })
              if(!this.getHarvestDetails.yield.length > 0){
                this.data.produce_trader_id = arr
              }                             
            }
            else{
              if(!this.getHarvestDetails.yield.length > 0){
                this.data.produce_trader_id = this.getHarvestDetails.produce_trader[0].id
              }                           
            }            
          }          
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
          this.data.order_finalPrice = parseFloat(order.order_negotiatedPrice).toFixed(2) 
          this.data.order_finalQty = qty[1]
          this.order_total = maxPrice
          this.id = order.id
          this.data.bid_order_acc_amount = remainingBal
          this.maxPrice = parseFloat(order.order_negotiatedPrice).toFixed(2)          
          this.data.produce_trader_id = order.produce_trader_id
        },
        initializeProduce(){
          this.newProduce = true
          this.data.produce_trader_id = this.getProduceList[0].id
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
            this.order_total = this.data.order_finalQty * this.data.order_finalPrice
            this.data.bid_order_acc_amount = this.order_total - order.order_dpAmount
            if(this.data.bid_order_acc_amount < 0){              
              var maxPrice = parseFloat(qty[1] * parseFloat(order.order_negotiatedPrice))
              var remainingBal = maxPrice - order.order_dpAmount
              console.log(parseFloat(remainingBal / qty[1]).toFixed(2))
              // this.data.order_finalPrice = parseFloat(remainingBal / qty[1]).toFixed(2)                             
              // this.data.order_finalQty = qty[1] 
              this.data.bid_order_acc_amount = 0    
              // this.order_total = order.order_dpAmount
            }
          }
          
        },
        setPrice(e, order){
          console.log(order)
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
            // this.data.order_finalPrice = parseFloat(remainingBal / qty[1]).toFixed(2)
            // this.data.order_finalQty = qty[1] 
            this.data.bid_order_acc_amount = 0 
            // this.order_total = order.order_dpAmount                                           
          }
        },
        setProduce(e){
          this.data.produce_trader_id = e.target.value
        },
        getName(order){
          var distObj = this.getHarvestDetails.distributors.filter((d) => {
            return parseInt(d.id) === parseInt(order.distributor_id)
          })
          return distObj[0].distributor_firstName + ' ' + distObj[0].distributor_lastName
        },
        getProduceName(order){
          var prodTraderObj = this.getHarvestDetails.produce_list.filter((p) => {
            return parseInt(p.id) === order.produce_trader_id
          })
          return prodTraderObj[0].prod_name
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
    mounted(){      
      this.$root.$on('bv::modal::hide', (bvEvent) => {      
        if(bvEvent.trigger == 'ok'){
          // this.$router.push({ path: `/delivery/${this.id}`, 
          // query: {
          //   finalPrice: this.data.order_finalPrice,
          //   finalQty: this.data.order_finalQty,
          //   produce: this.data.produce_trader_id
          // }})      
          ///bid/orderss/:id
          this.$router.push({ path: `/bid/orderss/${this.id}`, 
          query: {
            finalPrice: this.data.order_finalPrice,
            finalQty: this.data.order_finalQty,
            produce: this.data.produce_trader_id
          }})                                                  
        }
        else if(bvEvent.trigger == 'headerclose'){
          this.newProduce = false          
          // this.data.produce_trader_id = null
          this.data.order_finalPrice = 0
          this.data.order_finalQty = 0
          this.data.bid_order_acc_amount = 0
          this.order_total = 0
          this.data.produce_trader_id = null
        }
        // this.id = null                
      })              
    },    
    computed: {
      ...mapGetters(['getHarvestDetails']),
      getProduceList(){
        var bidOrderObj = this.getHarvestDetails.bid_orders.filter((b) => { // start layouting for harvest inventory per project
          return parseInt(b.id) === parseInt(this.id)
        })
        var prodList = this.getHarvestDetails.produce_list.filter((p) => {
          return parseInt(p.id) !== parseInt(bidOrderObj[0].produce_trader_id)
        })                  
        return prodList
      }       
    },

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
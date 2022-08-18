<template>
  <div class="bidOrderHistory h-100 w-100">
    <div class="container-fluid w-100 d-flex pe-5 pt-3 justify-content-between align-items-center" style="height:10%;">
        <h3>My Orders</h3>
    </div>
    <div class="container-fluid m-0 pe-3 pt-4" style="width:100%; height:90%; overflow-y: scroll;">
      <table width="95%" style="margin: 0 auto; border-collapse: collapse; border-spacing:0;">
        <thead>
          <tr>
            <th>Produce</th>
            <th>Bid</th>
            <th>Quantity (in kgs)</th>
            <th>Status</th>
            <th>Trader's Price (php)</th>
            <th>Asking Price (php)</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>           
          <tr v-for="(order, index) in getOrderHistory.orders" :key="index">
            <td style="border-left: 2px solid black">{{ getProduceName(order) }}</td>
            <td>{{ getBidType(order) }}</td>
            <td>{{ getQty(order) }}</td>
            <td>{{ getStatus(order) }}</td>
            <td>{{ getTraderPrice(order) }}</td>
            <td v-if="order.order_initialPrice">{{ order.order_initialPrice.toFixed(2) }}</td>
            <td>{{ getTotal(order) }}</td>
            <td style="border-right: 2px solid black; text-align:right"><button class="btn btn-success" @click="triggerModal(order)">View Details</button></td>
            <b-modal no-close-on-esc no-close-on-backdrop size="lg" centered :id="`modal-${order.id}`" :title="getProduceName(order)" hide-footer>
              <div style="width:100%; position:relative" class="p-1">
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;">              
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
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;">              
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Status:</h5>
                    <p>{{ getStatus(order) }}</p> 
                  </div>                                 
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Expected Date of Harvest:</h5>
                      <p>{{ getHarvestDate(order) }}</p> 
                    </div>                  
                  </div>
                </div>              
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;">              
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Trader Name:</h5>
                    <p>{{ getTraderName(order) }}</p> 
                  </div>                                 
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Email Address:</h5>
                      <p>{{ getTraderEmail(order) }}</p> 
                    </div>                  
                  </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;">              
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Contact Number:</h5>
                    <p>{{ getContactNum(order) }}</p> 
                  </div>                                 
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Trader's Price:</h5>
                      <p>{{ getTraderPrice(order) }}</p> 
                    </div>                  
                  </div>
                </div>
                <div :class="order.bid_order_status_id != 4 ? 'd-flex align-items-baseline' : 'd-flex align-items-baseline justify-content-between'">
                  <div v-if="order.bid_order_status_id == 4" class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Farm Originated:</h5>
                    <p>{{ getFarmName(order) }}</p> 
                  </div>                 
                  <div v-if="order.bid_order_status_id == 4" class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Date Refundable (100%):</h5>
                    <p>{{ getRefundDate(order, 100) }}</p> 
                  </div>                 
                  <h5 v-if="order.bid_order_status_id != 4" class="me-3">Farm Originated:</h5>
                  <p v-if="order.bid_order_status_id != 4">{{ getFarmName(order) }}</p>                
                </div>
                <div :class="order.bid_order_status_id != 4 ? 'd-flex align-items-baseline' : 'd-flex align-items-baseline justify-content-between'">
                  <div v-if="order.bid_order_status_id == 4" class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Estimated Harvest:</h5>
                    <p>{{ getHarvest(order) + ' kgs' }}</p> 
                  </div>                 
                  <div v-if="order.bid_order_status_id == 4" class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Date Refundable (50%):</h5>
                    <p>{{ getRefundDate(order, 50) }}</p> 
                  </div>                
                  <h5 v-if="order.bid_order_status_id != 4" class="me-3">Estimated Harvest:</h5>
                  <p v-if="order.bid_order_status_id != 4">{{ getHarvest(order) + ' kgs' }}</p>                
                </div>
                <div class="d-flex align-items-baseline mt-3">
                  <h5 class="me-3">Expected Dates Needed:</h5>
                  <p>{{ order.order_dateNeededFrom + ' - ' + order.order_dateNeededTo }}</p>                
                </div>
                <div class="d-flex align-items-baseline justify-content-between mt-4" style="width:100%;">              
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Asking Price:</h5>
                    <p>{{ order.order_initialPrice.toFixed(2) }}</p> 
                  </div>                                 
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Quantity Needed:</h5>
                      <p>{{ getQty(order) + ' kgs' }}</p> 
                    </div>                  
                  </div>
                </div>              
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;">              
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Minimum Price:</h5>
                    <p>{{ getMinPrice(order) }}</p> 
                  </div>                                 
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Maximum Price:</h5>
                      <p>{{ getMaxPrice(order) }}</p> 
                    </div>                  
                  </div>
                </div> 
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;" v-if="order.bid_order_status_id != 1">              
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Negotiated Price:</h5>
                    <p>{{ order.order_negotiatedPrice.toFixed(2) }}</p> 
                  </div>                                 
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">First Payment:</h5>
                      <p>{{ order.order_dpAmount.toFixed(2) + ` (${getPercentage(order)}%)` }}</p> 
                    </div>                  
                  </div>
                </div>
                <div class="d-flex align-items-baseline" v-if="order.bid_order_status_id != 1">
                  <h5 class="me-3">Due Date of First Payment:</h5>
                  <p>{{ order.order_dpDueDate }}</p>                
                </div>                 
                <div class="d-flex align-items-center justify-content-end" style="position:absolute; bottom:0; right:5%;" v-if="order.bid_order_status_id != 1">     
                  <button class="btn btn-success" @click="setStatus(order)">{{ order.bid_order_status_id == 2 ? 'Confirm' : order.bid_order_status_id == 4 ? 'Refund Advanced Order' : 'Pay Amount' }}</button>
                </div> 
                <div class="d-flex align-items-baseline justify-content-between mt-3" style="width:100%;" v-if="order.bid_order_status_id > 2">              
                  <div :class="order.bid_order_status_id == 4 ? 'd-flex align-items-baseline w-50' : 'd-flex align-items-baseline justify-content-between w-50'">
                    <h5 class="me-3">Mode of Payment:</h5>
                    <select v-if="order.bid_order_status_id != 4" name="bid_order_acc_paymentMethod" id="" class="form-select ms-2" style="width:150px" @change="setPaymentMethod($event)">
                      <option value="Cash" selected>Cash</option>
                      <option value="Bank">Bank</option>
                    </select>
                    <p v-else>{{ getPaymentMethod(order) }}</p> 
                  </div>                                 
                  <div class="d-flex align-items-baseline w-50 ps-5">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Amount:</h5>
                      <p>{{ order.order_dpAmount.toFixed(2) + ` (${getPercentage(order)}%)` }}</p> 
                    </div>                  
                  </div>
                </div>
                <div v-if="order.bid_order_status_id == 4 && getPaymentMethod(order) == 'Bank'">
                  <div class="d-flex align-items-baseline w-50">
                      <h5 class="me-3">Bank Name:</h5>
                      <p>{{ getBankName(order) }}</p>                  
                  </div>
                  <div class="d-flex align-items-baseline w-50">
                      <h5 class="me-3">Account Number:</h5>
                      <p>{{ getAccNum(order) }}</p>                  
                  </div>
                  <div class="d-flex align-items-baseline w-50">
                      <h5 class="me-3">Account Name:</h5>
                      <p>{{ getAccName(order) }}</p>                  
                  </div>                  
                </div>                                
                <div v-if="payment_data.bid_order_acc_paymentMethod == 'Bank' && order.bid_order_status_id == 3">
                  <div class="d-flex align-items-baseline justify-content-between w-50">
                    <h5 class="me-3">Bank Name:</h5>
                    <input type="text" name="bid_order_acc_bankName" id="" class="form-control" style="width:150px" v-model="payment_data.bid_order_acc_bankName">              
                  </div>                  
                  <div class="d-flex align-items-baseline justify-content-between w-50">
                    <h5 class="me-3">Account Number:</h5>
                    <input type="text" name="bid_order_acc_accNum" id="" class="form-control" style="width:150px" v-model="payment_data.bid_order_acc_accNum">              
                  </div>                  
                  <div class="d-flex align-items-baseline justify-content-between w-50">
                    <h5 class="me-3">Account Name:</h5>
                    <input type="text" name="bid_order_acc_accName" id="" class="form-control" style="width:150px" v-model="payment_data.bid_order_acc_accName">              
                  </div>                  
                </div>
                <div v-if="order.bid_order_status_id > 2" :class="order.bid_order_status_id == 4 ? 'd-flex align-items-baseline w-50' : 'd-flex align-items-baseline justify-content-between w-50'">
                  <h5 class="me-3">Date Paid:</h5>
                  <input v-if="order.bid_order_status_id == 3" type="date" name="bid_order_acc_datePaid" id="" onkeydown="return false" class="form-control" style="width:150px" v-model="payment_data.bid_order_acc_datePaid">              
                  <p v-else>{{ getDatePaid(order) }}</p> 
                </div>                                            
              </div>            
            </b-modal>            
          </tr>   
        </tbody>
      </table>
    </div>      
  </div>
</template>

<script>
import { add, format, sub } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
import auth from '../../../store/modules/Auth/auth'
export default {
    name: 'BidOrderHistory',
    created(){      
      this.fetchBidHistory(auth.state.user.email)
      .then(() => {
        this.readyApp()
      })
    },
    data(){
      return {
        payment_data: {
          id: null,
          bid_order_acc_paymentMethod: 'Cash',
          bid_order_acc_type: null,
          bid_order_acc_amount: null,
          bid_order_acc_bankName: null,
          bid_order_acc_accNum: null,
          bid_order_acc_accName: null,
          bid_order_acc_remarks: null,
          bid_order_acc_datePaid: new Date().toISOString().split('T')[0]          
        },
        errors: null
      }
    },
    mounted(){
      this.$root.$on('bv::modal::hide', (bvEvent) => {              
        if(bvEvent.trigger == 'headerclose'){                  
          this.payment_data.bid_order_acc_paymentMethod = 'Cash'
          this.payment_data.bid_order_acc_bankName = null
          this.payment_data.bid_order_acc_accNum = null
          this.payment_data.bid_order_acc_accName = null         
        }                 
      })      
    },
    methods:{
        ...mapActions(['readyApp', 'fetchBidHistory', 'updateStatus', 'firstPayment']),
        getProduceName(order){             
          var projectObj = this.getOrderHistory.projects.filter((p) => {          
            return parseInt(order.project_id) === parseInt(p.id)
          })             
          var contractObj = this.getOrderHistory.contracts.filter((c) => {
            return parseInt(projectObj[0].contract_id) === parseInt(c.id)
          })
          var prodObj = this.getOrderHistory.produces.filter((p) => {
            return parseInt(p.id) === parseInt(contractObj[0].produce_trader_id)
          })
          return prodObj[0].prod_name
        },
        getBidType(order){
          var projectObj = this.getOrderHistory.projects.filter((p) => {
            return parseInt(order.project_id) === parseInt(p.id)
          })
          var contractObj = this.getOrderHistory.contracts.filter((c) => {
            return parseInt(projectObj[0].contract_id) === parseInt(c.id)
          })          
          var string = null  
          if(this.getOrderHistory.project_bids.length > 0){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
              return parseInt(order.id) === parseInt(p.bid_order_id)
            })
            if(projectBidObj[0]){
              string = contractObj[0].farm_name + ' Project'
            }            
          } 
          else if(this.getOrderHistory.on_hand_bids.length > 0){
            var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
              return parseInt(order.id) === parseInt(o.bid_order_id)
            })
            if(onHandBidObj[0]){
              string = contractObj[0].farm_name + ' On-Hand'           
            }            
          }      
          return string       
        },
        getQty(order){
          if(this.getOrderHistory.project_bids.length > 0){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
                return parseInt(order.id) === parseInt(p.bid_order_id)
              })
            if(projectBidObj[0]){
              return projectBidObj[0].project_bid_minQty + ' - ' + projectBidObj[0].project_bid_maxQty
            }            
          }
          else if(this.getOrderHistory.on_hand_bids.length > 0){
            var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
              return parseInt(order.id) === parseInt(o.bid_order_id)
            })
            if(onHandBidObj[0]){
              return onHandBidObj[0].on_hand_bid_qty
            }            
          }
        },
        getStatus(order){
            if(order.bid_order_status_id == 1){
                return 'For Approval'
            }
            else if(order.bid_order_status_id == 2){
                return 'To Be Confirmed by Distributor'
            }
            else if(order.bid_order_status_id == 3){
                return 'First Payment Pending'
            }
            else if(order.bid_order_status_id == 4){
                return 'First Payment Paid'
            }
            else if(order.bid_order_status_id == 5){
                return 'To be Delivered'
            }
            else if(order.bid_order_status_id == 6){
                return 'Confirm Final Payment'
            }
            else if(order.bid_order_status_id == 7){
                return 'Return for Refund Pending'
            }
            else if(order.bid_order_status_id == 8){
                return 'Confirmation for Refund'
            }          
        },
        getTraderPrice(order){
          var projectObj = this.getOrderHistory.projects.filter((p) => {
            return parseInt(order.project_id) === parseInt(p.id)
          })
          var contractObj = this.getOrderHistory.contracts.filter((c) => {
            return parseInt(projectObj[0].contract_id) === parseInt(c.id)
          })
          return contractObj[0].contract_estimatedPrice.toFixed(2)          
        },        
        getTotal(order){
          if(this.getOrderHistory.project_bids.length > 0){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
              return parseInt(order.id) === parseInt(p.bid_order_id)
            })
            if(projectBidObj[0]){
              return projectBidObj[0].project_bid_total
            }            
          }
          else if(this.getOrderHistory.on_hand_bids.length > 0){
            var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
              return parseInt(order.id) === parseInt(o.bid_order_id)
            }) 
            if(onHandBidObj[0]){
              return onHandBidObj[0].on_hand_total.toFixed(2)
            }                       
          }
        },
        getHarvest(order){
          var projectObj = this.getOrderHistory.projects.filter((p) => {
            return parseInt(order.project_id) === parseInt(p.id)
          })
          var contractObj = this.getOrderHistory.contracts.filter((c) => {
            return parseInt(projectObj[0].contract_id) === parseInt(c.id)
          })
          return contractObj[0].contract_estimatedHarvest.toFixed(2)    
        },
        getHarvestDate(order) {    
          var projectObj = this.getOrderHistory.projects.filter((p) => {
            return parseInt(order.project_id) === parseInt(p.id)
          })                  
          if(projectObj[0].project_harvestableEnd){                   
              return projectObj[0].project_harvestableEnd
          }
          else {
              var harvestDate = add(new Date(projectObj[0].project_commenceDate), {
                  weeks: 1
              })
              var formattedDate = format(harvestDate, 'yyyy-MM-dd');       
              return formattedDate
          }                        
        },
        getTraderName(order){
          var traderObj = this.getOrderHistory.traders.filter((t) => {
            return parseInt(order.trader_id) === parseInt(t.id)
          })
          return traderObj[0].trader_firstName + ' ' + traderObj[0].trader_lastName
        },
        getTraderEmail(order){
          var traderObj = this.getOrderHistory.traders.filter((t) => {
            return parseInt(order.trader_id) === parseInt(t.id)
          })
          return traderObj[0].trader_email 
        },
        getContactNum(order){
          var traderObj = this.getOrderHistory.traders.filter((t) => {
            return parseInt(order.trader_id) === parseInt(t.id)
          })
          var traderContactNumObj = null        
          this.getOrderHistory.trader_contactNums.forEach((n) => {
            traderContactNumObj = n.filter((nn) => {
              return parseInt(traderObj[0].id) === parseInt(nn.trader_id)
            })          
          })          
          return traderContactNumObj[0].trader_contactNum
        },
        getFarmName(order){
          var projectObj = this.getOrderHistory.projects.filter((p) => {
            return parseInt(order.project_id) === parseInt(p.id)
          })
          var contractObj = this.getOrderHistory.contracts.filter((c) => {
            return parseInt(projectObj[0].contract_id) === parseInt(c.id)
          })
          return contractObj[0].farm_name          
        },  
        getMinPrice(order){
          if(this.getOrderHistory.project_bids.length > 0){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
                return parseInt(order.id) === parseInt(p.bid_order_id)
              })
            if(projectBidObj[0]){
              var minPrice = parseFloat(projectBidObj[0].project_bid_total.split(' - ')[0]).toFixed(2)
              return minPrice
            }            
          }          
        },        
        getMaxPrice(order){
          if(this.getOrderHistory.project_bids.length > 0){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
                return parseInt(order.id) === parseInt(p.bid_order_id)
              })
            if(projectBidObj[0]){
              var maxPrice = parseFloat(projectBidObj[0].project_bid_total.split(' - ')[1]).toFixed(2)
              return maxPrice
            }            
          }           
        },
        getPercentage(order){
          var total = null
          if(this.getOrderHistory.project_bids.length > 0){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
              return parseInt(order.id) === parseInt(p.bid_order_id)
            })
            if(projectBidObj[0]){
              total = parseFloat(parseFloat(order.order_negotiatedPrice) * parseFloat(projectBidObj[0].project_bid_maxQty))         
            }            
          }
          else if(this.getOrderHistory.on_hand_bids.length > 0){
            var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
              return parseInt(order.id) === parseInt(o.bid_order_id)
            }) 
            if(onHandBidObj[0]){
              total = parseFloat(parseFloat(order.order_negotiatedPrice) * parseFloat(onHandBidObj[0].on_hand_bid_qty))         
            }                       
          }          
          return parseFloat(parseFloat(order.order_dpAmount) / parseFloat(total)) * 100
        },
        triggerModal(order){
          this.$bvModal.show(`modal-${order.id}`);
        },
        setStatus(order){       
          if(order.bid_order_status_id == 2){
            this.updateStatus(order.id)
            .then(() => {
              setTimeout(() => {
                this.$toastr.s('Status Updated!')
              }, 5000)
              location.reload();
            })
            .catch((err) => {
              console.error(err)
            })
          } 
          else if(order.bid_order_status_id == 3){
            this.payment_data.id = order.id
            this.payment_data.bid_order_acc_amount = order.order_dpAmount.toFixed(2)
            this.payment_data.bid_order_acc_type = 'First Payment'
            this.firstPayment(this.payment_data)
            .then(() => {
              setTimeout(() => {
                this.$toastr.s('First Payment Delivered!')
              }, 5000)              
              location.reload()
            })
            .catch((err) => {
              console.log(err)
              this.errors = err.response.data.errors
              for(var error in this.errors){
                this.$toastr.e(this.errors[error][0])
              }              
            })
          }            

        },
        setPaymentMethod(e){
          this.payment_data.bid_order_acc_paymentMethod = e.target.value
          if(e.target.value == 'Cash'){
            this.payment_data.bid_order_acc_bankName = null
            this.payment_data.bid_order_acc_accNum = null
            this.payment_data.bid_order_acc_accName = null
          }
        },
        getPaymentMethod(order){
          console.log(order)
          var bidOrderAccObj = this.getOrderHistory.bid_order_accs.filter((o) => {
            if(o){
              return parseInt(order.id) === parseInt(o.bid_order_id);
            }
          })                   
          return bidOrderAccObj[0].bid_order_acc_paymentMethod
        },
        getDatePaid(order){
          var bidOrderAccObj = this.getOrderHistory.bid_order_accs.filter((o) => {
            if(o){
              return parseInt(order.id) === parseInt(o.bid_order_id);
            }
          })
          return bidOrderAccObj[0].bid_order_acc_datePaid   
        },
        getRefundDate(order, percentage){
          var date = null
          var formattedDate = null
          var harvestDate = this.getHarvestDate(order);  
          if(percentage == 100){
            date = sub(new Date(harvestDate), {
              months: 1
            })
            formattedDate = format(date, 'yyyy-MM-dd');       
          }
          else{
            date = sub(new Date(harvestDate), {
              weeks: 2
            })
            formattedDate = format(date, 'yyyy-MM-dd');
          }
          return formattedDate  
        },
        getBankName(order){
          var bidOrderAccObj = this.getOrderHistory.bid_order_accs.filter((o) => {
            if(o){
              return parseInt(order.id) === parseInt(o.bid_order_id);
            }
          })
          return bidOrderAccObj[0].bid_order_acc_bankName           
        },
        getAccNum(order){
          var bidOrderAccObj = this.getOrderHistory.bid_order_accs.filter((o) => {
            if(o){
              return parseInt(order.id) === parseInt(o.bid_order_id);
            }
          })
          return bidOrderAccObj[0].bid_order_acc_accNum         
        },
        getAccName(order){
          var bidOrderAccObj = this.getOrderHistory.bid_order_accs.filter((o) => {
            if(o){
              return parseInt(order.id) === parseInt(o.bid_order_id);
            }
          })
          return bidOrderAccObj[0].bid_order_acc_accName      
        },
    },
    computed: {
      ...mapGetters(['getOrderHistory'])
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
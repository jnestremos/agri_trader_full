<template>
  <div class="bidOrderHistory h-100 w-100">
    <div class="container-fluid w-100 d-flex pe-5 pt-3 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>My Orders</h3>
    </div>
    <div class="container-fluid m-0 pe-3 pt-4" style="width:100%; height:90%; overflow-y: scroll;">
      <table width="95%" style="margin: 0 auto; border-collapse: collapse; border-spacing:0;">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Produce</th>
            <th>Bid</th>
            <th>Quantity (in kgs)</th>
            <th>Status</th>
            <th>Trader's Price</th>
            <th>Asking Price</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(order, index) in getOrderHistory.orders" :key="index">
            <td style="border-left: 2px solid black">{{ order.id }}</td>
            <td>{{ getProduceName(order) }}</td>
            <td>{{ getBidType(order) }}</td>
            <td>{{ getQty(order) }}</td>
            <td>{{ getStatus(order) }}</td>
            <td>{{ getTraderPrice(order) }}</td>
            <td v-if="order.order_initialPrice">{{ order.order_initialPrice.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' }) }}</td>
            <td>{{ getTotal(order) }}</td>
            <td style="border-right: 2px solid black; text-align:right"><button class="btn btn-success" @click="triggerModal(order)">View Details</button></td>
            <b-modal no-close-on-esc no-close-on-backdrop size="lg" centered :id="`modal-${order.id}`" :title="`Order #${order.id} for ` + getProduceName(order)" hide-footer>
              <div style="width:100%; position:relative" class="p-1">
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;">
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Date Placed:</h5>
                    <p>{{ getCreatedDate(order) }}</p>
                  </div>
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Date Updated:</h5>
                      <p>{{ getUpdatedDate(order) }}</p>
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
                      <h5 class="me-3">{{ isProjectOrOnHand(order) == 'Project' ? 'Expected Date of Harvest:' : 'Last Date of Harvest:' }}</h5>
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
                      <p>{{ getTraderPrice(order) + ' per kg' }}</p>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;" v-if="order.bid_order_status_id != 4 && isProjectOrOnHand(order) == 'Project'">
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Estimated Harvest:</h5>
                    <p>{{ getHarvest(order) + ' kgs' }}</p>
                  </div>
                </div>
                <div :class="order.bid_order_status_id != 4 ? 'd-flex align-items-baseline' : 'd-flex align-items-baseline justify-content-between'">
                  <div v-if="order.bid_order_status_id == 4" class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Farm Originated:</h5>
                    <p>{{ getFarmName(order) }}</p>
                  </div>
                  <div v-if="order.bid_order_status_id == 4 && isProjectOrOnHand(order) == 'Project'" class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Date Refundable (100%):</h5>
                    <p>{{ getRefundDate(order, 100) }}</p>
                  </div>
                  <h5 v-if="order.bid_order_status_id != 4" class="me-3">Farm Originated:</h5>
                  <p v-if="order.bid_order_status_id != 4">{{ getFarmName(order) }}</p>
                </div>
                <div :class="order.bid_order_status_id != 4 ? 'd-flex align-items-baseline' : 'd-flex align-items-baseline justify-content-between'">
                  <div v-if="order.bid_order_status_id == 4 && isProjectOrOnHand(order) == 'Project'" class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Estimated Harvest:</h5>
                    <p>{{ getHarvest(order) + ' kgs' }}</p>
                  </div>
                  <div v-if="order.bid_order_status_id == 4" class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Date Refundable (50%):</h5>
                    <p>{{ getRefundDate(order, 50) }}</p>
                  </div>
                  <h5 v-if="order.bid_order_status_id != 4 && isProjectOrOnHand == 'Project'" class="me-3">Estimated Harvest:</h5>
                  <p v-if="order.bid_order_status_id != 4 && isProjectOrOnHand == 'Project'">{{ getHarvest(order) + ' kgs' }}</p>
                </div>
                <div class="d-flex align-items-baseline mt-3">
                  <h5 class="me-3">Expected Dates Needed:</h5>
                  <p>{{ getDatesNeeded(order) }}</p>
                </div>
                <div class="d-flex align-items-baseline justify-content-between mt-4" style="width:100%;">
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Distributor's Initial Price:</h5>
                    <p>{{ order.order_initialPrice.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' }) + ' per kg'}}</p>
                  </div>
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">{{ order.order_finalPrice ? 'Quantity to be Delivered' : 'Quantity Needed' }}:</h5>
                      <p>{{ getQty(order) + ' kg/s' }}</p>
                    </div>
                  </div>
                </div>
                <div v-if="order.bid_order_status_id != 5" class="d-flex align-items-baseline justify-content-between" style="width:100%;">
                  <div class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Total Initial Price:</h5>
                      <p>{{ getInitTotal(order) }}</p>
                    </div>
                  </div>
                  <div v-if="order.bid_order_status_id != 1" class="d-flex align-items-baseline w-50">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">Total Negotiated Price:</h5>
                      <p>{{ getTotal(order) }}</p>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;" v-if="order.bid_order_status_id != 1">
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Negotiated Price:</h5>
                    <p>{{ order.order_negotiatedPrice.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' }) + ' per kg' }}</p>
                  </div>
                  <div class="d-flex align-items-baseline w-50" :style="order.bid_order_status_id == 2 ? {'margin-bottom':'40px'} : '' ">
                    <div class="d-flex align-items-baseline">
                      <h5 class="me-3">First Payment:</h5>
                      <p>{{ order.order_dpAmount.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' }) + ` (${getPercentage(order)}%)` }}</p>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-baseline" v-if="order.bid_order_status_id != 1 && (order.bid_order_status_id == 3 && getDpDueDate(order) == order.order_dpDueDate)">
                  <h5 class="me-3">{{ getDpDueDate(order) != order.order_dpDueDate ? 'Date of First Payment:' : 'Due Date of First Payment:' }}</h5>
                  <p>{{ dpDueDateFormat(getDpDueDate(order)) }}</p>
                </div>
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;" v-if="order.bid_order_status_id == 5">
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Final Negotiated Price:</h5>
                    <p>{{ order.order_finalPrice.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' }) + ' per kg' }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between" style="width:100%;" v-if="order.bid_order_status_id == 5 || order.bid_order_status_id == 6">
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Delivery Dispatch Date:</h5>
                    <p>{{ getDateDispatch(order) }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-end" style="position:absolute; bottom:0; right:5%;" v-if="order.bid_order_status_id != 1 && order.bid_order_status_id != 6">
                  <button class="btn btn-secondary me-3" v-if="order.bid_order_status_id != 1 && order.bid_order_status_id != 3 && order.bid_order_status_id != 4 && order.bid_order_status_id != 5 && order.bid_order_status_id != 7" @click="cancelTransaction(order)">Cancel</button>
                  <button class="btn btn-success me-3" v-if="!hasRefundRequest(order) && ((isProjectOrOnHand(order) == 'Project' && order.bid_order_status_id == 4 && payment_data.refund_numOfDays >= 14) || (isProjectOrOnHand(order) == 'Project' && isDateAfterHarvest(order))) || order.bid_order_status_id == 5" @click="refundOrder(order)">Refund</button>
                  <button class="btn btn-success" v-if="getDpDueDate(order) == order.order_dpDueDate || order.bid_order_status_id == 5 || order.bid_order_status_id == 8" @click="setStatus(order)">{{ order.bid_order_status_id == 2 || order.bid_order_status_id == 7 || order.bid_order_status_id == 8 ? 'Confirm' : order.bid_order_status_id == 4 ? 'Refund Advanced Order' : 'Pay Amount' }}</button>
                </div>
                <div class="d-flex align-items-baseline justify-content-between mt-3" style="width:100%;" v-if="order.bid_order_status_id > 2">
                  <div :class="order.bid_order_status_id == 4 || (order.bid_order_status_id == 3 && getDpDueDate(order) != order.order_dpDueDate) || order.bid_order_status_id == 6 || order.bid_order_status_id == 7 || order.bid_order_status_id == 8 ? 'd-flex align-items-baseline w-50' : 'd-flex align-items-baseline justify-content-between w-50'">
                    <h5 class="me-3">Mode of Payment:</h5>
                    <select v-if="((order.bid_order_status_id == 3 && getDpDueDate(order) == order.order_dpDueDate) || order.bid_order_status_id == 5)" name="bid_order_acc_paymentMethod" id="" class="form-select ms-2" style="width:150px" @change="setPaymentMethod($event)">
                      <option value="Cash" selected>Cash</option>
                      <option value="Bank">Bank</option>
                    </select>
                    <p v-else>{{ getPaymentMethod(order) }}</p>
                  </div>
                  <div :class="(order.bid_order_status_id == 3 && getDpDueDate(order) == order.order_dpDueDate) || order.bid_order_status_id == 5 ? 'd-flex align-items-baseline w-50 ps-5' : 'd-flex align-items-baseline w-50'">
                    <div v-if="order.bid_order_status_id != 8" class="d-flex align-items-baseline">
                      <h5 class="me-3">Amount:</h5>
                      <!-- <p>{{ order.order_finalPrice ?
                      getRemainingBalance(order).toFixed(2)
                      : order.order_dpAmount.toFixed(2) + ` (${getPercentage(order)}%)` }}</p>  -->
                      <p>{{ order.order_finalPrice && order.bid_order_status_id == 5 ?
                      getRemainingBalance(order).toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
                      : order.order_dpAmount.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' }) }}</p>
                    </div>
                    <div v-else class="d-flex align-items-baseline">
                      <h5 class="me-3">Amount to Refunded:</h5>
                      <p>{{ getRefundAmount(order) }}</p>
                    </div>
                  </div>
                </div>
                <div v-if="((order.bid_order_status_id == 4 || order.bid_order_status_id == 6) && (order.bid_order_status_id == 3 && getDpDueDate(order) != order.order_dpDueDate) || (getPaymentMethod(order) == 'Bank' && order.bid_order_status_id != 5))">
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
                <div class="d-flex align-items-baseline justify-content-between w-100" v-else-if="((order.bid_order_status_id == 4 || order.bid_order_status_id == 6) && (order.bid_order_status_id == 3 && getDpDueDate(order) != order.order_dpDueDate) || (getPaymentMethod(order) == 'Cash' && order.bid_order_status_id != 5))">
                  <div class="d-flex align-items-baseline w-50">
                      <h5 class="me-3">Payer Name:</h5>
                      <p>{{ getAccName(order) }}</p>
                  </div>
                  <div v-if="hasRefundRequest(order)" class="d-flex align-items-baseline w-50">
                      <h5 class="me-3">Date of Refund Request:</h5>
                      <p>{{ getRefundDatePlaced(order) }}</p>
                  </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between w-100 mb-3" v-if="order.bid_order_status_id == 8 && getPaymentType(order) == 'Refund'">
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Received By:</h5>
                    <input type="text" name="refund_receivedBy" id="" class="form-control" style="width:150px" v-model="payment_data.refund_receivedBy">
                  </div>
                  <div class="d-flex align-items-baseline w-50">
                    <h5 class="me-3">Contact Num:</h5>
                    <input type="text" name="refund_contactNum" id="" class="form-control" style="width:150px" v-model="payment_data.refund_contactNum">
                  </div>
                </div>
                <div v-if="payment_data.bid_order_acc_paymentMethod == 'Bank' && (order.bid_order_status_id == 3 || order.bid_order_status_id == 5)">
                  <div class="d-flex align-items-baseline justify-content-between w-50">
                    <h5 class="me-3">Bank Name:</h5>
                    <input type="text" name="bid_order_acc_bankName" id="" class="form-control" style="width:150px" v-model="payment_data.bid_order_acc_bankName">
                  </div>
                  <div class="d-flex align-items-baseline justify-content-between w-50">
                    <h5 class="me-3">Account Number:</h5>
                    <input type="text" name="bid_order_acc_accNum" id="" class="form-control" style="width:150px" v-model="payment_data.bid_order_acc_accNum">
                  </div>
                </div>
                <div v-if="(order.bid_order_status_id == 3 && getDpDueDate(order) == order.order_dpDueDate) || order.bid_order_status_id == 5">
                  <div class="d-flex align-items-baseline justify-content-between w-100">
                    <div class="d-flex align-items-baseline justify-content-between w-50">
                      <h5 class="me-3">Account Name:</h5>
                      <input type="text" name="bid_order_acc_accName" id="" class="form-control" style="width:150px" v-model="payment_data.bid_order_acc_accName">
                    </div>
                    <div class="d-flex align-items-baseline w-50 ps-5" v-if="order.bid_order_status_id == 5">
                      <h5 class="me-3">Received By:</h5>
                      <input type="text" name="delivery_receivedBy" id="" class="form-control" style="width:150px" v-model="payment_data.delivery_receivedBy">
                    </div>
                  </div>
                  <div class="d-flex align-items-baseline justify-content-between w-100 mt-3" v-if="order.bid_order_status_id == 5">
                    <div class="d-flex align-items-baseline w-50">
                    </div>
                    <div class="d-flex align-items-baseline w-50 ps-5">
                      <h5 class="me-2">Contact Num:</h5>
                      <input type="text" name="delivery_contactNum" id="" class="form-control" style="width:150px" v-model="payment_data.delivery_contactNum">
                    </div>
                  </div>
                </div>
                <!-- <div v-else-if="order.bid_order_status_id != 1 && order.bid_order_status_id != 2">
                  <div class="d-flex align-items-baseline justify-content-between w-100">
                    <div class="d-flex align-items-baseline w-50">
                      <h5 class="me-3">Account Name:</h5>
                      <p>{{ getAccName(order) }}</p>
                    </div>
                  </div>
                </div>                                                 -->
                <div v-if="order.bid_order_status_id > 2" :class="(order.bid_order_status_id == 3 && getDpDueDate(order) == order.order_dpDueDate) || order.bid_order_status_id == 5 ? 'd-flex align-items-baseline justify-content-between w-50' : (order.bid_order_status_id == 4 || order.bid_order_status_id == 6) ? 'd-flex align-items-baseline w-50' : 'd-flex align-items-baseline w-50'">
                  <h5 class="me-3">Date Paid:</h5>
                  <input v-if="(order.bid_order_status_id == 3 && getDpDueDate(order) == order.order_dpDueDate) || order.bid_order_status_id == 5" type="date" class="form-control" style="width:150px;" v-model="payment_data.bid_order_acc_datePaid">
                  <p v-else>{{ getDatePaid(order) }}</p>
                  <!-- <div v-if="order.bid_order_status_id == 7 && hasRefundRequest(order)">

                  </div> -->
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
import { add, format, sub, eachDayOfInterval, isAfter, isEqual } from 'date-fns'
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
          bid_order_acc_datePaid: format(new Date(), 'yyyy-MM-dd'),
          delivery_receivedBy: null,
          delivery_contactNum: null,
          bid_type: null,
          refund_numOfDays: null,
          refund_percentage: null,
          refund_amount: null,
          refund_receivedBy: null,
          refund_contactNum: null,
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
          this.payment_data.delivery_receivedBy = null
          this.payment_data.delivery_contactNum = null
        }
      })
    },
    methods:{
        ...mapActions([
          'readyApp',
          'fetchBidHistory',
          'updateProjectStatus',
          'updateOnHandStatus',
          'firstPayment',
          'finalPayment',
          'requestRefund',
          'confirmRefund',
          'cancelPaymentOrOrder'
        ]),
        cancelTransaction(order){
          var data;
          if(this.isProjectOrOnHand(order) == 'Project'){
            data = {
              id: order.id,
              bid_type: 'Project Bid'
            }
          }
          else if(this.isProjectOrOnHand(order) == 'OnHand'){
            data = {
              id: order.id,
              bid_type: 'On Hand Bid'
            }
          }
          this.cancelPaymentOrOrder(data)
          .then(() => {
            location.reload()
          })
        },
        getProduceName(order){
          var prodTraderObj = this.getOrderHistory.produce_trader.filter((p) => {
            return parseInt(p.id) === parseInt(order.produce_trader_id)
          })
          var prodObj = this.getOrderHistory.produces.filter((pp) => {
            return parseInt(prodTraderObj[0].produce_id) === parseInt(pp.id)
          })
          console.log(prodObj)
          var arr = prodTraderObj[0].prod_name.split('(Class')
          if(arr.indexOf('(Class') != -1){
            arr.splice(arr.indexOf('(Class'), 0, prodObj[0].prod_type)
            return arr.join(' ')
          }
          else{
            return prodTraderObj[0].prod_name + ' ' + prodObj[0].prod_type
          }
        },
        isProjectOrOnHand(order){
          var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
            return parseInt(order.id) === parseInt(p.bid_order_id)
          })
          var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
            return parseInt(order.id) === parseInt(o.bid_order_id)
          })
          if(projectBidObj[0]){
            return 'Project'
          }
          else if(onHandBidObj[0]){
            return 'OnHand'
          }
        },
        getCreatedDate(order){
          return format(new Date(order.created_at.split('T')[0]), 'MMM. dd, yyyy')
        },
        getDatesNeeded(order){
          // order.order_dateNeededFrom + ' - ' + order.order_dateNeededTo
          return format(new Date(order.order_dateNeededFrom), 'MMM. dd, yyyy') + ' - ' +
          format(new Date(order.order_dateNeededTo), 'MMM. dd, yyyy')
        },
        getUpdatedDate(order){
          if(this.isProjectOrOnHand(order) == 'OnHand'){
            var projObj = this.getOrderHistory.projects.filter((p) => {
              return parseInt(order.project_id) === parseInt(p.id)
            })
            var contractObj = this.getOrderHistory.contracts.filter((c) => {
              return parseInt(projObj[0].contract_id) === parseInt(c.id)
            })
            var farmProdObj = this.getOrderHistory.farm_produce.filter((pp) => {
              return parseInt(contractObj[0].farm_id) === parseInt(pp.farm_id) && parseInt(order.produce_trader_id) === parseInt(pp.produce_trader_id)
            })
            return format(new Date(farmProdObj[0].updated_at.split(' ')[0]), 'MMM. dd, yyyy') // stopped on for approval on hand bid on history
          }
          else{
            return format(new Date(order.updated_at.split('T')[0]), 'MMM. dd, yyyy')
          }
        },
        getBidType(order){
          var projectObj = this.getOrderHistory.projects.filter((p) => {
            return parseInt(order.project_id) === parseInt(p.id)
          })
          var contractObj = this.getOrderHistory.contracts.filter((c) => {
            return parseInt(projectObj[0].contract_id) === parseInt(c.id)
          })
          var string = null
          var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
            return parseInt(order.id) === parseInt(p.bid_order_id)
          })
          var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
            return parseInt(order.id) === parseInt(o.bid_order_id)
          })
          if(projectBidObj[0]){
            string = contractObj[0].farm_name + ' Project'
          }
          if(onHandBidObj[0]){
            string = contractObj[0].farm_name + ' On-Hand'
          }
          return string
        },
        getQty(order){
          if(order.bid_order_status_id != 5){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
              return parseInt(order.id) === parseInt(p.bid_order_id)
            })
            var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
              return parseInt(order.id) === parseInt(o.bid_order_id)
            })
            if(projectBidObj[0]){
              return projectBidObj[0].project_bid_maxQty
            }
            if(onHandBidObj[0]){
              return onHandBidObj[0].on_hand_bid_qty
            }
          }
          else{
            return order.order_finalQty
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
          if(this.isProjectOrOnHand(order) == 'Project'){
            var projectObj = this.getOrderHistory.projects.filter((p) => {
              return parseInt(order.project_id) === parseInt(p.id)
            })
            var contractObj = this.getOrderHistory.contracts.filter((c) => {
              return parseInt(projectObj[0].contract_id) === parseInt(c.id)
            })
            return contractObj[0].contract_estimatedPrice.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
          }
          else{
            return order.order_traderPrice.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
          }

        },
        getInitTotal(order){
          if(this.isProjectOrOnHand(order) == 'Project'){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
              return parseInt(order.id) === parseInt(p.bid_order_id)
            })
            return (order.order_initialPrice * projectBidObj[0].project_bid_maxQty).toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
          }
          else{
            var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
              return parseInt(order.id) === parseInt(o.bid_order_id)
            })
            return (order.order_initialPrice * onHandBidObj[0].on_hand_bid_qty).toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
          }
        },
        getTotal(order){
          var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
            return parseInt(order.id) === parseInt(p.bid_order_id)
          })
          var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
            return parseInt(order.id) === parseInt(o.bid_order_id)
          })
          if(projectBidObj[0]){
            return parseFloat(projectBidObj[0].project_bid_total).toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
          }
          if(onHandBidObj[0]){
            return onHandBidObj[0].on_hand_bid_total.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
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
          if(this.isProjectOrOnHand(order) == 'Project'){
            var projectObj = this.getOrderHistory.projects.filter((p) => {
              return parseInt(order.project_id) === parseInt(p.id)
            })
            if(projectObj[0].project_harvestableEnd){
                return format(new Date(projectObj[0].project_harvestableEnd), 'MMM. dd, yyyy')
            }
            else {
                var harvestDate = add(new Date(projectObj[0].project_commenceDate), {
                    weeks: 1
                })
                var formattedDate = format(harvestDate, 'yyyy-MM-dd');
                return formattedDate
            }
          }
          else{
            var projObj = this.getOrderHistory.projects.filter((pp) => {
              return parseInt(order.project_id) === parseInt(pp.id)
            })
            var contractObj = this.getOrderHistory.contracts.filter((c) => {
              return parseInt(projObj[0].contract_id) === parseInt(c.id)
            })
            var farmProdObj = this.getOrderHistory.farm_produce.filter((ppp) => {
              return parseInt(contractObj[0].farm_id) === parseInt(ppp.farm_id) && parseInt(ppp.produce_trader_id) === parseInt(order.produce_trader_id)
            })
            return format(new Date(farmProdObj[0].prod_lastDateOfHarvest), 'MMM. dd, yyyy')
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
        // getMaxPrice(order){
        //   if(this.isProjectOrOnHand(order) == 'Project'){
        //     var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
        //         return parseInt(order.id) === parseInt(p.bid_order_id)
        //       })
        //     if(projectBidObj[0]){
        //       var maxPrice = parseFloat(projectBidObj[0].project_bid_total.split(' - ')[1]).toFixed(2)
        //       return maxPrice
        //     }
        //   }
        //   else{
        //     var onHandBidObj = this.getOrderHistory.on_hand_bids.filter((o) => {
        //       return parseInt(order.id) === parseInt(o.bid_order_id)
        //     })
        //     var prodYieldObj = this.getOrderHistory.produce_yields.filter((y) => {
        //       return parseInt(order.project_id) === parseInt(y.project_id) && parseInt(order.produce_trader_id) === parseInt(y.produce_trader_id)
        //     })
        //     var maxPrice = parseFloat(prodYieldObj[0].produce_yield_price * )
        //   }
        // },
        getPercentage(order){
          var total = null
          if(this.isProjectOrOnHand(order) == 'Project'){
            var projectBidObj = this.getOrderHistory.project_bids.filter((p) => {
              return parseInt(order.id) === parseInt(p.bid_order_id)
            })
            if(projectBidObj[0]){
              total = parseFloat(parseFloat(order.order_negotiatedPrice) * parseFloat(projectBidObj[0].project_bid_maxQty))
            }
          }
          else{
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
          if(this.isProjectOrOnHand(order) == 'Project'){
            var projectObj = this.getOrderHistory.projects.filter((p) => {
              return parseInt(order.project_id) === parseInt(p.id)
            })

            var year = parseInt(projectObj[0].project_harvestableEnd.split('-')[0]);
            var month = parseInt(projectObj[0].project_harvestableEnd.split('-')[1]);
            var day = parseInt(projectObj[0].project_harvestableEnd.split('-')[2]);
            var isAfterDate = isAfter(new Date(year, month-1, day).setHours(8, 0, 0, 0), new Date().setHours(8, 0, 0, 0))
            console.log(new Date())
            if(isAfterDate){
              this.payment_data.refund_numOfDays = (eachDayOfInterval({
                start: new Date().setHours(8, 0, 0, 0),
                end: new Date(year, month-1, day).setHours(8, 0, 0, 0)
              }).length - 1)

              if(this.payment_data.refund_numOfDays >= 30){
                this.payment_data.refund_percentage = 1
                this.payment_data.refund_amount = (order.order_dpAmount * 1).toFixed(2)
              }
              else if(this.payment_data.refund_numOfDays >= 14){
                this.payment_data.refund_percentage = 0.5
                this.payment_data.refund_amount = (order.order_dpAmount * 0.5).toFixed(2)
              }
              else{
                this.payment_data.refund_percentage = null
                this.payment_data.refund_amount = null
              }
            }
          }
        },
        isDateAfterHarvest(order){
          var projObj = this.getOrderHistory.projects.filter((p) => {
            return parseInt(order.project_id) === parseInt(p.id)
          })
          console.log(projObj[0])
          var year = parseInt(projObj[0].project_harvestableEnd.split('-')[0]);
          var month = parseInt(projObj[0].project_harvestableEnd.split('-')[1]);
          var day = parseInt(projObj[0].project_harvestableEnd.split('-')[2]);
          var isAfterDate = isAfter(new Date().setHours(8, 0, 0, 0), new Date(year, month-1, day).setHours(8, 0, 0, 0)) || isEqual(new Date().setHours(8, 0, 0, 0), new Date(year, month-1, day).setHours(8, 0, 0, 0))
          console.log(new Date().setHours(8, 0, 0, 0))
          console.log(new Date(year, month-1, day))
          console.log(isAfterDate)
          // console.log(order.id, 1)
          return isAfterDate
        },
        getRefundAmount(order){
          var refundObj = this.getOrderHistory.refunds.filter((r) => {
            return parseInt(order.id) === parseInt(r.bid_order_id)
          })
          return refundObj[0].refund_amount.toFixed(2)
        },
        setStatus(order){
          if(order.bid_order_status_id == 2){
            if(this.isProjectOrOnHand(order) == 'Project'){
              this.updateProjectStatus(order.id)
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
            else{
              this.updateOnHandStatus(order.id)
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
          }
          else if(order.bid_order_status_id == 3){
            console.log(order.bid_order_status_id)
            this.payment_data.id = order.id
            this.payment_data.bid_order_acc_amount = order.order_dpAmount.toFixed(2)
            this.payment_data.bid_order_acc_type = 'First Payment'
            this.payment_data.bid_type = this.isProjectOrOnHand(order)
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
          else if(order.bid_order_status_id == 5){
            console.log(1)
            this.payment_data.id = order.id
            this.payment_data.bid_order_acc_amount = this.getRemainingBalance(order).toFixed(2)
            this.payment_data.bid_order_acc_type = 'Final Payment'
            this.payment_data.bid_type = this.isProjectOrOnHand(order)
            this.finalPayment(this.payment_data)
            .then(() => {
              setTimeout(() => {
                this.$toastr.s('Final Payment Delivered!')
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
          else if(order.bid_order_status_id == 8){
            this.payment_data.id = order.id
            this.payment_data.bid_type = this.isProjectOrOnHand(order)
            this.confirmRefund(this.payment_data)
            .then(() => {
              setTimeout(() => {
                this.$toastr.s('Refund Confirmed!')
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
        refundOrder(order){
          this.payment_data.id = order.id
          this.requestRefund(this.payment_data)
          .then(() => {
            this.$toastr.s('Request for Refund Delivered!')
            setTimeout(() => {
              location.reload()
            }, 5000)
          })
          .catch((err) => {
            console.log(err)
            this.errors = err.response.data.errors
            for(var error in this.errors){
              this.$toastr.e(this.errors[error][0])
            }
          })
        },
        getPaymentType(order){
          var bidOrderAccObj = this.getOrderHistory.bid_order_accs.filter((o) => {
            return parseInt(order.id) === parseInt(o.bid_order_id)
          })
          return bidOrderAccObj[bidOrderAccObj.length - 1].bid_order_acc_type
        },
        hasRefundRequest(order){
          var refundObj = this.getOrderHistory.refunds.filter((r) => {
            return parseInt(order.id) === parseInt(r.bid_order_id)
          })
          if(refundObj[0]){
            console.log(true)
            return true
          }
          console.log(false)
          return false
        },
        getRefundDatePlaced(order){
          var refundObj = this.getOrderHistory.refunds.filter((r) => {
            return parseInt(order.id) === parseInt(r.bid_order_id)
          })
          return refundObj[0].created_at.split('T')[0]
        },
        getRemainingBalance(order){
          var result = (order.order_finalPrice * order.order_finalQty) - order.order_dpAmount
          return result < 0 ? 0 : result
        },
        dpDueDateFormat(date){
          return format(new Date(date), 'MMM. dd, yyyy')
        },
        getDpDueDate(order){
          var bidOrderAccObj = this.getOrderHistory.bid_order_accs.filter((o) => {
            if(o){
              return parseInt(order.id) === parseInt(o.bid_order_id);
            }
          })
          var bidOrder = bidOrderAccObj.filter((o) => {
            return o.bid_order_acc_type === 'First Payment'
          })
          if(bidOrder.length > 0){
            return bidOrder[0].bid_order_acc_datePaid
          }
          else{
            return order.order_dpDueDate
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
          if(bidOrderAccObj.length > 0){
            return bidOrderAccObj[0].bid_order_acc_paymentMethod
          }
        },
        getDatePaid(order){
          var bidOrderAccObj = this.getOrderHistory.bid_order_accs.filter((o) => {
            if(o){
              return parseInt(order.id) === parseInt(o.bid_order_id);
            }
          })
          if(bidOrderAccObj.length > 0){
            return format(new Date(bidOrderAccObj[0].bid_order_acc_datePaid), 'MMM. dd, yyyy')
          }
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
          if(bidOrderAccObj.length > 0){
            return bidOrderAccObj[0].bid_order_acc_accName
          }
        },
        getDateDispatch(order){
          var deliveryObj = this.getOrderHistory.deliveries.filter((d) => {
            return parseInt(order.id) === parseInt(d.bid_order_id)
          })
          return format(new Date(deliveryObj[0].delivery_date), 'MMM. dd, yyyy')
        }
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

<template>
  <div class="distributorListOfBids">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Bid Orders</h3>
    </div>
    <div class="container-fluid p-3 m-0" style="height:90%; width:100%;">
      <div class="container-fluid w-100 h-100" style="background:lightgreen;">
        <div class="d-flex justify-content-between align-items-baseline w-100 p-3" style="height:10%;">
          <h4 v-if="getOrderList.distributor">{{ getOrderList.distributor.distributor_firstName + ' ' + getOrderList.distributor.distributor_lastName }}</h4>
          <h4 v-if="getOrderList.orders">Total Orders: {{ getOrderList.orders.length }}</h4>
        </div>
        <div class="w-100" :style="[getOrderList.orders && getOrderList.orders.length >= 16 ? {'overflow-y':'scroll'} : {}, {'height': '90%'}]">          
          <table style="width:100%;">
            <thead>
                <tr>
                    <th>Order No.</th>
                    <th>Produce Name</th>
                    <th>Qty</th>
                    <th>Order Status</th>
                    <th>Initial Price</th>
                    <th>Negotiated Price</th>
                    <th>Final Price</th>
                    <th>Date Placed</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
              <tr v-for="(order, index) in getOrderList.orders" :key="index" @click="redirectBidOrderDetails(order.id)">
                <td>{{ order.id }}</td>                
                <td v-if="getOrderList.produce_trader">{{ getProdName(order) }}</td>
                <td v-if="getOrderList.project_bids && getOrderList.on_hand_bids">{{ getQty(order) }}</td>
                <td>{{ getStatus(order) }}</td>
                <td v-if="order.order_initialPrice">{{ order.order_initialPrice.toFixed(2) }}</td>
                <td v-if="order.order_negotiatedPrice">{{  order.order_negotiatedPrice.toFixed(2) }}</td>
                <td v-else>&nbsp;</td>
                <td v-if="order.order_finalPrice">{{ order.order_finalPrice.toFixed(2) }}</td>
                <td v-else>&nbsp;</td>
                <td>{{ order.created_at.split('T')[0] }}</td>
                <td v-if="getOrderList.project_bids && getOrderList.on_hand_bids">{{ order.order_finalTotal ? order.order_finalTotal.toFixed(2) : getTotal(order) }}</td>
              </tr>              
            </tbody>    
          </table>
        </div>
      </div>
    </div>  
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  name: 'DistributorListOfBids',
  created(){
    this.fetchOrdersForDist(this.$route.params.id)
    .then(() => {
      this.readyApp()
    })
    
  },
  methods: {
    ...mapActions(['readyApp', 'fetchOrdersForDist']),
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
    getProdName(order){      
      var prodTraderObj = this.getOrderList.produce_trader.filter((p) => {
        return parseInt(order.produce_trader_id) === parseInt(p.id)
      })
      return prodTraderObj[0].prod_name
    },
    getQty(order){
      var projBidObj = this.getOrderList.project_bids.filter((p) => {
        return parseInt(order.id) === parseInt(p.bid_order_id)
      })
      var onHandObj = this.getOrderList.on_hand_bids.filter((o) => {
        return parseInt(order.id) === parseInt(o.bid_order_id)
      })
      if(projBidObj[0]){
        return projBidObj[0].project_bid_maxQty + ' kg/s'
      }
      else if(onHandObj[0]){
        return onHandObj[0].on_hand_bid_qty + ' kg/s'
      }
    },
    getTotal(order){
      var projBidObj = this.getOrderList.project_bids.filter((p) => {
        return parseInt(order.id) === parseInt(p.bid_order_id)
      })
      var onHandObj = this.getOrderList.on_hand_bids.filter((o) => {
        return parseInt(order.id) === parseInt(o.bid_order_id)
      })
      if(projBidObj[0]){
        return projBidObj[0].project_bid_total
      }
      else if(onHandObj[0]){
        return onHandObj[0].on_hand_bid_total.toFixed(2)
      }
    },
    redirectBidOrderDetails(id){
      this.$router.push({ path: `/bid/orders/${id}` })
    }
  },
  computed: {
    ...mapGetters(['getOrderList'])
  }
}
</script>

<style scoped>
table, th, td {
  border-collapse: collapse;
  border-spacing: 0;
  border: 1px solid black;
  padding: 10px;
  text-align:center
}
tr{
  background:transparent
}
tr:hover{
  cursor: pointer;
  background:lightgray;
  transition: 0.5s ease-in;
}
</style>
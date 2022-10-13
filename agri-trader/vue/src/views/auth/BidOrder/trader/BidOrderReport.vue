<template>
  <div class="bidOrderReport">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA; background-color: #E0EDCA;">
            <h3>Bid Orders</h3>
        </div>
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
          <div style="width:85%; height:65%" class="pb-5">
              <div class="form-row mt-3">
                  <div class="col-lg-2 me-3">
                      <label for="stockInHitsory_supplierList" class="form-label me-4">Select Project #</label>
                      <select class="form-select" id="supplier_name" @change="setProject($event)">
                          <option selected value="None">None</option>
                          <option v-for="(id, index) in getProjectIDs" :key="index" :value="id">{{ id }}</option>                        
                      </select>                    
                  </div>                  
                  <div class="col-lg-2 me-3">
                      <label for="stockInHitsory_supplierList" class="form-label me-4">Select Distributor</label>
                      <select class="form-select" id="supplier_name" @change="setDist($event)">
                          <option selected value="None">None</option>
                          <option v-for="(distributor, index) in getDistributors" :key="index" :value="distributor.id">{{ distributor.distributor_firstName + ' ' + distributor.distributor_lastName }}</option>                        
                      </select>                    
                  </div>                  
                  <div class="col-lg-2 me-3">
                      <label for="stockInHitsory_supplierList" class="form-label me-4">Date From</label>
                      <input type="date" name="" class="form-control" id="" v-model="filter_dateFrom">                  
                  </div>                  
                  <div class="col-lg-2 me-3">
                    <label for="stockInHitsory_supplierList" class="form-label me-4">Date To</label>
                    <input type="date" name="" class="form-control" id="" v-model="filter_dateTo">                    
                  </div>                  
              </div>
              <div class="mb-2 mt-4" style="width:100%; height:90%; clear:left;">
                  <table id="supplySelect" class="table table-striped table-bordered align-middle" :style="[filteredTable && filteredTable.length > 8 ? {'overflow-y':'scroll'} : {}, {'width': '100%'}]">
                      <thead align="center">
                          <tr>                              
                              <th scope="col">Order #</th>                                                                               
                              <th scope="col">Distributor Name</th>
                              <th scope="col">Produce Name</th>                             
                              <th scope="col">Qty</th>                             
                              <th scope="col">Price</th>                             
                              <th scope="col">Subtotal</th>                             
                              <th scope="col">Type of Bid</th>                             
                              <th scope="col">Date Needed</th>
                              <th scope="col">Date Issued</th>
                          </tr>
                      </thead>
                      <tbody align="center">
                          <tr style="cursor:pointer;" v-for="(order, index) in filteredTable" :key="index" @click="$router.push({ path: `/bid/orders/${order.id}` })">                   
                              <td>{{ order.id }}</td>                                                                         
                              <td>{{ getDistName(order) }}</td>
                              <td>{{ getProdName(order) }}</td>                                            
                              <td>{{ getQty(order) }}</td>
                              <td>{{ getPrice(order) }}</td>                                                                                                                 
                              <td>{{ getTotal(order) }}</td>                                                                                                                 
                              <td>{{ getType(order) }}</td>                                                                                                                 
                              <td>{{ order.order_dateNeededFrom + ' / ' + order.order_dateNeededTo }}</td>
                              <td>{{ order.created_at.split('T')[0] }}</td>
                          </tr>
                      </tbody>
                  </table>
              </div>              
          </div>
      </div>    
  </div>
</template>

<script>
import { format, add, sub } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'BidOrderReport',
    created(){
        this.fetchBidOrderReport()
        .then(() => {
            if(this.getBidOrderReport.orders){
                var orders = this.getBidOrderReport.orders.sort((a, b) => {
                    return new Date(a.created_at) - new Date(b.created_at)
                })
                this.filter_dateFrom = format(new Date(orders[0].created_at), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(orders[orders.length - 1].created_at), 'yyyy-MM-dd')
            }
            this.readyApp()
        })
    },
    data(){
        return {
            filter_project: "None",
            filter_dist: "None",
            filter_dateFrom: null,
            filter_dateTo: null,
        }
    },
    watch: {
        filter_dateFrom(newVal){
            if(newVal >= this.filter_dateTo){
                this.filter_dateFrom = format(sub(new Date(newVal), {
                    days: 1
                }), 'yyyy-MM-dd')
            }
        },
        filter_dateTo(newVal){
            if(newVal <= this.filter_dateFrom){
                this.filter_dateTo = format(add(new Date(newVal), {
                    days: 1
                }), 'yyyy-MM-dd') 
            }
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchBidOrderReport']),
        setProject(e){
            this.filter_project = e.target.value
        },
        setDist(e){
            this.filter_dist = e.target.value
        },
        getDistName(order){
            var distObj = this.getBidOrderReport.distributors.filter((d) => {
                return parseInt(order.distributor_id) === parseInt(d.id)
            })
            return distObj[0].distributor_firstName + ' ' + distObj[0].distributor_lastName
        },
        getProdName(order){
            var prodTraderObj = this.getBidOrderReport.produce_traders.filter((p) => {
                return parseInt(order.produce_trader_id) === parseInt(p.id)
            })
            var prodObj = this.getBidOrderReport.produces.filter((p) => {
                return parseInt(prodTraderObj[0].produce_id) === parseInt(p.id)
            })
            var arr = prodTraderObj[0].prod_name.split('(Class')
            if(arr.indexOf('(Class') != -1){
                arr.splice(arr.indexOf('(Class') - 1, 0, prodObj[0].prod_type)
                arr.join(' ')
                return arr
            }
            else{
                return prodTraderObj[0].prod_name + ' ' + prodObj[0].prod_type
            }
        },
        getQty(order){
            if(order.order_finalQty){
                return order.order_finalQty
            }
            else{
                var projBidObj = this.getBidOrderReport.project_bids.filter((b) => {
                    return parseInt(order.id) === parseInt(b.bid_order_id)
                })
                var onHandBidObj = this.getBidOrderReport.on_hand_bids.filter((b) => {
                    return parseInt(order.id) === parseInt(b.bid_order_id)
                })
                if(projBidObj[0]){
                    return projBidObj[0].project_bid_maxQty
                }
                else if(onHandBidObj[0]){
                    return onHandBidObj[0].on_hand_bid_qty
                }
            }
        },
        getPrice(order){
            if(order.order_finalPrice){
                return order.order_finalPrice
            }
            else if(order.order_negotitatedPrice){
                return order.order_negotitatedPrice
            }
            else if(order.order_initialPrice){
                return order.order_initialPrice
            }
        },
        getTotal(order){
            if(order.order_finalTotal){
                return order.order_finalTotal
            }
            else{
                var projBidObj = this.getBidOrderReport.project_bids.filter((b) => {
                    return parseInt(order.id) === parseInt(b.bid_order_id)
                })
                var onHandBidObj = this.getBidOrderReport.on_hand_bids.filter((b) => {
                    return parseInt(order.id) === parseInt(b.bid_order_id)
                })
                if(projBidObj[0]){
                    return projBidObj[0].project_bid_total
                }
                else if(onHandBidObj[0]){
                    return onHandBidObj[0].on_hand_bid_total
                }
            }
        },
        getType(order){
            var projBidObj = this.getBidOrderReport.project_bids.filter((b) => {
                return parseInt(order.id) === parseInt(b.bid_order_id)
            })
            var onHandBidObj = this.getBidOrderReport.on_hand_bids.filter((b) => {
                return parseInt(order.id) === parseInt(b.bid_order_id)
            })
            if(projBidObj[0]){
                return 'Project Bid'
            }
            else if(onHandBidObj[0]){
                return 'On-Hand Bid'
            }
        },
    },
    computed: {
        ...mapGetters(['getBidOrderReport']),
        filteredTable(){
            var table = [];
            if(this.getBidOrderReport.orders){
                table = this.getBidOrderReport.orders.filter((o) => {
                    return format(new Date(o.created_at), 'yyyy-MM-dd') >= this.filter_dateFrom
                    && format(new Date(o.created_at), 'yyyy-MM-dd') <= this.filter_dateTo
                })
                if(this.filter_project != 'None' && this.filter_dist != 'None'){
                    table = table.filter((o) => {
                        return parseInt(this.filter_project) === parseInt(o.project_id) 
                        && parseInt(this.filter_dist) === parseInt(o.distributor_id)
                    })
                }
                else if(this.filter_project == 'None' && this.filter_dist != 'None'){
                    table = table.filter((o) => {
                        return parseInt(this.filter_dist) === parseInt(o.distributor_id)
                    })
                }
                else if(this.filter_project != 'None' && this.filter_dist == 'None'){
                    table = table.filter((o) => {
                        return parseInt(this.filter_project) === parseInt(o.project_id)                     
                    })
                }
            }
            return table
        },
        getProjectIDs(){
            var ids = new Set()
            var arr = []
            if(this.getBidOrderReport.orders){
                this.getBidOrderReport.orders.forEach((o) => {
                    ids.add(o.project_id)
                })
                ids.forEach((id) => {
                    arr.push(id)
                })
            }
            return arr
        },
        getDistributors(){
            var ids = new Set()
            var arr = []
            if(this.getBidOrderReport.orders){
                this.getBidOrderReport.orders.forEach((o) => {
                    ids.add(o.distributor_id)
                })
                ids.forEach((id) => {
                    var distObj = this.getBidOrderReport.distributors.filter((d) => {
                        return parseInt(id) === parseInt(d.id)
                    })
                    var data = {
                        id: distObj[0].id,
                        distributor_firstName: distObj[0].distributor_firstName,
                        distributor_lastName: distObj[0].distributor_lastName,
                    }
                    arr.push(data)
                })
            }
            return arr
        },
    }
}
</script>

<style>

</style>
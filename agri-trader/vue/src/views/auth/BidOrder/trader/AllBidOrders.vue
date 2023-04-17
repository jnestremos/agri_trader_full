<template>
  <div class="allBidOrders">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Bid Orders</h3>
        <div class="d-flex">
            <router-link to="/bid/orders/report"><button class="btn btn-primary me-3">View All Orders</button></router-link>
            <button @click="filterBidOrders()" class="btn btn-secondary">Filter By Buyer</button>
        </div>

    </div>
    <div class="container-fluid w-100 d-flex flex-wrap" style="height:90%; position: relative;">
        <div class="w-100" v-if="getOrderData.orders.length > 0">
            <div class="row mb-5" v-for="(order, index) in filtered" :key="index">
                <div class="col-4" style="height:35vh" v-for="(o, i) in order" :key="i">
                    <div class="d-flex order" style="height:100%; border-radius:50px; position: relative;" @click="showOrder(o.id)">
                        <div class="" style="position: absolute; top:5%; left:5%; width:85%;">
                            <div class="d-flex">
                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:40px;" class="me-3"/>
                                <h3 class="mb-4">{{ 'Order #' + o.id + ` (${getDistName(o)})` }}</h3>
                            </div>
                            <h5 class="d-flex align-items-baseline">Produce: <p class="ms-3">{{ getProduceName(o) }}</p></h5>
                            <h5 class="d-flex align-items-baseline">Trader's Price: <p class="ms-3">{{ getOrigPrice(o).toLocaleString("en-PH", { style: 'currency', currency: 'PHP' }) }}</p></h5>
                            <h5 class="d-flex align-items-baseline">Asking Price: <p class="ms-3">{{ o.order_initialPrice.toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })  }}</p></h5>
                            <h5 class="d-flex align-items-baseline">Quantity: <p class="ms-3">{{ getQty(o) + ' kgs' }}</p></h5>
                            <h5 class="d-flex align-items-baseline">Status: <p class="ms-3">{{ getStatus(o) }}</p></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="" style="top:42%; left:42%; position: absolute" v-else>
           <h2 class="text-black">No Bids Added!</h2>
        </div>
        <!-- <pre>{{getFarmData}}</pre>         -->
        <div class="" style="position:absolute; right:5%; bottom:5%" v-if="getOrderData.total > 6">
            <p class="text-center">Page {{ getOrderData.current_page }} out of {{ getOrderData.last_page }}</p>
            <ul class="pagination" id="pagination">
                <li :class="[getOrderData.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>
                <li :class="[getOrderData.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getOrderData.links" :key="index" @click="showPage(page.label)">
                    <a class="page-link">{{page.label}}</a>
                </li>
                <li :class="[getOrderData.current_page != getOrderData.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
            </ul>
        </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "AllBidOrders",
    created(){
        this.fetchAllOrders()
        .then(() => {
            if(this.getOrderData.current_page){
                var query = `page=${this.getOrderData.current_page}`
                this.fetchAllOrders(query)
            }
            this.filtered = this.filteredOrderArray()
            this.readyApp()
        })
    },
    methods: {
        ...mapActions(['readyApp', 'fetchAllOrders', 'fetchOrder']),
        getDistName(order){
            var distObj = this.getOrderDistributors.filter((d) => {
                return parseInt(order.distributor_id) === parseInt(d.id)
            })
            return distObj[0].distributor_firstName + ' ' + distObj[0].distributor_lastName
        },
        getFarmName(order){
            var projObj = this.getOrderProjects.filter((p) => {
                return parseInt(p.id) === parseInt(order.project_id)
            })
            console.log(projObj)
            var contractObj = this.getOrderContracts.filter((c) => {
                return parseInt(c.id) === parseInt(projObj[0].contract_id)
            })
            console.log(contractObj)
            return contractObj[0].farm_name
        },
        getProduceName(order){
            var prodObj = this.getOrderProduces.filter((p) => {
                return parseInt(p.id) === parseInt(order.produce_trader_id)
            })
            var produceObj = this.getOrderData.produces_all.filter((p) => {
                return parseInt(prodObj[0].produce_id) === parseInt(p.id)
            })
            var arr = prodObj[0].prod_name.split('(Class')
            if(arr.indexOf('(Class') != -1){
                arr.splice(arr.indexOf('(Class'), 0, produceObj[0].prod_type)
                return arr.join(' ')
            }
            else{
                return prodObj[0].prod_name + ' ' + produceObj[0].prod_type
            }
        },
        getOrigPrice(order){
            var projObj = this.getOrderProjects.filter((p) => {
                return parseInt(p.id) === parseInt(order.project_id)
            })
            var contractObj = this.getOrderContracts.filter((c) => {
                return parseInt(c.id) === parseInt(projObj[0].contract_id)
            })
            return contractObj[0].contract_estimatedPrice
        },
        getQty(order){
            var projBidObj = this.getOrderProjectBids.filter((p) => {
                return parseInt(p.bid_order_id) === parseInt(order.id)
            })
            var onHandBidObj = this.getOrderOnHandBids.filter((p) => {
                return parseInt(p.bid_order_id) === parseInt(order.id)
            })
            console.log(projBidObj)
            console.log(onHandBidObj)
            if(projBidObj[0]){
                return projBidObj[0].project_bid_maxQty
            }
            else if(onHandBidObj[0]){
                return onHandBidObj[0].on_hand_bid_qty
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
        showPrevious(){
            var query = this.getOrderData.prev_page_url.split('?')[1]
            this.fetchAllOrders(query)
            .then(() => {
                this.filtered = this.filteredOrderArray()
            })

        },
        showNext(){
            var query = this.getOrderData.next_page_url.split('?')[1]
            this.fetchAllOrders(query)
            .then(() => {
                this.filtered = this.filteredOrderArray()
            })
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchAllOrders(query)
            .then(() => {
                this.filtered = this.filteredOrderArray()
            })
        },
        filteredOrderArray(){
            var filtered = [];
            var arr = this.getOrderData.orders;
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }
            return filtered
        },
        showOrder(id){
            this.fetchOrder(id)
            .then(() => {
                this.$router.push({ path: `/bid/orders/${id}` })
            })
        },
        filterBidOrders(){
            this.$router.push({ name: 'AllBidOrdersFiltered' })
        }
    },
    computed: {
        ...mapGetters([
            'getOrderData',
            'getOrderContracts',
            'getOrderProjects',
            'getOrderProduces',
            'getOrderProjectBids',
            'getOrderOnHandBids',
            'getOrderDistributors'
            ]),
        getPages(){
            var pages = []
            for(var i = 1; i <= this.getOrderData.last_page; i++){
                if(i >= this.getOrderData.current_page){
                    pages.push(i)
                }
            }
            return pages
        },
    },
    data(){
        return {
            filtered: null
        }
    }
}
</script>

<style scoped>
.order{
    background-color:greenyellow;
}

.order:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}

.page-item a{
    cursor:pointer;
}
</style>

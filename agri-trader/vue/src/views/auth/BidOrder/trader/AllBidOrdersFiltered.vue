<template>
  <div class="allBidOrdersFiltered">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Bid Orders</h3>
    </div>
    <div class="container-fluid w-100 d-flex flex-wrap" style="height:90%; position: relative;">
        <div class="w-100" v-if="getOrderData.orders.length > 0">            
            <div class="row mb-5" v-for="(order, index) in filtered" :key="index">                     
                <div class="col-4" style="height:35vh" v-for="(o, i) in order" :key="i">                
                    <div class="d-flex order" style="height:100%; border-radius:50px; position: relative;" @click="showOrder(o.id)">                
                        <div class="" style="position: absolute; top:5%; left:5%; 
                        width:85%;">
                            <div class="d-flex">
                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:40px;" class="me-3"/>
                                <h3 class="mb-4">{{ getDistName(o) }}</h3>
                            </div>                                                                                
                            <h5 class="d-flex align-items-baseline">Pending Orders: <p class="ms-3">{{ o['count(id)'] }}</p></h5>
                            <h5 class="d-flex align-items-baseline">Last Order Date Placed: <p class="ms-3">{{ o['max(created_at)'].split('T')[0] }}</p></h5>                          
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
    name: 'AllBidOrdersFiltered',
    created(){
      this.fetchAllOrdersFilter()
        .then(() => {
            if(this.getOrderData.current_page){
                var query = `page=${this.getOrderData.current_page}`               
                this.fetchAllOrdersFilter(query)                 
            }
            this.filtered = this.filteredOrderArray()
            this.readyApp()
        })        
    },  
    methods:{   
        ...mapActions(['readyApp']),
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
        getDistName(o){
          var distObj = this.getOrderDistributors.filter((d) => {
            return parseInt(d.id) === parseInt(o.distributor_id);
          })
          return `${distObj[0].distributor_firstName} ${distObj[0].distributor.lastName}`
        },
        showPrevious(){
            var query = this.getOrderData.prev_page_url.split('?')[1]
            this.fetchAllOrdersFilter(query)
            .then(() => {
                this.filtered = this.filteredOrderArray()   
            })
            
        },
        showNext(){
            var query = this.getOrderData.next_page_url.split('?')[1]
            this.fetchAllOrdersFilter(query)
            .then(() => {
                this.filtered = this.filteredOrderArray()   
            })  
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchAllOrdersFilter(query)
            .then(() => {
                this.filtered = this.filteredOrderArray()   
            })
        },
    },
    computed: {
      ...mapGetters(['getOrderData', 'getOrderData', 'getOrderDistributors', 'getOrderLastOrderDate'])
    }
}
</script>

<style>

</style>
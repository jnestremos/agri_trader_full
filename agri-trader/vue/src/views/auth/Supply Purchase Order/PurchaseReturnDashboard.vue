<template>
    <div class="PurchaseReturnDashboard">
        <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply Purchase Order | Purchase Returns</h3>
            <router-link to="/supplyOrder/statusDashboard"><button class="btn btn-success text-right">Return to Order Status</button></router-link>
        </div>
            <div class="container-fluid w-100 d-flex" style="height:90%; position: relative;">
                <div class="w-100" v-if="getReturnDashboard.returns_filtered && getReturnDashboard.returns_filtered.length > 0">
                    <div class="form-row mb-5" v-for="(order, index) in filtered" :key="index">
                        <div class="col-4" v-for="(o, i) in order" :key="i">
                            <div class="card mt-3 ms-3" style="height: 90%; background-color: #80B032; border-radius: 20px;">
                                <div class="card-body" style="position:relative">
                                    <!-- <div @click="triggerModal(o)" style="position:absolute; height:100%; width:100%; z-index:999; top:0; left:0; cursor: pointer;"></div> -->
                                    <label class="card-title font-weight-bold">{{ 'Return Request for ' + o.purchaseOrder_num }}</label> <!-- name of supply -->
                                    <label class="d-flex align-items-baseline">Last Purchase Order for Return: <p class="ms-3">{{ o.returnOrder_num }}</p></label>
                                    <label class="d-flex align-items-baseline">Supplier: <p class="ms-3">{{ getSupplierName(o) }}</p></label>                                    
                                    <label class="d-flex align-items-baseline">Number of Return Requests: <p class="ms-3">{{ getReturnRequests(o) }}</p></label>
                                    <label class="d-flex align-items-baseline">Status: <p class="ms-3 font-weight-bold" style="color:red">{{ o.returnOrder_status }}</p></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="" style="top:42%; left:42%; position: absolute" v-else>
                    <h2 class="text-black">No Purchase Orders Added!</h2>
                </div>
                <div class="" style="position:absolute; right:5%; bottom:5%" v-if="getReturnDashboard.total > 6">
                    <p class="text-center">Page {{ getReturnDashboard.current_page }} out of {{ getReturnDashboard.last_page }}</p>
                    <ul class="pagination" id="pagination">
                        <li :class="[getReturnDashboard.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>
                        <li :class="[getReturnDashboard.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getReturnDashboard.links" :key="index" @click="showPage(page.label)">
                            <a class="page-link">{{page.label}}</a>
                        </li>                
                        <li :class="[getReturnDashboard.current_page != getReturnDashboard.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
                    </ul>
                </div>    
            </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: "PurchaseReturnDashboard",
    created() {
        this.fetchReturnDashboard()
        .then(() => {
            if(this.getReturnDashboard.current_page){
                var query = `page=${this.getReturnDashboard.current_page}`      
                this.fetchReturnDashboard(query)   
            }
            this.filtered = this.filteredReturnArray()         
            this.readyApp()
        })        
    },
    data(){
        return {
            filtered: null
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchReturnDashboard']),
        filteredReturnArray(){
            var filtered = [];
            var arr = this.getReturnDashboard.returns_filtered;             
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        },
        getSupplierName(order){
            var supplyObj = this.getReturnDashboard.supplies.filter((ss) => {
                return parseInt(order.supply_id) === parseInt(ss.id)
            })
            var supplierObj = this.getReturnDashboard.suppliers.filter((s) => {
                return parseInt(supplyObj[0].supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        },
        getReturnRequests(order){
            var returnObj = this.getReturnDashboard.returns.filter((r) => {
                return order.purchaseOrder_num === r.purchaseOrder_num
            })
            return returnObj.length
        },
        showPrevious(){
            var query = this.getReturnDashboard.prev_page_url.split('?')[1]
            this.fetchReturnDashboard(query)
            .then(() => {
                this.filtered = this.filteredReturnArray()   
            })            
        },
        showNext(){
            var query = this.getReturnDashboard.next_page_url.split('?')[1]
            this.fetchReturnDashboard(query)
            .then(() => {
                this.filtered = this.filteredReturnArray()   
            })  
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchReturnDashboard(query)
            .then(() => {
                this.filtered = this.filteredReturnArray()   
            })
        }
    },
    computed: {
        ...mapGetters(['getReturnDashboard'])
    }
}
</script>

<style scoped>
/* .h5{
    flex: 1 1 auto;
} */
</style>
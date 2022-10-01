<template>
  <div class="allFarmsForOwner">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Farms and Owners</h3>
    </div>
    <div class="container-fluid w-100 d-flex flex-wrap" style="height:90%; position: relative;">
        <div class="w-100" v-if="getFarmDataForOwner.farms.length > 0">
            <div class="row mb-5" v-for="(farm, index) in filtered" :key="index">
                <div class="col-4" style="height:30vh" v-for="(f, i) in farm" :key="i">
                    <div class="d-flex farm" @click="viewFarm(f)" style="height:100%; border-radius:50px; position: relative; cursor: pointer;">
                        <div class="" style="position: absolute; top:5%; left:5%; 
                        width:85%;">
                            <div class="d-flex mb-4">
                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:40px;" class="me-3"/>
                                <h3 class="mb-4">{{ f.farm_name }}</h3>
                            </div>                             
                            <h4 class="d-flex">Projects Pending: <p class="ms-3">{{ getProjectsPending(f) }}</p></h4>
                            <h4 class="d-flex">Produce Count: <p class="ms-3">{{ getProduceCount(f) }}</p></h4>
                            <h4 class="d-flex">Trader In Charge: <p class="ms-3">{{ getTraderName(f) }}</p></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="" style="top:42%; left:42%; position: absolute" v-else>
           <h2 class="text-black">No Farms Added!</h2>
        </div> 
        <div class="" style="position:absolute; right:5%; bottom:5%" v-if="getFarmDataForOwner.total > 6">
            <p class="text-center">Page 1 out of 5</p>
            <ul class="pagination" id="pagination">
                <li :class="[getFarmDataForOwner.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>
                <li :class="[getFarmDataForOwner.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getFarmDataForOwner.links" :key="index" @click="showPage(page.label)">
                    <a class="page-link">{{page.label}}</a>
                </li>                
                <li :class="[getFarmDataForOwner.current_page != getFarmDataForOwner.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
            </ul>
        </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'AllFarmsForOwner',
    created(){
        this.fetchAllFarmsForOwner()
        .then(() => {
            if(this.getFarmDataForOwner.current_page){ 
                var query = `page=${this.getFarmData.current_page}`               
                this.fetchAllFarmsForOwner(query)                
            }
            this.filtered = this.filteredFarmArray()   
            this.readyApp()
        })        
    },
    data(){
        return {
            filtered: null
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchAllFarmsForOwner']),
        getProjectsPending(f){
            var contractObj = this.getContractListForOwner.filter((c) => {
                return parseInt(c.farm_id) === parseInt(f.id)
            })
            var projects = [];
            contractObj.forEach((c) => {
                var projectObj = this.getProjectListForOwner.filter((p) => {
                    return parseInt(p.contract_id) === parseInt(c.id) && c.project_completionDate === null
                })
                projects.push(projectObj[0])
            })
            return projects.length
        },
        getProduceCount(f){
            var farmProdObj = this.getFarmProduceListForOwner.filter((ff) => {
                return parseInt(f.id) === parseInt(ff.farm_id)
            })
            return farmProdObj.length
        },
        getTraderName(f){
            var traderObj = this.getTraderListForOwner.filter((t) => {
                return parseInt(f.trader_id) === parseInt(t.id)
            })
            return traderObj[0].trader_firstName + ' ' + traderObj[0].trader_lastName
        },
        viewFarm(f){            
            this.$router.push({ path: `/farm/owner/details/${f.id}` })
        },
        filteredFarmArray(){
            var filtered = [];
            var arr = this.getFarmDataForOwner.farms;             
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        },
        showPrevious(){
            var query = this.getFarmDataForOwner.prev_page_url.split('?')[1]
            this.fetchAllFarmsForOwner(query)
            .then(() => {
                this.filtered = this.filteredFarmArray()   
            })
            
        },
        showNext(){
            var query = this.getFarmDataForOwner.next_page_url.split('?')[1]
            this.fetchAllFarmsForOwner(query)
            .then(() => {
                this.filtered = this.filteredFarmArray()   
            })  
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchAllFarmsForOwner(query)
            .then(() => {
                this.filtered = this.filteredFarmArray()   
            })
        }
    },
    computed:{
        ...mapGetters([
            'getFarmDataForOwner', 
            'getProjectListForOwner', 
            'getContractListForOwner', 
            'getFarmProduceListForOwner',
            'getTraderListForOwner'
        ])
    }
}
</script>

<style scoped>
.farm{
    background-color:greenyellow;
}

.farm:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}

.page-item a{
    cursor:pointer;
}
</style>
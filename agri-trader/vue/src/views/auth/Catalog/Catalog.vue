<template>
  <div class="catalog p-3">
    <div class="d-flex justify-content-between align-items-end">
        <h2>Produces</h2>
        <input type="search" name="" id="" placeholder="Search" class="form-control" style="width:200px">
    </div>               
    
    <div class="container-fluid p-0 m-0">
        <div class="row p-0 mt-0 mb-2 ms-0 me-0">
            <div class="col-4 p-0 m-0">
                <p>Showing {{ getProduceDataa.produces.length }} of {{ getProduceDataa.total }} results</p> 
            </div>
            <div class="col-8 p-0 mt-2">
                <select name="" id="" class="form-select" style="width:200px; float:right; border:0">
                    <option value="" disabled selected hidden>Default Sorting</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>                
            </div>
        </div>    
        <div class="w-100" v-if="getProduceDataa.produces.length > 0">
            <div class="row p-0 m-0" v-for="(produce, index) in filtered" :key="index">
                <div class="col-3 p-0 mb-2 d-flex justify-content-center" v-for="(p, i) in produce" :key="i">
                    <div class="d-flex flex-column produce" style="width: 55%; height: 35vh;" @click="triggerModal(p.id)">
                        <div class="d-flex justify-content-center" style="height:75%; width:100%;">
                            <div style="height:100%; width:85%; background:green">
                                <!-- <img width="100%" height="100%" :src="[p != null ? require(`../../../../../public/storage/catalog_images/${p.prod_name}.jpg`) : '']" alt=""> -->
                            </div>
                        </div>
                        <p class="ms-3 mt-3">{{ p.prod_name + ' ' + p.prod_type }}</p>
                        <template>
                            <b-modal hide-footer :id="'modal-' + p.id" size="xl" scrollable>
                                <template #modal-header="{ close }">
                                    <h5 v-if="setModalContent == 'project'">List of Projects for {{ p.prod_name + ' ' + p.prod_type }}</h5> 
                                    <h5 v-else>Available Traders for {{ p.prod_name + ' ' + p.prod_type }}</h5> 
                                    <div class="d-flex align-items-center justify-content-between" style="width:15vw">
                                        <button type="button" class="btn btn-success" :disabled="!getProjects(p).length > 0" @click="setModalContent = 'project'">Projects</button>
                                        <button type="button" class="btn btn-success" :disabled="!getOnHand(p).length > 0" @click="setModalContent = 'onhand'">On-Hand</button>
                                    </div>                                   
                                    <font-awesome-icon icon="fa-solid fa-xmark" style="font-size:20px; cursor: pointer;" @click="close()"/>
                                </template>                            
                                <div class="container-fluid w-100 d-flex flex-wrap" >                                    
                                    <div v-if="setModalContent == 'project'" class="w-100">
                                        <div v-if="getProjects(p).length > 0" class="w-100">
                                            <div class="row">
                                                <div class="col-6 mb-5" v-for="(proj, ind) in getProjects(p)" :key="ind" style="height:35vh;">                
                                                    <div class="produceModal" style="height:100%; border-radius: 50px;" @click="redirectToBidOrder(proj)">
                                                        <div class="" style="position: absolute; top:7%; left:8%; width:90%; height:80%">
                                                            <div class="d-flex mb-2 align-items-center">
                                                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:25px;" class="me-3"/>
                                                                <h3>{{ getFarmName(proj) }}</h3>
                                                            </div>                                                                                
                                                            <h5 class="d-flex">Expected Harvest Date: <p class="ms-3">{{ getHarvestDate(proj) }}</p></h5>
                                                            <h5 class="d-flex">Expected Kilos Harvested: <p class="ms-3">{{ getHarvestKilos(proj).toFixed(2) + ' kgs' }}</p></h5>                                                                                            
                                                            <h5 class="d-flex">Asking Price: <p class="ms-3">{{ getPrice(proj).toFixed(2) }}</p></h5>                                                                                            
                                                            <h5 class="d-flex">Fruit Stage: <p class="ms-3">{{ getStage(proj) }}</p></h5>                                                                                                                        
                                                        </div>                       
                                                    </div>
                                                </div>                                                               
                                            </div>                                            
                                        </div>
                                        <div v-else class="w-100 d-flex justify-content-center align-items-center">
                                            <h3>NO PROJECTS ADDED!</h3>
                                        </div>                                    
                                    </div>
                                    <div v-else class="w-100">
                                        <div v-if="getOnHand(p).length > 0" class="w-100">
                                            <div class="row">
                                                <div class="col-6 mb-5" v-for="(prod, ind) in getOnHand(p)" :key="ind" style="height:35vh;">                
                                                    <div class="produceModal" style="height:100%; border-radius: 50px;" @click="redirectToBidOrder(prod)">
                                                        <div class="" style="position: absolute; top:7%; left:8%; width:90%; height:80%">
                                                            <div class="d-flex mb-2 align-items-center">
                                                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:25px;" class="me-3"/>
                                                                <h3>{{ getTraderName(prod) + ' - ' + getClass(prod) }}</h3>
                                                            </div>                                                                                                                                                                                                                                       
                                                            <h5 class="d-flex">On-Hand Price: <p class="ms-3">{{ 'Php ' + prod.on_hand_latestPrice.toFixed(2) }}</p></h5>                                                                                                                                                        
                                                            <h5 class="d-flex">Farm Origin: <p class="ms-3">{{ prod.farm_name }}</p></h5>                                                                                                                                                        
                                                            <!-- <h5 class="d-flex">Quantity on Hand: <p class="ms-3">{{ prod.produce_inventory_qtyOnHand }}{{ prod.produce_inventory_qtyOnHand > 1 ? ' kgs' : ' kg' }}</p></h5>                                                                                             -->
                                                            <h5 class="d-flex">Last Date of Harvest: <p class="ms-3">{{ getLastDateHarvest(prod) }}</p></h5>                                                                                            
                                                        </div>                       
                                                    </div>
                                                </div>                                                               
                                            </div>                                            
                                        </div>
                                        <div v-else class="w-100 d-flex justify-content-center align-items-center">
                                            <h3>NO AVAILABLE ON HAND!</h3>
                                        </div>                                    
                                    </div>
                                </div>                                                                                                
                            </b-modal>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-baseline" style="position:absolute; right:5%; bottom:0%;" v-if="getProduceDataa.total > 8">
            <p class="text-center me-3">Page {{ getProduceDataa.current_page }} out of {{ getProduceDataa.last_page }}</p>
            <ul class="pagination" id="pagination">
                <li :class="[getProduceDataa.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>
                <li :class="[getProduceDataa.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getProduceDataa.links" :key="index" @click="showPage(page.label)">
                    <a class="page-link">{{page.label}}</a>
                </li>                
                <li :class="[getProduceDataa.current_page != getProduceDataa.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
            </ul>
        </div>        
                      
    </div>

    
    
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import {  add, format, isEqual, isAfter, isBefore, sub } from 'date-fns'
export default {
    name: "Catalog",
    created(){
        this.fetchAllCatalogProduces()
        .then(() => {
            if(this.getProduceDataa.current_page){
                var query = `page=${this.getProduceDataa.current_page}`               
                this.fetchAllCatalogProduces(query)                
            }
            this.filtered = this.filteredProduceArray()   
            this.readyApp();
        })        
    },
    methods: {
        ...mapActions(['readyApp', 'fetchAllCatalogProduces']),
        filteredProduceArray(){
            var filtered = [];
            var arr = this.getProduceDataa.produces;             
            var arr1 = arr.slice(0,4)
            var arr2 = arr.slice(4,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        },     
        triggerModal(id){            
            this.$bvModal.show(`modal-${id}`)
        },
        getProjects(produce){   
            var contractObjs = this.getContractss.filter((c) => {
                return parseInt(produce.id) === parseInt(c.produce_id)
            })
            var projects = [];
            contractObjs.forEach((c) => {
                if(this.getProjectss.length > 0){
                    var projObj = this.getProjectss.filter((p) => {
                        return parseInt(p.contract_id) === parseInt(c.id)
                    })
                    if(projObj[0]){                                                                       
                        var year = this.getHarvestDate(projObj[0]).split('-')[0];
                        var month = this.getHarvestDate(projObj[0]).split('-')[1];
                        var day = this.getHarvestDate(projObj[0]).split('-')[2];
                        var yearToday = new Date().toISOString().split('T')[0].split('-')[0];
                        var monthToday = new Date().toISOString().split('T')[0].split('-')[1];
                        var dayToday = new Date().toISOString().split('T')[0].split('-')[2];
                        var harvestDate = new Date(year, month, day, 8, 0, 0, 0)
                        var today = new Date(yearToday, monthToday, dayToday, 8, 0, 0, 0)                        
                        var subMonth = sub(harvestDate, {
                            months: 1
                        })                                        
                        var before = isBefore(today, subMonth)   
                        var prodYieldObj = this.getProduceYields.filter((y) => {
                            return parseInt(projObj[0].id) === parseInt(y.project_id)
                        })         
                        if(before || prodYieldObj.length == 0){
                            projects.push(projObj[0])
                        } 
                    }                    
                }                                    
            })                 
            return projects          
        },
        getOnHand(produce){
            console.log(this.setModalContent)
            var prodTraderObj = this.getProduceTrader.filter((p) => {
                return parseInt(produce.id) === parseInt(p.produce_id) && parseFloat(p.prod_totalQty) > 0
            })
            var farmProdObjs = []
            console.log(prodTraderObj)
            prodTraderObj.forEach((prod) => {
                var farmProdObj = this.getFarmProducess.filter((p) => {
                    return parseInt(prod.id) === parseInt(p.produce_trader_id)
                })               
                farmProdObj.forEach((p) => {                    
                    farmProdObjs.push(p)
                })
            })            
            return farmProdObjs
        },
        getFarmName(proj){             
            var projectObj = this.getProjectss.filter((p) => {
                return parseInt(p.id) === parseInt(proj.id)
            })

            var contractObj = this.getContractss.filter((c) => {
                return parseInt(projectObj[0].contract_id) === parseInt(c.id)
            })

            return contractObj[0].farm_name
        },
        getHarvestDate(proj) {      
            if(proj){
                if(proj.project_harvestableEnd){                   
                    return proj.project_harvestableEnd
                }
            else {
                var harvestDate = add(new Date(proj.project_commenceDate), {
                    weeks: 1
                })
                var formattedDate = format(harvestDate, 'yyyy-MM-dd');       
                return formattedDate
                } 
            }                          
        },
        getHarvestKilos(proj){
            var contractObj = this.getContractss.filter((c) => {
                return parseInt(c.id) === parseInt(proj.contract_id)
            })
            return contractObj[0].contract_estimatedHarvest            
        },
        getPrice(p){               
            var contractObj = this.getContractss.filter((c) => {
                return parseInt(c.id) === parseInt(p.contract_id)
            })
            return contractObj[0].contract_estimatedPrice            
        }, 
        getTraderName(p){
           var prodTraderObj = this.getProduceTrader.filter((pp) => {
                return parseInt(pp.id) === parseInt(p.produce_trader_id)
           })
           var traderObj = this.getTraderss.filter((t) => {
                return parseInt(prodTraderObj[0].trader_id) === parseInt(t.id)
           })
           return traderObj[0].trader_firstName + ' ' + traderObj[0].trader_lastName
        },
        getLastDateHarvest(p){                 
            return p.prod_lastDateOfHarvest
        },
        getStage(proj){                         
            var keys = Object.keys(proj);    
            // console.log(projectObj[0])        
            var dateKeys = keys.splice(5,8)
            var pattern1 = new RegExp('project_flowering*')
            var pattern2 = new RegExp('project_fruitBudding*')
            var pattern3 = new RegExp('project_devFruit*')        
            var pattern4 = new RegExp('project_harvestable*') 
            var stage = null
            var check = false       

            for(var i = 0; i < dateKeys.length; i++){                
                if(proj[dateKeys[i]] != null && i < dateKeys.length - 2){                  
                    var year = proj[dateKeys[i]].split('-')[0]
                    var month = proj[dateKeys[i]].split('-')[1]
                    var day = proj[dateKeys[i]].split('-')[2]
                    var year1 = proj[dateKeys[i+1]].split('-')[0]
                    var month1 = proj[dateKeys[i+1]].split('-')[1]
                    var day1 = proj[dateKeys[i+1]].split('-')[2]
                    var checkStartDate = isBefore(new Date(year, month-1, day, 8,0,0,0), new Date().setHours(8,0,0,0))
                    var checkEndDate = isAfter(new Date(year1, month1-1, day1, 8,0,0,0), new Date().setHours(8,0,0,0))
                    var isStartEqual = isEqual(new Date(year, month-1, day, 8,0,0,0), new Date().setHours(8,0,0,0))
                    var isEndEqual = isEqual(new Date(year1, month1-1, day1, 8,0,0,0), new Date().setHours(8,0,0,0))                    
                    var string = null;
                    console.log((checkStartDate && checkEndDate) || (isStartEqual || isEndEqual))
                    if((checkStartDate && checkEndDate) || (isStartEqual || isEndEqual)){
                        check = true
                        if(isStartEqual){
                            string = dateKeys[i]
                        }
                        else if(isEndEqual){
                            string = dateKeys[i+1]
                        }
                        if(!string){
                            string = dateKeys[i]
                        }
                        if(pattern1.test(string)){
                            stage = "Flowering"
                            break
                        }
                        else if(pattern2.test(string)){
                            stage = "Fruit Budding"
                            break
                        }
                        else if(pattern3.test(string)){
                            stage = "Developing Fruit"
                            break
                        }
                        else if(pattern4.test(string)){
                            stage = "Harvestable"
                            break
                        }                                            
                    }
                    else{                        
                        if(!check){
                            stage = null
                        }
                        // if(pattern1.test(dateKeys[i-2])){
                        //     stage = "Flowering"
                        //     break
                        // }
                        // else if(pattern2.test(dateKeys[i-2])){
                        //     stage = "Fruit Budding"
                        //     break
                        // }
                        // else if(pattern3.test(dateKeys[i-2])){
                        //     stage = "Developing Fruit"
                        //     break
                        // }
                        // else if(pattern4.test(dateKeys[i-2])){
                        //     stage = "Harvestable"
                        //     break
                        // }                                                                            
                    }                    
                }                                
            }            
            return stage ?? 'Stages Not Yet Initiated'  
        },
        getClass(proj){                      
            var prodTraderObj = this.getProduceTrader.filter((p) => {
                return parseInt(proj.produce_trader_id) === parseInt(p.id) && parseFloat(proj.on_hand_qty) > 0
            })                    
            var arr = prodTraderObj[0].prod_name.split(' ')
            if(arr.indexOf('(Class') && arr[arr.length - 2]){                        
                return arr[arr.length - 2] + ' ' + arr[arr.length - 1]
            }
            else{
                return 'No Class'
            }            
        },
        redirectToBidOrder(p){  
            var contractObj = null
            if(this.setModalContent == 'project'){
                contractObj = this.getContractss.filter((c) => {
                    return parseInt(p.contract_id) === parseInt(c.id)
                })
                this.$router.push({ path: `/bid_order/progress/${contractObj[0].id}` }) 
            }
            else if(this.setModalContent == 'onhand'){
                // var prodInventoryObj = this.getProduceInventories.filter((i) => {
                //     return parseInt(p.produce_inventory_id) === parseInt(i.id) && parseFloat(i.produce_inventory_qtyOnHand) > 0
                // }) 
                // var prodYieldObj = null
                // this.getProduceYields.forEach((y) => {
                //     var container = y.filter((yy) => {
                //         return parseInt(prodInventoryObj[0].produce_yield_id) === parseInt(yy.id)
                //     }) 
                //     if(container.length > 0){
                //         prodYieldObj = container
                //     }                   
                // })
                // console.log(prodYieldObj)
                // var projObj = this.getProjectss.filter((ppp) => {
                //     return prodYieldObj[0].project_id === parseInt(ppp.id)
                // })
                // contractObj = this.getContractss.filter((c) => {
                //     return parseInt(projObj[0].contract_id) === parseInt(c.id)
                // })
                this.$router.push({ path: `/bid_order/on_hand/${p.farm_id}/${p.produce_trader_id}` }) 
            }           
            // if(prodInventoryObj[0] && prodInventoryObj[0].produce_inventory_qtyOnHand && prodInventoryObj[0].produce_inventory_qtyOnHand > 0 ){
            //     contractObj = this.getContractss.filter((c) => {
            //         return parseInt(p.farm_id) === parseInt(c.farm_id) && parseInt(p.produce_trader_id) === parseInt(c.produce_trader_id)
            //     })
            //     this.$router.push({ path: `/bid_order/on_hand/${contractObj[contractObj.length - 1].id}` }) 
            // }
            // else{
            //     contractObj = this.getContractss.filter((c) => {
            //         return parseInt(p.contract_id) === parseInt(c.id)
            //     })
            //     this.$router.push({ path: `/bid_order/progress/${contractObj[0].id}` }) 
            // }     
                                   
        },
        showPrevious(){
            var query = this.getProduceDataa.prev_page_url.split('?')[1]
            this.fetchAllCatalogProduces(query)
            .then(() => {
                this.filtered = this.filteredProduceArray()   
            })
            
        },
        showNext(){
            var query = this.getProduceDataa.next_page_url.split('?')[1]
            this.fetchAllCatalogProduces(query)
            .then(() => {
                this.filtered = this.filteredProduceArray()   
            })  
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchAllCatalogProduces(query)
            .then(() => {
                this.filtered = this.filteredProduceArray()   
            })
        }        
    },
    computed: {
        ...mapGetters([
            'getProduceDataa', 
            'getProduceTrader', 
            'getProjectss', 
            'getContractss', 
            'getFarmProducess', 
            'getProduceYields', 
            'getProduceInventories',
            'getTraderss'
        ]),
        getPages(){
            var pages = []
            for(var i = 1; i <= this.getProduceDataa.last_page; i++){
                if(i >= this.getProduceDataa.current_page){
                    pages.push(i)
                }
            }
            return pages
        },               
    },
    data(){
        return {
            filtered: null,
            setModalContent: 'project'
        }
    },
    mounted(){
        this.$root.$on('bv::modal::hide', (bvEvent) => {     
            if(bvEvent.trigger == 'headerclose'){                                        
                this.setModalContent = 'project'                
            }                 
        })        
    }
}
</script>

<style scoped>
.produce{
    background-color:transparent;
}

.produce:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}
.produceModal{
    background-color:lightgreen;
}

.produceModal:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}
</style>
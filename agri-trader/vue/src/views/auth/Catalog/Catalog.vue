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
                                <img width="100%" height="100%" :src="[p != null ? require(`../../../../../public/storage/catalog_images/${p.prod_name}.jpg`) : '']" alt="">
                            </div>
                        </div>
                        <p class="ms-3 mt-3">{{ p.prod_name }}</p>
                        <template>
                            <b-modal hide-footer :id="'modal-' + p.id" size="xl" scrollable>
                                <template #modal-header="{ close }">
                                    <h5 v-if="setModalContent = 'project'">List of Projects</h5> 
                                    <h5 v-else>Available Traders</h5> 
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
                                                            <h5 class="d-flex">Class: <p class="ms-3">{{ getClass(proj) ? getClass(proj) : 'No Class Indicated' }}</p></h5>                                                            
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
                                                                <h3>{{ prod.farm_name }}</h3>
                                                            </div>                                                                                                                                                                                                                                       
                                                            <h5 class="d-flex">On-Hand Price: <p class="ms-3">{{ getPrice(prod) }}</p></h5>                                                                                                                                                        
                                                            <h5 class="d-flex">Quantity on Hand: <p class="ms-3">{{ prod.produce_inventory_qtyOnHand + ' kgs'}}</p></h5>                                                                                            
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
import {  add, format, isEqual, isAfter, isBefore } from 'date-fns'
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
           var prodTraderObj = this.getProduceTrader.filter((p) => {
                return parseInt(produce.id) === parseInt(p.produce_id)
            })
            var result = []
            prodTraderObj.forEach((prod) => {
                var farmProdObj = this.getFarmProducess.filter((p) => {
                    return parseInt(prod.id) === parseInt(p.produce_trader_id)
                })
                farmProdObj.forEach((farmProd) => {
                    result.push(farmProd)
                })                                
                
            })            
            var contracts = []
            result.forEach((farmProd) => {
                var contractObj = this.getContractss.filter((p) => {
                    return parseInt(farmProd.farm_id) === parseInt(p.farm_id) && parseInt(farmProd.produce_trader_id) === parseInt(p.produce_trader_id)
                })
                contractObj.forEach((contract) => {
                    contracts.push(contract)
                })
            })            
            var projects = []            
            contracts.forEach((contract) => {
                var projectObj = this.getProjectss.filter((p) => {
                    return parseInt(contract.id) === parseInt(p.contract_id)
                })                
                projectObj.forEach((project) => {
                    projects.push(project)
                })
            })
            return projects          
        },
        getOnHand(produce){
            var prodTraderObj = this.getProduceTrader.filter((p) => {
                return parseInt(produce.id) === parseInt(p.produce_id)
            })

            var farmProdObjs = []

            prodTraderObj.forEach((prod) => {
                var farmProdObj = this.getFarmProducess.filter((p) => {
                    return parseInt(prod.id) === parseInt(p.produce_trader_id) && parseFloat(p.produce_inventory_qtyOnHand) > 0
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
        },
        getHarvestKilos(proj){
            var contractObj = this.getContractss.filter((c) => {
                return parseInt(c.id) === parseInt(proj.contract_id)
            })
            return contractObj[0].contract_estimatedHarvest            
        },
        getPrice(p){   
            if(this.setModalContent == 'project'){
                var contractObj = this.getContractss.filter((c) => {
                    return parseInt(c.id) === parseInt(p.contract_id)
                })
                return contractObj[0].contract_estimatedPrice
            }   
            else{
                var contractArr = this.getContractss.filter((c) => {
                    return parseInt(c.farm_id) === parseInt(p.farm_id) && parseInt(c.produce_trader_id) === parseInt(p.produce_trader_id)
                })

                var projects = []
                
                contractArr.forEach((c) => {
                    var projectObj = this.getProjectss.filter((pp) => {
                        return parseInt(c.id) === parseInt(pp.contract_id)
                    })
                    projects.push(projectObj[0])
                })

                var prodYields = []

                projects.forEach((pp) => {
                    var prodYieldObj = this.getProduceYields.filter((ppp) => {
                        return parseInt(pp.id) === parseInt(ppp.project_id)
                    })
                    prodYields.push(prodYieldObj[0])
                })

                return prodYields[prodYields.length - 1].produce_yield_price
                
            }                 
            
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
            // var check = false       

            for(var i = 0; i < dateKeys.length; i = i + 2){                
                if(proj[dateKeys[i]] != null){                  
                    var year = proj[dateKeys[i]].split('-')[0]
                    var month = proj[dateKeys[i]].split('-')[1]
                    var day = proj[dateKeys[i]].split('-')[2]
                    var year1 = proj[dateKeys[i+1]].split('-')[0]
                    var month1 = proj[dateKeys[i+1]].split('-')[1]
                    var day1 = proj[dateKeys[i+1]].split('-')[2]
                    if(isBefore(new Date(year, month-1, day, 8,0,0,0), new Date().setHours(8,0,0,0)) || 
                    isEqual(new Date(year, month-1, day, 8,0,0,0), new Date().setHours(8,0,0,0))){

                        if(isAfter(new Date(year1, month1-1, day1, 8,0,0,0), new Date().setHours(8,0,0,0)) ||
                    (isEqual(new Date(year1, month1-1, day1, 8,0,0,0), new Date().setHours(8,0,0,0)))){

                            if(pattern1.test(dateKeys[i])){
                                stage = "Flowering"
                                break
                            }
                            else if(pattern2.test(dateKeys[i])){
                                stage = "Fruit Budding"
                                break
                            }
                            else if(pattern3.test(dateKeys[i])){
                                stage = "Developing Fruit"
                                break
                            }
                            else if(pattern4.test(dateKeys[i])){
                                stage = "Harvestable"
                                break
                            }                        
                        }
                    }
                    else{
                        if(i != 0){
                            if(pattern1.test(dateKeys[i-2])){
                                stage = "Flowering"
                                break
                            }
                            else if(pattern2.test(dateKeys[i-2])){
                                stage = "Fruit Budding"
                                break
                            }
                            else if(pattern3.test(dateKeys[i-2])){
                                stage = "Developing Fruit"
                                break
                            }
                            else if(pattern4.test(dateKeys[i-2])){
                                stage = "Harvestable"
                                break
                            }                             
                        }
                    }                    
                }                                
            }            
            return stage ?? 'No Stage Set By Trader'  
        },
        getClass(proj){
            var projObj = this.getProjectss.filter((c) => {
                return parseInt(c.id) === parseInt(proj.id)
            })
            var contractObj = this.getContractss.filter((c) => {
                return parseInt(c.id) === parseInt(projObj[0].contract_id)
            })
            var prodTraderObj = this.getProduceTrader.filter((p) => {
                return parseInt(p.id) === parseInt(contractObj[0].produce_trader_id)
            })
            var arr = prodTraderObj[0].prod_name.split(' ')
            var container = null 
            if(arr.indexOf('(Class')){
                for(var i = 0; i < arr.length; i++) {
                    if(arr[i] == '(Class'){                    
                        container = arr[i].substring(1, arr[i].length) + ' ' + arr[i + 1].substring(0, 1)                                    
                        arr.pop()
                        arr.pop()                    
                        return container             
                    }                                                          
                }                
            }           
            return null
        },
        redirectToBidOrder(p){   
            var contractObj = null
            if(p.produce_inventory_qtyOnHand){
                contractObj = this.getContractss.filter((c) => {
                    return parseInt(p.farm_id) === parseInt(c.farm_id) && parseInt(p.produce_trader_id) === parseInt(c.produce_trader_id)
                })
                this.$router.push({ path: `/bid_order/on_hand/${contractObj[contractObj.length - 1].id}` }) 
            }
            else{
                contractObj = this.getContractss.filter((c) => {
                    return parseInt(p.contract_id) === parseInt(c.id)
                })
                this.$router.push({ path: `/bid_order/progress/${contractObj[0].id}` }) 
            }     
                                   
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
        ...mapGetters(['getProduceDataa', 'getProduceTrader', 'getProjectss', 'getContractss', 'getFarmProducess', 'getProduceYields']),
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
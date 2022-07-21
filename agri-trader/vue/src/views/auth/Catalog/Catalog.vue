<template>
  <div class="catalog">
    <div class="d-flex justify-content-between align-items-end">
        <h2>Produces</h2>
        <input type="search" name="" id="" placeholder="Search" class="form-control" style="width:200px">
    </div>    
    <p>Showing 1-12 of 20 results</p>        
    
    <div class="container-fluid p-0 m-0">
        <div class="row p-0 m-0">
            <div class="col-8">
                <select name="" id="" class="form-select" style="width:200px; float:right; border:0">
                    <option value="" disabled selected hidden>Default Sorting</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>                
            </div>
        </div>
        <div class="w-100" v-if="getContractData.contracts.length > 0">
            <div class="row p-0 m-0" v-for="(produce, index) in filtered" :key="index">
                <div class="col-3 p-0 mb-2 d-flex justify-content-center" v-for="(p, i) in produce" :key="i">
                    <div class="d-flex flex-column produce" style="width: 55%; height: 35vh;" @click="triggerModal(i)">
                        <div class="d-flex justify-content-center" style="height:75%; width:100%;">
                            <div style="height:100%; width:85%; background:green">
                                <img width="100%" height="100%" :src="[p != null ? require(`../../../../../public/storage/catalog_images/${getProdName(p)}.jpg`) : '']" alt="">
                            </div>
                        </div>
                        <p class="ms-3 mt-3">{{ getProdName(p) }}</p>
                        <template> 
                            <b-modal :id="'modal-' + i" size="xl" title="List of Traders">            
                            <div class="container-fluid w-100 d-flex flex-wrap">
                                <div v-if="getListOfTraders(p).length > 0" class="w-100">
                                <div class="row">
                                    <div class="col-6 mb-5" v-for="(prod, ind) in getListOfTraders(p)" :key="ind" style="height:20vh;">                
                                    <div class="produce" style="height:100%; border-radius: 50px;">
                                        <div class="" style="position: absolute; top:7%; left:8%; width:90%; height:80%">
                                            <div class="d-flex mb-2 align-items-center">
                                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:25px;" class="me-3"/>
                                                <h3>{{ getFarmName(prod) }}</h3>
                                            </div>                                                                                
                                            <h5 class="d-flex">Expected Harvest Date: <p class="ms-3">{{ getHarvestDate(prod) }}</p></h5>
                                            <h5 class="d-flex">Expected Kilos Harvested: <p class="ms-3">{{ getHarvestKilos(prod) + ' kgs' }}</p></h5>                                                                                            
                                        </div>                       
                                    </div>
                                    </div>                                                               
                                </div>
                                </div>
                                <div v-else class="w-100 d-flex justify-content-center align-items-center">
                                <h3>NO PRODUCES ADDED!</h3>
                                </div>         
                            </div> 
                            <template #modal-footer="{ ok, cancel }">            
                                <b-button size="md" variant="secondary" @click="cancel()">
                                Cancel
                                </b-button>             
                                <b-button size="md" variant="primary" @click="ok()" disabled id="okButton">
                                Add Produce
                                </b-button>                                                                 
                            </template>                      
                            </b-modal>
                        </template>                           
                    </div>
                </div>
            </div>
        </div>
                      
    </div>

    
    
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';



export default {
    name: "Catalog",
    created(){
        this.fetchAllCatalogProduces()
        .then(() => {
            if(this.getContractData.current_page){
                var query = `page=${this.getContractData.current_page}`               
                this.fetchAllCatalogProduces(query)                
            }
            this.filtered = this.filteredContractArray()   
            this.readyApp();
        })        
    },
    methods: {
        ...mapActions(['readyApp', 'fetchAllCatalogProduces']),
        filteredContractArray(){
            var filtered = [];
            var arr = this.getContractData.contracts;             
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        }, 
        getProdName(produce){            
            var produceObj = this.getProducess.filter((p) => {
                return parseInt(p.id) === parseInt(produce.produce_id)
            })
            return produceObj[0].prod_name
        },
        triggerModal(index){            
            this.$bvModal.show(`modal-${index}`)
        },
        getListOfTraders(produce){
            var contractObj = this.getContractss.filter((c) => {
                return parseInt(c.produce_id) === parseInt(produce.produce_id)
            })

            return contractObj
        },
        getFarmName(prod){
            var contractObj = this.getContractss.filter((c) => {
                return parseInt(c.id) === parseInt(prod.id)
            })

            return contractObj[0].farm_name
        },
        getHarvestDate(prod) {
            var projectObj = this.getProjectss.filter((p) => {
                return parseInt(p.contract_id) === parseInt(prod.id)
            })

            return projectObj[0].project_harvestableEnd ?? 'No Date Specified'
        },
        getHarvestKilos(prod){
            var contractObj = this.getContractss.filter((c) => {
                return parseInt(c.id) === parseInt(prod.id)
            })

            return contractObj[0].contract_estimatedHarvest            
        }
    },
    computed: {
        ...mapGetters(['getContractData', 'getProducess', 'getProjectss', 'getContractss']),
        getPages(){
            var pages = []
            for(var i = 1; i <= this.getContractData.last_page; i++){
                if(i >= this.getContractData.current_page){
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
.produce{
    background-color:transparent;
}

.produce:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}
</style>
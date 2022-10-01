<template>
  <div class="allProducesForOwner">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Produce List</h3>
        <div class="d-flex justify-content-between align-items-center" style="width:200px;">
            <router-link to="/produces/add" style="text-decoration:none; color:white;"><button class="btn btn-success" style="width:60px;">Add</button></router-link>
            <button>Edit</button>
            <button>Search</button>
        </div>
    </div>
    <div class="container-fluid w-100 d-flex flex-wrap" style="height:90%; position: relative;">
        <div class="w-100" v-if="getProduceDataForOwner.produces.length > 0">            
            <div class="row mb-5" v-for="(produce, index) in filtered" :key="index">                     
                <div class="col-4" style="height:30vh" v-for="(p, i) in produce" :key="i">                
                    <div class="d-flex produce" style="height:100%; border-radius:50px; position: relative;" @click="showProduce(p)">
                        <div class="" style="position: absolute; top:5%; left:5%; 
                        width:90%;">
                            <div class="d-flex mb-5">
                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:40px;" class="me-3"/>
                                <h3 class="mb-4">{{ getProdName(p) }}</h3>
                            </div>                                                                                
                            <h4 class="d-flex">Time to Harvest: <p class="ms-3">{{ p.prod_timeOfHarvest }}</p></h4>                            
                        </div>                                                
                    </div>
                </div>                
            </div>            
        </div>
        <div class="" style="top:42%; left:42%; position: absolute" v-else>
           <h2 class="text-black">No Produces Added!</h2>
        </div>  
        <!-- <pre>{{getFarmData}}</pre>         -->
        <div class="" style="position:absolute; right:5%; bottom:5%" v-if="getProduceDataForOwner.total > 6">
            <p class="text-center">Page {{ getProduceDataForOwner.current_page }} out of {{ getProduceDataForOwner.last_page }}</p>
            <ul class="pagination" id="pagination">
                <li :class="[getProduceDataForOwner.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>
                <li :class="[getProduceDataForOwner.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getProduceDataForOwner.links" :key="index" @click="showPage(page.label)">
                    <a class="page-link">{{page.label}}</a>
                </li>                
                <li :class="[getProduceDataForOwner.current_page != getProduceDataForOwner.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
            </ul>
        </div>
    </div>    
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'AllProducesForOwner',
    created(){
        this.fetchAllProducesForOwner()
        .then(() => {
            if(this.getProduceDataForOwner.current_page){
                var query = `page=${this.getProduceDataForOwner.current_page}`
                this.fetchAllProducesForOwner(query)
            }
            this.filtered = this.filteredProdArray()
            this.readyApp()
        })        
    },
    data(){
        return {
            filtered: null
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchAllProducesForOwner']),
        filteredProdArray(){
            var filtered = [];
            var arr = this.getProduceDataForOwner.produces;             
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        },
        getProdName(p){
            var prodObj = this.getProduceListForOwner.filter((pp) => {
                return parseInt(p.produce_id) === parseInt(pp.id)
            })
            var arr = p.prod_name.split(' ')
            if(arr.indexOf('(Class)')){
                arr.splice(arr.indexOf('(Class)') - 1, 0, prodObj[0].prod_type)
                return arr.join(' ')
            }
            else{
                return p.prod_name + ' ' + p.prod_type
            }            
        },
        showProduce(p){
            this.$router.push({ path: `/produce/owner/details/${p.id}` })
        },
        showPrevious(){
            var query = this.getProduceDataForOwner.prev_page_url.split('?')[1]
            this.fetchAllProducesForOwner(query)
            .then(() => {
                this.filtered = this.filteredProdArray()   
            })
            
        },
        showNext(){
            var query = this.getProduceDataForOwner.next_page_url.split('?')[1]
            this.fetchAllProducesForOwner(query)
            .then(() => {
                this.filtered = this.filteredProdArray()   
            })  
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchAllProducesForOwner(query)
            .then(() => {
                this.filtered = this.filteredProdArray()   
            })
        }
    },
    computed: {
        ...mapGetters(['getProduceDataForOwner', 'getProduceListForOwner'])
    }
}
</script>

<style scoped>

.produce{
    background-color:greenyellow;
}

.produce:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}

.page-item a{
    cursor:pointer;
}

</style>
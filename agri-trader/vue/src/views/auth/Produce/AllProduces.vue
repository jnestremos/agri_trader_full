<template>
  <div class="allProduces">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Produce List</h3>
        <div class="d-flex justify-content-between align-items-center" style="width:200px;">
            <router-link to="/produces/add" style="text-decoration:none; color:white;"><button class="btn btn-success" style="width:60px;">Add</button></router-link>
            <button>Edit</button>
            <button>Search</button>
        </div>
    </div>
    <div class="container-fluid w-100 d-flex flex-wrap" style="height:90%; position: relative;">
        <div class="w-100" v-if="getProduces.length > 0">            
            <div class="row mb-5" v-for="(produce, index) in filtered" :key="index">                     
                <div class="col-4 mt-3" style="height:30vh" v-for="(p, i) in produce" :key="i">                
                    <div class="d-flex produce" style="height:100%; border-radius:50px; position: relative;" @click="showProduce(p.produce_id)">
                        <div class="" style="position: absolute; top:5%; left:5%; 
                        width:90%;">
                            <div class="d-flex mb-4">
                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:40px;" class="me-3"/>
                                <h3 class="mb-4">{{ getProduceName(p) }}</h3>
                            </div>                                                                                
                            <h4 class="d-flex">Quantity: <p class="ms-3">{{ p.prod_totalQty }}</p></h4>
                            <h4 class="d-flex">Farms Associated: <p class="ms-3">{{ p.prod_numOfFarms }}</p></h4>
                            <h4 class="d-flex" v-if="p.prod_lastDateOfHarvest">Date of Harvest: <p class="ms-3">{{ p.prod_lastDateOfHarvest }}</p></h4>
                            <h4 class="d-flex" v-else>Date of Harvest: <p class="ms-3">No Harvest Date Recorded</p></h4>
                        </div>                                                
                    </div>
                </div>                
            </div>            
        </div>
        <div class="" style="top:42%; left:42%; position: absolute" v-else>
           <h2 class="text-black">No Produces Added!</h2>
        </div>  
        <!-- <pre>{{getFarmData}}</pre>         -->
        <div class="" style="position:absolute; right:5%; bottom:5%" v-if="getProduceData.total > 6">
            <p class="text-center">Page {{ getProduceData.current_page }} out of {{ getProduceData.last_page }}</p>
            <ul class="pagination" id="pagination">
                <li :class="[getProduceData.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>
                <li :class="[getProduceData.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getProduceData.links" :key="index" @click="showPage(page.label)">
                    <a class="page-link">{{page.label}}</a>
                </li>                
                <li :class="[getProduceData.current_page != getProduceData.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
            </ul>
        </div>
    </div>        
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: 'AllProduces',
    created(){        
        this.fetchAllProduces()
        .then(() => {
            //this.loading = true                           
            if(this.getProduceData.current_page){ 
                var query = `page=${this.getProduceData.current_page}`               
                this.fetchAllProduces(query)                
            }
            this.filtered = this.filteredProdArray()   
            this.readyApp();                                     
        })
    },
    data(){
        return {
            produce_id: null,
            produce_numOfGrades: 1,
            filtered: null
        }
    },
    computed: {
        ...mapGetters(['getProduceData', 'getProduces']),
        getPages(){
            var pages = []
            for(var i = 1; i <= this.getProduceData.last_page; i++){
                if(i >= this.getProduceData.current_page){
                    pages.push(i)
                }
            }
            return pages
        },                  
    },
    methods: {
        ...mapActions(['readyApp', 'fetchAllProduces', 'fetchProduce']),
        getProduceName(p){
            var arr = p.prod_name.split(' ')
            var prodObj = this.getProduceData.types.filter((pp) => {
                return parseInt(pp.id) === parseInt(p.produce_id)
            })
            if(arr.indexOf('(Class') != -1){
              arr.splice(arr.indexOf('(Class'), 0, prodObj[0].prod_type)             
              return arr.join(' ')
            }
            else {
                return p.prod_name + ' ' + prodObj[0].prod_type
            }
        },
        filteredProdArray(){
            var filtered = [];
            var arr = this.getProduces;             
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        },
        showProduce(id){
            console.log(id)
            this.fetchProduce(id)
            .then(() => {
                this.$router.push({path : `/produce/${id}`})
            })
            .catch((err) => {
                console.log(err)
            })
        },
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
<template>
  <div class="allProjects">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Projects</h3>
        <div class="d-flex justify-content-between align-items-center" style="width:200px;">
            <router-link v-if="getFarmsForProject" :to="getFarmsForProject.length > 0 ? '/project/add' : ''" :style="[getFarmsForProject.length > 0 ? {'cursor' : 'pointer'} : {'cursor' : 'default'}]"><button :class="[getFarmsForProject.length > 0 ? 'btn btn-success' : 'btn btn-secondary']" style="width:60px" :disabled="getFarmsForProject.length == 0">Add</button></router-link>
            <button>Edit</button>
            <button>Search</button>
        </div>
    </div>     
    <div class="container-fluid w-100 d-flex flex-wrap" style="height:90%; position: relative;">
        <div class="w-100" v-if="getProjects.length > 0">                 
            <div class="row mb-5" v-for="(project, index) in filtered" :key="index">                                   
                <div class="col-4" style="height:30vh" v-for="(p, i) in project" :key="i">                
                    <div class="d-flex project" style="height:100%; border-radius:50px; position: relative;" @click="showProject(p.id)">                    
                        <div class="" style="position: absolute; top:5%; left:5%; 
                        width:85%;">
                            <div class="d-flex mb-4">
                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:40px;" class="me-3"/>
                                <h4 class="mb-4">{{ getName(p) }} - {{ getCommenceDate(p)}} &nbsp; {{100 - getShareAmount(p) + "% / " + getShareAmount(p) + '%' }}</h4>
                            </div>                                                                
                            <h4 class="d-flex">Produce: <p class="ms-3">{{ getProduceName(p) }}</p></h4>
                            <h4 class="d-flex">Estimated Harvest (in kg): <p class="ms-3">{{ p.contract_estimatedHarvest + ' kg'}}</p></h4>
                        </div>                                                
                    </div>
                </div>                
            </div>            
        </div>
        <div class="" style="top:42%; left:42%; position: absolute" v-else>
           <h2 class="text-black">No Projects Added!</h2>
        </div>  
        <div class="" style="position:absolute; right:5%; bottom:5%" v-if="getProjectData.total > 6">
            <p class="text-center">Page {{ getProjectData.current_page }} out of {{ getProjectData.last_page }}</p>
            <ul class="pagination" id="pagination">
                <li :class="[getProjectData.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>
                <li :class="[getProjectData.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getProjectData.links" :key="index" @click="showPage(page.label)">
                    <a class="page-link">{{page.label}}</a>
                </li>                
                <li :class="[getProjectData.current_page != getProjectData.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
            </ul>
        </div>
    </div>       
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "AllProjects",
    created(){
        this.fetchAllProjects()
        .then(() => {
            if(this.getProjects.current_page){ 
                var query = `page=${this.getProjectData.current_page}`               
                this.fetchAllProjects(query)                
            }        
            this.filtered = this.filteredProjectArray() 
            this.fetchAllFarmsForProject() 
            .then(() => {                
                this.readyApp()
            })                
        })
        
    },
    data(){
        return {
            filtered: null
        }
    },
    computed: {
        ...mapGetters(['getProjects', 'getProjectData', 'getFarmsForProject', 'getFarmList' , 'getOwnerList', 'getProduceList', 'getShareList', 'getDateList']),        
    },   
    methods: {
        ...mapActions(['readyApp', 'fetchAllProjects', 'fetchAllFarmsForProject']),
        filteredProjectArray(){
            var filtered = [];
            var arr = this.getProjects;             
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        },
        showPrevious(){
            var query = this.getProjectData.prev_page_url.split('?')[1]
            this.fetchAllFarms(query)
            .then(() => {
                this.filtered = this.filteredProjectArray()   
            })            
        },
        showNext(){
            var query = this.getProjectData.next_page_url.split('?')[1]
            this.fetchAllFarms(query)
            .then(() => {
                this.filtered = this.filteredProjectArray()   
            })  
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchAllFarms(query)
            .then(() => {
                this.filtered = this.filteredProjectArray()   
            })
        },  
        showProject(id){
            this.$router.push({ path: `/projects/${id}` })
        },
        getName(contract){
            var farmObj = this.getFarmList.filter((f) => {
                return parseInt(f.id) === parseInt(contract.farm_id)
            })
            var distObj = this.getOwnerList.filter((o) => {
                return parseInt(o.id) === farmObj[0].farm_owner_id
            })            
            return distObj[0].owner_firstName + ' ' + distObj[0].owner_lastName
        },
        getCommenceDate(contract){
            var projObj = this.getDateList.filter((p) => {
                return parseInt(p.contract_id) === parseInt(contract.id)
            })
            return projObj[0].project_commenceDate
        },                              
        getShareAmount(contract){
            var shareObj = this.getShareList.filter((s) => {
                return parseInt(contract.contract_share_id) === parseInt(s.id)
            })
            return parseFloat(shareObj[0].contractShare_amount)
        },
        getShareType(contract){
            var shareObj = this.getShareList.filter((s) => {
                return parseInt(contract.contract_share_id) === parseInt(s.id)
            })
            return shareObj[0].contractShare_type
        },
        getProduceName(contract){
            var prodObj = this.getProduceList.filter((p) => {
                return parseInt(contract.produce_id) === parseInt(p.id)
            })
            return prodObj[0].prod_name
        }
    }
}
</script>

<style scoped>

.project{
    background-color:greenyellow;
}

.project:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}

</style>
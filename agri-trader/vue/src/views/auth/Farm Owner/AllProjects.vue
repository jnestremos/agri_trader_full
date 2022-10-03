<template>
  <div class="allProjectsOwner">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Projects</h3>  
        <button class="btn btn-primary">Show Completed Projects</button>      
    </div> 
    <div class="container-fluid w-100 d-flex flex-wrap" style="height:90%; position: relative;">
        <div class="w-100" v-if="getProjectDataForOwner.projects.length > 0">
            <div class="row mb-5" v-for="(project, index) in filtered" :key="index">
                <div class="col-4" style="height:35vh" v-for="(p, i) in project" :key="i">
                    <div class="d-flex project" style="height:100%; border-radius:50px; position: relative;" @click="showProject(p.id)">
                        <div class="" style="position: absolute; top:5%; left:5%; 
                        width:85%;"> 
                            <div class="d-flex mb-4">
                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:40px;" class="me-3"/>
                                <h4 class="mb-4">{{ p.farm_name + ' Project' }}</h4>
                            </div>
                            <h4 class="d-flex">Date Commenced: <p class="ms-3">{{ p.created_at.split('T')[0] }}</p></h4>
                            <h4 class="d-flex">Contract Share: <p class="ms-3">{{ getShare(p) }}</p></h4>
                            <h4 class="d-flex">Produce: <p class="ms-3">{{ getProduce(p) }}</p></h4>
                            <h4 class="d-flex">Estimated Harvest (in kg): <p class="ms-3">{{ p.contract_estimatedHarvest + ' kg/s' }}</p></h4>
                        </div>
                    </div>
                </div>                                
            </div>                        
        </div>
        <div class="" style="top:42%; left:42%; position: absolute" v-else>
            <h2 class="text-black">No Projects Added!</h2>
        </div>
        <div class="" style="position:absolute; right:5%; bottom:5%" v-if="getProjectDataForOwner.total > 6">
            <p class="text-center">Page {{ getProjectDataForOwner.current_page }} out of {{ getProjectDataForOwner.last_page }}</p>
            <ul class="pagination" id="pagination">
                <li :class="[getProjectDataForOwner.current_page != 1 ? 'page-item ' : 'page-item disabled']"><a class="page-link" @click="showPrevious()">Previous</a></li>                
                <li :class="[getProjectDataForOwner.current_page == page.label ? 'page-item active' : 'page-item']" v-for="(page,index) in getProjectDataForOwner.links" :key="index" @click="showPage(page.label)">
                    <a class="page-link">{{page.label}}</a>
                </li> 
                <li :class="[getProjectDataForOwner.current_page != getProjectDataForOwner.last_page ?'page-item ' : 'page-item disabled']" @click="showNext()"><a class="page-link">Next</a></li>
            </ul>
        </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'AllProjectsOwner',
    created(){
        this.fetchAllProjectsForOwner()
        .then(() => {
            if(this.getProjectDataForOwner.current_page){
                var query = `page=${this.getProjectData.current_page}`
                this.fetchAllProjectsForOwner(query) 
            }
            this.filtered = this.filteredProjectArray()  
            this.readyApp()           
        })        
    },
    data(){
        return {
            filtered: null
        }
    },
    methods:{
        ...mapActions(['readyApp', 'fetchAllProjectsForOwner']),
        showProject(id){
            this.$router.push({ path: `/projects/owner/${id}` })
        },
        filteredProjectArray(){
            var filtered = [];
            var arr = this.getProjectDataForOwner.projects;             
            var arr1 = arr.slice(0,3)
            var arr2 = arr.slice(3,arr.length)

            filtered.push(arr1)
            if(arr2.length > 0){
                filtered.push(arr2)
            }            
            return filtered
        },
        getShare(p){
            var shareObj = this.getShareListForOwner.filter((s) => {
                return parseInt(s.id) === parseInt(p.contract_share_id)
            })
            if(shareObj[0].contractShare_type == 'Amount'){
                return shareObj[0].contractShare_amount
            }
            else if(shareObj[0].contractShare_type == 'Percentage'){
                return 100 - shareObj[0].contractShare_amount + '%  /' + shareObj[0].contractShare_amount + '%'
            }
        },
        getProduce(p){
            var prodObj = this.getProduceListForOwner.filter((pp) => {
                return parseInt(p.produce_id) === parseInt(pp.id)
            })
            return prodObj[0].prod_name + ' ' + prodObj[0].prod_type
        },
        showPrevious(){
            var query = this.getProjectDataForOwner.prev_page_url.split('?')[1]
            this.fetchAllProjectsForOwner(query)
            .then(() => {
                this.filtered = this.filteredProjectArray()   
            })            
        },
        showNext(){
            var query = this.getProjectDataForOwner.next_page_url.split('?')[1]
            this.fetchAllProjectsForOwner(query)
            .then(() => {
                this.filtered = this.filteredProjectArray()   
            })  
        },
        showPage(page){
            var query = `page=${page}`
            this.fetchAllProjectsForOwner(query)
            .then(() => {
                this.filtered = this.filteredProjectArray()   
            })
        },  
    },
    computed:{
        ...mapGetters([
            'getProjectDataForOwner', 
            'getFarmListForOwner',
            'getTraderListForOwner',
            'getProduceListForOwner',
            'getShareListForOwner',
            'getDateListForOwner',
        ])
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
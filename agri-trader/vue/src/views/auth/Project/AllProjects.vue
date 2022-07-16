<template>
  <div class="allProjects">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Projects</h3>
        <div class="d-flex justify-content-between align-items-center" style="width:200px;">
            <router-link to="/project/add"><button class="btn btn-success" style="width:60px;">Add</button></router-link>
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
                                <h3 class="mb-4">asdasd</h3>
                            </div>                                                                                
                            <h4 class="d-flex">Owner: <p class="ms-3">asdasd</p></h4>
                            <h4 class="d-flex">Produces Owned: <p class="ms-3">asdasd</p></h4>
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
            this.readyApp()
        })
        
    },
    data(){
        return {
            filtered: null
        }
    },
    computed: {
        ...mapGetters(['getProjects', 'getProjectData'])
    },
    methods: {
        ...mapActions(['readyApp', 'fetchAllProjects']),
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
        }                        
    }
}
</script>

<style>

.project{
    background-color:greenyellow;
}

.project:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}

</style>
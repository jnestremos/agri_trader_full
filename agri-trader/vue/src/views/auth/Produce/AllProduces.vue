<template>
  <div class="allProduces">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Produce List</h3>
        <div class="d-flex justify-content-between align-items-center" style="width:200px;">
            <button class="btn btn-success" style="width:60px;"><router-link to="/produces/add" style="text-decoration:none; color:white;">Add</router-link></button>
            <button>Edit</button>
            <button>Search</button>
        </div>
    </div>    
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: 'AllProduces',
    created(){
        this.readyApp();
        this.fetchAllProduces()
        .then(() => {
            //this.loading = true                           
            if(this.getProduceData.current_page){ 
                var query = `page=${this.getProduceData.current_page}`               
                this.fetchAllProduces(query)                
            }
            this.filtered = this.filteredFarmArray()   
                     
        })
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
        filteredFarmArray(){
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

<style>

</style>
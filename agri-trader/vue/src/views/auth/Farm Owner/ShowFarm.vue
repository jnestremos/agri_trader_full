<template>
  <div class="showFarmOwner">
    <div class="showFarm p-4" style="position:relative; height:100%; overflow-y: hidden;">
        <div class="grid">
            <div class="farm-image">
                <!-- <img alt="" width="80%" style="border-radius:10%; float:right;" :src="[getFarmInfoForOwner.farm.farm_imageUrl ? require(`../../../../../public/storage/farms/${getFarmInfoForOwner.farm.farm_imageUrl}`) : '']" -->
                picture here {{ $route.params.id }}
            </div>
            <div class="location d-flex flex-column justify-content-center">              
                <h2 v-if="getFarmInfoForOwner.farm">{{ getFarmInfoForOwner.farm.farm_name }}</h2>
                <p v-if="getFarmInfoForOwner.farm">{{ getFarmInfoForOwner.farm.farm_hectares }} hectares of land</p>
            </div>
            <div v-if="getFarmInfoForOwner.projects && getFarmInfoForOwner.projects.length == 0" class="trader_terminate d-flex justify-content-center align-items-center">
                <button class="btn btn-danger">Terminate Partnership</button>
            </div>
            <!-- <div class="edit-profile">
                <p class="me-3">Edit Profile</p>
            </div> -->
            <div class="address">
                <h5>Address</h5>       
                <p v-if="getFarmInfoForOwner.farm_address">{{ getFarmInfoForOwner.farm_address.farm_address }}</p>
                <div class="d-flex justify-content-between">
                <div>
                    <h5>Province:</h5>
                    <p v-if="getFarmInfoForOwner.farm_address">{{ getFarmInfoForOwner.farm_address.farm_province }}</p>
                </div>
                <div>
                    <h5>City:</h5>
                    <p v-if="getFarmInfoForOwner.farm_address">{{ getFarmInfoForOwner.farm_address.farm_city }}</p>
                </div>
                <div>
                    <h5>Zipcode:</h5>
                    <p v-if="getFarmInfoForOwner.farm_address">{{ getFarmInfoForOwner.farm_address.farm_zipcode }}</p>
                </div>        
                </div>
                <p>Check in Google Maps</p>
            </div> 
            <div class="farm-partners d-flex align-items-center justify-content-center" style="position:relative">
                <h2 style="position:absolute; right:2%; top:2%;">+</h2>
                <h3>NO FARM PARTNERS</h3>                
            </div> 
            <div class="primary-image">
                <img src="https://media.istockphoto.com/vectors/sample-stamp-sample-square-grunge-sign-sample-vector-id1172218730?k=20&m=1172218730&s=612x612&w=0&h=0iTFUtb2WJtRAfg4Lbx7oh2lfVtH5pBsQAMxhCWKZH0="
                alt="" width="100%">
            </div>   
            <div class="second-image">
                <img src="https://media.istockphoto.com/vectors/sample-stamp-sample-square-grunge-sign-sample-vector-id1172218730?k=20&m=1172218730&s=612x612&w=0&h=0iTFUtb2WJtRAfg4Lbx7oh2lfVtH5pBsQAMxhCWKZH0="
                alt="" width="100%">         
            </div>   
            <div class="third-image">
                <img src="https://media.istockphoto.com/vectors/sample-stamp-sample-square-grunge-sign-sample-vector-id1172218730?k=20&m=1172218730&s=612x612&w=0&h=0iTFUtb2WJtRAfg4Lbx7oh2lfVtH5pBsQAMxhCWKZH0="
                alt="" width="100%">
            </div>
            <div class="farm-produces" style="position:relative;">
                <!-- <h2 style="float:right; cursor:pointer;" @click="triggerModal()">+</h2>                         -->
                <div style="clear:right;" class="mt-3" v-if="getFarmInfoForOwner.farm_produces && getFarmInfoForOwner.farm_produces.length > 0">
                    <div class="container-fluid w-100 d-flex">
                        <div class="w-100">
                            <div class="row">
                                <div class="col-6 mb-5" v-for="(produce, index) in getFarmInfoForOwner.farm_produces" :key="index" style="height:10vh;">
                                    <div class="produce d-flex justify-content-center align-items-center" v-b-tooltip.hover :title="produce.on_hand_qty + ' kgs'" style="height:100%; border-radius: 20px; cursor:default">                                                            
                                        <p class="mt-2" style="font-weight:bold; text-align: center; font-size:0.8rem">{{ produce.prod_name }}</p>                                                                                                                                                                                               
                                    </div>                      
                                </div>                  
                            </div>
                        </div>
                    </div>          
                </div>
                <h4 v-else style="position:absolute; top:50%; left: 25%;">NO PRODUCES</h4>
            </div>
            <div class="title-num">
                <h5>Title Number</h5>
                <p v-if="getFarmInfoForOwner.farm">{{ getFarmInfoForOwner.farm.farm_titleNum }}</p>
            </div>        
            <div class="farm-owner">
                <h5>Farm Owner</h5>
                <p v-if="getFarmInfoForOwner.owner">{{ getFarmInfoForOwner.owner.owner_firstName }} {{ getFarmInfoForOwner.owner.owner_lastName }}</p>
            </div>                                                                  
        </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'ShowFarmOwner',
    created(){
        this.fetchFarmForOwner(this.$route.params.id)
        .then(() => {
            this.readyApp()
        })        
    },
    data(){
        return {
            filtered: null
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchFarmForOwner']),
    },
    computed: {
        ...mapGetters(['getFarmInfoForOwner'])
    }
}
</script>

<style scoped>
.grid{  
    width:95%;      
    display:grid;
    gap: 1rem;
    grid-template-columns: 1.2fr repeat(4, 0.6fr) 1.4fr;
    grid-template-rows: 0.5fr 0.5fr repeat(2, 11rem) 0.4fr;
    position:absolute;     

}


.farm-image{
    grid-column-start: 1;
    grid-column-end: span 1;
}

.location{
    grid-column-start: 2;    
}

.trader_terminate{
    grid-column-start: 3;
}


.edit-profile :first-child{
    float:right; 
}

.address{
    grid-column-start: 2;
    grid-column-end: span 3;  
}

.farm-partners{
    grid-column-start: 5;
    grid-column-end: span 2;
    background:lightgreen;
}

.farm-produces{
    grid-row-start: 2;
    grid-row-end: span 3;
    background:lightgreen;
    overflow-y: scroll;
    overflow-x: hidden;
}

.primary-image{
    background:red;
    grid-column-start:2;
    grid-column-end:6;  
    grid-row-start: 3;
    grid-row-end: 4;
}

.second-image{ 
    grid-column-start:6;   
    grid-row-start:3;    
}
.third-image{
    grid-column-start:6;   
    grid-row-start:4;    
}



.title-num{
    grid-column-start:2;
    grid-row-start: 5
}

.farm-owner{
    grid-column-start:3;
    grid-row-start: 5
}
.produce{
    background-color:greenyellow;
}

.produce:hover{
    transition: 0.5s;
    background-color:grey;
    cursor:pointer;
}

.produce.clicked{
    background-color:grey;
}

</style>
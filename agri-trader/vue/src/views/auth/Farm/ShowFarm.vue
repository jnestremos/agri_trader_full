<template>
  <div class="showFarm" style="position:relative; height:100%;">
    <router-link to="/farms" style="position:absolute; top: 1%; left:1%;"><h3>&LeftTriangle; Back</h3></router-link>
    <div class="grid">
      <div class="farm-image">        
        <img alt="" width="100%" style="border-radius:10%;" :src="[getFarmDetails.farm.farm_imageUrl ? require(`../../../../../public/storage/farms/${getFarmDetails.farm.farm_imageUrl}`) : '']"
        v-if="getFarmDetails.farm.farm_imageUrl">
      </div>
      <div class="location">              
        <h2>{{ getFarmDetails.farm.farm_name }}</h2>
        <p>{{ getFarmDetails.farm.farm_hectares }} hectares of land</p>
      </div>
      <div class="edit-profile">
        <p>Edit Profile</p>
      </div>      
      <div class="address">
        <h5>Address</h5>       
        <p>{{ getFarmDetails.address.farm_address }}</p>
        <div class="d-flex justify-content-between">
          <div>
            <h5>Province:</h5>
            <p>{{ getFarmDetails.address.farm_province }}</p>
          </div>
          <div>
            <h5>City:</h5>
            <p>{{ getFarmDetails.address.farm_city }}</p>
          </div>
          <div>
            <h5>Zipcode:</h5>
            <p>{{ getFarmDetails.address.farm_zipcode }}</p>
          </div>        
        </div>
        <p>Check in Google Maps</p>
      </div>
      <div class="farm-partners d-flex align-items-center justify-content-center" style="position:relative">
        <h2 style="position:absolute; right:2%; top:2%;">+</h2>
        <h3 v-if="getFarmDetails.farm_partners"></h3>
        <h3 v-else>NO FARM PARTNERS</h3>
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
        <h2 style="float:right; cursor:pointer;" @click="triggerModal()">+</h2>        
        <template> 
          <b-modal id="modal-1" size="xl" title="Produces" scrollable>            
            <div class="container-fluid w-100 d-flex flex-wrap">
              <div v-if="getFilteredProduces.length > 0" class="w-100">
                <div class="row">
                  <div class="col-6 mb-5" v-for="(produce, index) in getFilteredProduces" :key="index" style="height:20vh;" @click="setProduce(produce.id)">                
                    <div class="produce" style="height:100%; border-radius: 50px;" :id="'produce'+produce.id">
                        <div class="" style="position: absolute; top:2%; left:8%; width:90%; height:80%">
                            <div class="d-flex mb-2 align-items-center">
                                <font-awesome-icon icon="fa-brands fa-pagelines" style="font-size:25px;" class="me-3"/>
                                <h3>{{  produce.prod_name }}</h3>
                            </div>                                                                                
                            <h5 class="d-flex">Time to Harvest: <p class="ms-3">{{ produce.prod_timeOfHarvest }}</p></h5>
                            <h5 class="d-flex" v-if="produce.produce_numOfGrades > 1">Grades: <p class="ms-3">A, B, C</p></h5>                          
                            <h5 class="d-flex" v-else>Grades: <p class="ms-3">None</p></h5>                          
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
              <!-- Emulate built in modal header close button action -->
              <b-button size="md" variant="secondary" @click="cancel()">
                Cancel
              </b-button>             
              <b-button size="md" variant="primary" @click="ok()" disabled id="okButton">
                Add Produce
              </b-button>                                                                 
            </template>                      
          </b-modal>
        </template>
        <div style="clear:right;" v-if="getFarmProduces.length > 0">
          {{ getFarmProduces[0].farm_id }}
        </div>
        <h4 v-else style="position:absolute; top:50%; left: 10%;">NO PRODUCES</h4>
      </div>
      <div class="title-num">
        <h5>Title Number</h5>
        <p>{{ getFarmDetails.farm.farm_titleNum }}</p>
      </div>        
      <div class="farm-owner">
        <h5>Farm Owner</h5>
        <p>{{ getFarmDetails.owner.owner_firstName }} {{ getFarmDetails.owner.owner_lastName }}</p>
      </div>        
    </div>

   
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'ShowFarm',
    data(){
      return {
        filtered: null,
        id: null
      }
    },
    created(){          
      this.fetchFarm(this.$route.params.id)
      .then(() => {
        this.produceSelection(this.$route.params.id)
        .then(() => {
          this.fetchFarmProduces(this.$route.params.id)
          .then(() => {
            this.readyApp(); 
          })          
        })                    
      })                                   
    },
    mounted(){      
      this.$root.$on('bv::modal::hide', (bvEvent) => {      
        if(bvEvent.trigger == 'ok'){
         this.assignProduce()                   
        }
        this.id = null                
      })     
               
    },
    watch: {  
      id(newVal, oldVal){        
        var okButton = document.getElementById('okButton')        
        if(newVal == null){
          okButton.className = 'btn btn-primary btn-md disabled'
          okButton.setAttribute('disabled', 'disabled')          
        }
        else{
           okButton.className = 'btn btn-primary btn-md'
           okButton.removeAttribute('disabled')
        }
        if(oldVal != null){
          var produce = document.getElementById(`produce${oldVal}`)
          produce.className = 'produce'
        }        
      },      
    },
    computed:{
      ...mapGetters(['getFarmDetails', 'getProduceData', 'getFilteredProduces', 'getFarmProduces'])
    },
    methods:{
      ...mapActions(['readyApp', 'fetchFarm', 'fetchAllProduces', 'produceSelection', 'addProduceToFarm', 'fetchFarmProduces']), 
      triggerModal(){
        this.$bvModal.show('modal-1')
      },
      filteredProdArray(){
        var filtered = [];
        var arr = this.getFilteredProduces;             
        var arr1 = arr.slice(0,3)
        var arr2 = arr.slice(3,arr.length)

        filtered.push(arr1)
        if(arr2.length > 0){
            filtered.push(arr2)
        }            
        return filtered
      },           
      setProduce(id){
        var produce = document.getElementById(`produce${id}`)
        produce.className = 'produce clicked'
        this.id = id       
      },
      assignProduce(){        
        var data = {
          id: this.id, 
          farm_id: this.$route.params.id       
        }
        this.addProduceToFarm(data)    
        .then(() => {
          location.reload()
        })    
        .catch((err) => {
          console.log(err)
        })
      }
    }
}
</script>

<style>
.grid{
  width:78%;    
  display:grid;
  gap: 1rem;
  grid-template-columns: repeat(6, 1fr);
  position:absolute;     
  top: 6%;
  left: 10%;    
}


.farm-image{
  grid-column-start: 1;
  grid-column-end: span 1;
}

.location{
  grid-column-start: 2;
  grid-column-start: span 4;
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
}

.primary-image{
  background:red;
  grid-column-start:2;
  grid-column-end:span 4;  
  grid-row-end:span 2;
}

.second-image{ 
  grid-column-start:6;   
  grid-row-start:3;    
}
.third-image{
  grid-column-start:6;   
  grid-row-start:4;    
}

.title-num, .farm-owner{
  margin-top:1.5rem
}

.title-num{
  grid-column-start:2;
}

.farm-owner{
  grid-column-start:3;
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
<template>
  <div class="showBidOrder h-100 w-100">    
    <form @submit.prevent="sendBid()" action="" class="h-100 w-100 p-3" style="position:relative;">
      <div class="d-flex align-items-baseline justify-content-between" style="width:70%;">
        <div class="d-flex align-items-baseline justify-content-between" style="width:30%">
          <h2>{{ getProgressData.prod_name }}</h2>
          <!-- <div class="d-flex align-items-baseline" v-if="getProgressData.prod_class">
            <label for="" class="form-label me-4">Class</label>
            <select name="" class="form-select" id="" style="width:100px">
              <option value="" :selected="getProgressData.prod_class == 'Class A'">A</option>
              <option value="" :selected="getProgressData.prod_class == 'Class B'">B</option>
              <option value="" :selected="getProgressData.prod_class == 'Class C'">C</option>
            </select>
          </div>           -->
        </div>
        <!-- <a href="">Upcoming Projects</a> -->
      </div>
      <p>{{ getProgressData.prod_class }}</p>
      <p>Transaction # 123-345-546</p>
      <div class="d-flex align-items-baseline justify-content-between" style="width:60%;">
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Trader Name: </h5>
          <p>{{ getProgressData.trader_name }}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Expected Date of Harvest: </h5>
          <p>{{ getProgressDate }}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Estimated Harvest: </h5>
          <p>{{ getProgressData.contract_estimatedHarvest + " kg"}}</p>
        </div>
      </div>
      <div class="d-flex align-items-baseline justify-content-between" style="width:60%;">
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Contact Number: </h5>
          <p>{{ getProgressData.trader_contactNum }}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Email Address: </h5>
          <p>{{ getProgressData.trader_email }}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Farm Originated: </h5>
          <p>{{ getProgressData.farm_name }}</p>
        </div>
      </div>
      <div class="d-flex align-items-center justify-content-between w-100 mb-2">
        <div style="height:45vh; width:50%; background:grey">
            Graph here
        </div>
        <div style="height:45vh; background:grey; width:50%">
          <b-carousel id="carousel-1" :interval="4000"
          controls indicators background="#ababab" style="text-shadow: 1px 1px 2px #333; width:100%; height:100%;">
          
            <b-carousel-slide style="height:45vh; width:100%">
              <template #img>
                <img class="d-block img-fluid w-100" style="width:100%; height:100%; object-fit:cover" src="https://picsum.photos/1024/480/?image=55" alt="image slot">
              </template>
            </b-carousel-slide>        
          </b-carousel>         
        </div>       
      </div>
      <div class="d-flex align-items-baseline justify-content-between" style="width:60%;">
        <div class="d-flex align-items-baseline">
          <h5 class="me-2">Asking Price: </h5>
          <input type="number" class="form-control" style="width:150px;" v-model="data.order_initialPrice">
        </div>
        <div class="d-flex align-items-baseline">
          <h5 class="form-h5 me-2">Expected Dates Needed: </h5>
          <input type="date" class="form-control me-3" style="width:150px;" onkeydown="return false" v-model="data.order_dateNeededFrom">
          <input type="date" class="form-control" style="width:150px;" onkeydown="return false" v-model="data.order_dateNeededTo">
        </div>          
      </div>
      <div class="d-flex align-items-baseline mt-3">
        <h5 class="me-4">Quantity Needed:</h5>
        <div class="d-flex align-items-baseline">
          <input type="number" class="form-control me-4" id="minQty" style="width:100px;" value="1" min="1" @change="setMinQty($event)" onkeydown="return false">
          <h5 class="me-4">To</h5>
          <input type="number" class="form-control" id="maxQty" style="width:100px;" value="5" min="1" @change="setMaxQty($event)" onkeydown="return false">
        </div>
      </div>
      <div class="d-flex align-items-baseline justify-content-between mt-3" style="width:60%">
        <div class="d-flex align-items-baseline">
          <h5 class="me-4">Current Stage:</h5>          
          <p v-if="currentStage">{{ currentStage }}</p>
          <p v-else>No Stage Indicated</p>
        </div>
        <div class="d-flex align-items-baseline">
          <h5 class="me-4">Total Price:</h5>          
          <h5>{{ data.project_bid_total }}</h5>
        </div>
      </div>
      <input type="submit" value="Send Order Bid" class="btn btn-success" style="position:absolute; bottom:0; right:5%;">
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { add, format, isEqual, isAfter, isBefore } from 'date-fns'
export default {
    name: 'ShowBidOrder',
    created(){
        this.fetchProjectProgress(this.$route.params.id)
        .then(() => {
          var keys = Object.keys(this.getProgressData);          
          var dateKeys = keys.splice(9,8)   
          var check = false          
          for(var i = 0; i < dateKeys.length; i = i + 2){
            if(this.getProgressData[dateKeys[i]]){
              var year = this.getProgressData[dateKeys[i]].split('-')[0]
              var month = this.getProgressData[dateKeys[i]].split('-')[1]
              var day = this.getProgressData[dateKeys[i]].split('-')[2]
              var year1 = this.getProgressData[dateKeys[i+1]].split('-')[0]
              var month1 = this.getProgressData[dateKeys[i+1]].split('-')[1]
              var day1 = this.getProgressData[dateKeys[i+1]].split('-')[2]  
              var checkStartDate = isBefore(new Date(year, month-1, day, 8,0,0,0), new Date().setHours(8,0,0,0))
              var checkEndDate = isAfter(new Date(year1, month1-1, day1, 8,0,0,0), new Date().setHours(8,0,0,0))
              var isStartEqual = isEqual(new Date(year, month-1, day, 8,0,0,0), new Date().setHours(8,0,0,0))
              var isEndEqual = isEqual(new Date(year1, month1-1, day1, 8,0,0,0), new Date().setHours(8,0,0,0))              
              if(!check){  
                console.log(checkStartDate)              
                if((checkStartDate && checkEndDate) || (isStartEqual || isEndEqual)){
                  console.log(dateKeys[i])
                  this.currentStage =  dateKeys[i] 
                if(dateKeys[i] == 'project_floweringStart'){
                  this.currentStage =  'Flowering'                                      
                }
                else if(dateKeys[i] == 'project_fruitBuddingStart'){
                  this.currentStage =  'Fruit Budding'                                                   
                }
                else if(dateKeys[i] == 'project_devFruitStart'){
                  this.currentStage =  'Developing Fruit'                                              
                }
                else if(dateKeys[i] == 'project_harvestableStart'){
                  this.currentStage =  'Harvestable'                                                  
                }                    
                  check = true
                }                                                                
              }                        
            }          
          } 
          this.data.order_initialPrice = this.getProgressData.contract_estimatedPrice.toFixed(2);
          this.data.project_bid_total = (this.data.order_initialPrice * this.data.project_bid_minQty).toFixed(2) + ' - ' + (this.data.order_initialPrice * this.data.project_bid_maxQty).toFixed(2)                                                    
          if(this.getProgressData.prod_class){
            var arr = this.getProgressData.prod_class.split(' ')
            this.data.order_grade = arr[1].split('').pop()[0]
          }          
          this.data.project_id = this.$route.params.id
          this.data.trader_id = this.getProgressData.trader_id
          this.readyApp()
        })        
        
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProjectProgress' ,'sendBidOrder']),
        setMinQty(e){
          var maxQty = document.getElementById("maxQty")          
          this.data.project_bid_minQty = parseInt(e.target.value)
          maxQty.value = parseInt(e.target.value) + 5            
          this.data.project_bid_maxQty = parseInt(maxQty.value)
          this.data.project_bid_total = (this.data.order_initialPrice * this.data.project_bid_minQty).toFixed(2) + ' - ' + (this.data.order_initialPrice * this.data.project_bid_maxQty).toFixed(2)  
        },
        setMaxQty(e){
          var minQty = document.getElementById("minQty")
          if(parseInt(e.target.value) <= parseInt(minQty.value)){
            e.target.value = parseInt(minQty.value) + 5
          }        
          this.data.project_bid_maxQty = parseInt(e.target.value)
          this.data.project_bid_total = (this.data.order_initialPrice * this.data.project_bid_minQty).toFixed(2) + ' - ' + (this.data.order_initialPrice * this.data.project_bid_maxQty).toFixed(2)          
        },
        sendBid(){
          this.sendBidOrder(this.data)
          .then(() => {
            this.$toastr.s('Bid Order Sent Successfully!')
            setTimeout(() => {
              this.$router.push({ name: 'Catalog' })
            }, 5000)            
          })
          .catch((err) => {
            console.log(err)
            if(err.response.data.errors){
              this.errors = err.response.data.errors
              for(var error in this.errors){
                this.$toastr.e(this.errors[error][0])
              }
            }
            else{
              console.log(err.response.data.error.toString())
              this.errors = err.response.data.error                          
              this.$toastr.e(this.errors.toString())
            }            
          })
        }
    },
    computed:{
      ...mapGetters(['getProgressData']),
      getProgressDate(){
        if(!this.getProgressData.project_commenceDate){
          var harvestDate = add(new Date(this.getProgressData.project_commenceDate), {
              weeks: 1
          })
          var formattedDate = format(harvestDate, 'yyyy-MM-dd');       
          return formattedDate                    
        }
        else{
          return this.getProgressData.project_commenceDate
        }
      },      
    },
    data(){
      return {      
        currentStage: null,
        errors: null,
        data: {
          order_grade: null,
          project_id: null,
          trader_id: null,
          order_dateNeededTo: null,
          order_dateNeededFrom: null,
          bid_status_id: 1,
          order_initialPrice: null,
          project_bid_minQty: 1,
          project_bid_maxQty: 5,
          project_bid_total: null
        },
      }
    }    
}
</script>

<style>

</style>
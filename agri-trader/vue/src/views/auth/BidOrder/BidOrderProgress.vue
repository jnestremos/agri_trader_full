<template>
  <div class="bidOrderProgress p-3 h-100 w-100" style="position:relative;">
    <h2>{{ getProgressData.prod_name }}</h2>
    <p>{{ getProgressData.prod_class ? getProgressData.prod_class : '' }}</p>    
    <div class="d-flex justify-content-between align-items-baseline" style="width:50%;">
      <div class="d-flex align-items-baseline">
        <h5 class="me-2">Trader Name:</h5>
        <p>{{ getProgressData.trader_name }}</p>
      </div>
      <div class="d-flex justify-content-start align-items-baseline" style="width:45%">
        <h5 class="me-2">Expected Date of Harvest:</h5>
        <p>{{ getProgressDate }}</p>
      </div>          
    </div>
    <div class="d-flex justify-content-between align-items-baseline mb-4" style="width:50%;">
      <div class="d-flex align-items-baseline">
        <h5 class="me-2">Contact Number:</h5>
        <p>{{ getProgressData.trader_contactNum }}</p>
      </div>
      <div class="d-flex justify-content-start align-items-baseline" style="width:45%">
        <h5 class="me-2">Email Address:</h5>
        <p>{{ getProgressData.trader_email }}</p>
      </div>
    </div>
    <div style="position:absolute; right:15%; top:5%">
      <h3>Progress currently Harvestable Stage</h3>
      <div class="d-flex justify-content-between align-items-baseline">
        <p>Stage Start: {{ currentStage ? getStageStart() : '' }}</p>
        <p class="ms-3">Current Date: {{ new Date().getFullYear() + '-' + new Date().getMonth() + '-' + new Date().getDate() }}</p>
        <p class="ms-3">Stage End: {{ currentStage ? getStageEnd() : '' }}</p>
      </div>
    </div>
    <div style="width:100%; height:2vh;" v-if="getDates" class="mb-4">     
      <div class="d-flex mx-auto" :style="{'width': '100%'}" style="height:100%; position:relative; z-index: 1; background:grey">        
        <div v-for="(stage, i) in getStages()" :key="i" :style="[i == 0 ? {'border-left':'2px solid black'} : {}, i == getStages().length - 1 ? {'border-right':'2px solid black'} : {}, {'width': '100%'}]" style="height:100%;"></div>        
        <div id="progress" v-b-tooltip.hover :title="((currentDate / dateTotal) * 100).toFixed(2) +'%'" style="position:absolute; z-index: 1; height:100%; left:0; height:100%; border-left: 2px solid black;" class="d-flex mx-auto" :style="{'width': (currentDate / dateTotal) * 100 +'%'}"></div>
      </div>           
    </div>  
    <div class="w-100" style="height:50vh; background:grey">
      <b-carousel class="mb-2" id="carousel-1" :interval="4000"
      controls indicators background="#ababab" style="text-shadow: 1px 1px 2px #333; width:100%; height:100%;">
      
        <b-carousel-slide style="height:50vh;">
          <template #img>
            <img class="d-block img-fluid w-100" style="width:100%; height:100%; object-fit:cover" src="https://picsum.photos/1024/480/?image=55" alt="image slot">
          </template>
        </b-carousel-slide>        
      </b-carousel>     
      <div class="d-flex justify-content-between align-items-baseline" style="width:15%;">
        <h4>Asking Price: </h4>
        <h5>{{ getProgressData.contract_estimatedPrice.toFixed(2) }}</h5>
      </div>
      <div class="d-flex justify-content-between align-items-baseline" style="width:15%;">
        <h4>Estimated Harvest:</h4>
        <h5>{{ getProgressData.contract_estimatedHarvest.toFixed(2) }}</h5>
      </div>
      <button type="button" class="btn btn-success" style="position:absolute; bottom:2%; right:5%" @click="proceedToBid()">PROCEED TO BID</button>
    </div>  
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { add, format, eachDayOfInterval, isBefore, isAfter, isEqual } from 'date-fns'
export default {
    name: 'BidOrderProgress',
    created(){
      this.fetchProjectProgress(this.$route.params.id)
      .then(() => {  
        var keys = Object.keys(this.getProgressData);          
        var dateKeys = keys.splice(7,8)             
        var dates = []  
        for(var i = 0; i < dateKeys.length; i = i + 2){
          if(this.getProgressData[dateKeys[i]] && this.getProgressData[dateKeys[i+1]]){                           
            dates.push(dateKeys[i])
            dates.push(dateKeys[i+1])
          }            
        }   
        if(dates.length > 0){          
          var start = this.getProgressData[dates[0]]
          var end = this.getProgressData[dates[dates.length - 1]]
          
          this.dateTotal += eachDayOfInterval({
            start: new Date(start),
            end: new Date(end)
          }).length

          this.currentDate += eachDayOfInterval({
            start: new Date(start),
            end: new Date()
          }).length          
        }        
                                  
        this.readyApp()
      })
      .catch((err) => {
        console.log(err)
        this.$router.push({ name:'Catalog' })
      })
      
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProjectProgress']),
        getStages(){                    
          var stages = []
          var keys = Object.keys(this.getProgressData);          
          var dateKeys = keys.splice(7,8)   
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
              if(dateKeys[i] == 'project_floweringStart'){
                stages.push(dateKeys[i])                                      
              }
              else if(dateKeys[i] == 'project_fruitBuddingStart'){
                stages.push(dateKeys[i])                                                    
              }
              else if(dateKeys[i] == 'project_devFruitStart'){
                stages.push(dateKeys[i])                                                   
              }
              else if(dateKeys[i] == 'project_harvestableStart'){
                stages.push(dateKeys[i])                                                    
              }
              if(!check){  
                console.log(checkStartDate)              
                if((checkStartDate && checkEndDate) || (isStartEqual || isEndEqual)){
                  console.log(dateKeys[i])
                  this.currentStage =  dateKeys[i]   
                  check = true
                }                                                                
              }                        
            }          
          }
          return stages         
      },
      getStageEnd(){
          var keys = Object.keys(this.getProgressData);              
          var dateKeys = keys.splice(7,8)
          for(var i = 0; i < dateKeys.length; i = i + 2){
            if(dateKeys[i] == this.currentStage){
              return this.getProgressData[dateKeys[i+1]]
            }
          }
      },                       
      getStageStart(){
          var keys = Object.keys(this.getProgressData);              
          var dateKeys = keys.splice(7,8)
          for(var i = 0; i < dateKeys.length; i = i + 2){
            if(dateKeys[i] == this.currentStage){
              return this.getProgressData[dateKeys[i]]
            }
          }
      },
      proceedToBid(){
        this.$router.push({ path: `/bid_order/project/${this.$route.params.id}` })
      }                       
    },
    computed: {
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
      getDates(){
        if(
          !this.getProgressData.project_floweringStart &&
          !this.getProgressData.project_floweringEnd &&
          !this.getProgressData.project_fruitBuddingStart &&
          !this.getProgressData.project_fruitBuddingEnd &&
          !this.getProgressData.project_devFruitStart &&
          !this.getProgressData.project_devFruitEnd &&
          !this.getProgressData.project_harvestableStart &&
          !this.getProgressData.project_harvestableEnd
        ){
          return false
        }
        else{    
          var keys = Object.keys(this.getProgressData);              
          var dateKeys = keys.splice(7,8)
          for(var i = 0; i < dateKeys.length; i = i + 2){                
              if(this.getProgressData[dateKeys[i]] != null){                  
                  var year = this.getProgressData[dateKeys[i]].split('-')[0]
                  var month = this.getProgressData[dateKeys[i]].split('-')[1]
                  var day = this.getProgressData[dateKeys[i]].split('-')[2]
                  var year1 = this.getProgressData[dateKeys[i+1]].split('-')[0]
                  var month1 = this.getProgressData[dateKeys[i+1]].split('-')[1]
                  var day1 = this.getProgressData[dateKeys[i+1]].split('-')[2]
                  var checkStartDate = isBefore(new Date(year, month-1, day, 8,0,0,0), new Date().setHours(8,0,0,0))
                  var isStartEqual = isEqual(new Date(year, month-1, day, 8,0,0,0), new Date().setHours(8,0,0,0))
                  var checkEndDate = isAfter(new Date(year1, month1-1, day1, 8,0,0,0), new Date().setHours(8,0,0,0))
                  var isEndEqual = isEqual(new Date(year1, month1-1, day1, 8,0,0,0), new Date().setHours(8,0,0,0))
                  if( (checkStartDate || isStartEqual) && (checkEndDate || isEndEqual) ){
                    return true                       
                  }                                      
              }                                
          }                          
          return false                                    
        }
      },               
    },
    data(){
      return {
        dateTotal: -1,
        currentDate: -1,
        currentStage: null
      }
    }
}
</script>

<style scoped>
#progress:hover {
  transition: 0.5s;
  background: green;
}
#progress {
  background:lightgreen;
}
</style>
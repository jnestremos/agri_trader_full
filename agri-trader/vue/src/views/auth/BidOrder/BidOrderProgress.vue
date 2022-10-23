<template>
  <div class="bidOrderProgress p-3 h-100 w-100" style="position:relative;">
    <h2>{{ getProgressData.prod_name + ' ' + getProgressData.prod_type }}</h2>
    <!-- <p>{{ getProgressData.prod_class ? getProgressData.prod_class : '' }}</p>     -->
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
      <h3 v-if="currentStage">Progress currently {{ currentStage }} Stage</h3>
      <h3 v-else>Produce Hasn't Entered Flowering Stage Yet</h3>
      <!-- <div class="d-flex justify-content-between align-items-baseline" v-if="currentStage">
        <p>Stage Start: {{ currentStage ? getStageStart() : '' }}</p>
        <p class="ms-3">Current Date: {{ new Date().getFullYear() + '-' + parseInt(new Date().getMonth() + 1) + '-' + new Date().getDate() }}</p>
        <p class="ms-3">Stage End: {{ currentStage ? getStageEnd() : '' }}</p>
      </div> -->
    </div>
    <div style="width:100%; height:2vh;" class="mb-4">     
      <div class="d-flex mx-auto" :style="{'width': '100%'}" style="height:100%; position:relative; z-index: 1; background:grey">        
        <div v-for="(stage, i) in stages" :key="i" :style="[i == 0 ? {'border-left':'2px solid black'} : {}, i == stages.length - 1 ? {'border-right':'2px solid black'} : {}, {'width': '100%'}]" style="height:100%;"></div>        
        <div id="progress" v-b-tooltip.hover :title="[((currentDate / dateTotal) * 100).toFixed(2) <= 0 ? '0.00' : ((currentDate / dateTotal) * 100).toFixed(2)] + '%'" style="position:absolute; z-index: 1; height:100%; left:0; border-left: 2px solid black;" class="d-flex mx-auto" :style="[currentDate != -1 ? {'width': (currentDate / dateTotal) * 100 +'%'} : {'width': '0%'}]"></div>
      </div>           
    </div>  
    <div class="w-100" style="height:50vh; background:grey">
      <b-carousel class="mb-2" id="carousel-1" :interval="4000"
      controls indicators background="#ababab" style="text-shadow: 1px 1px 2px #333; width:100%; height:100%;">
      
        <b-carousel-slide v-for="(image, index) in getProgressData.project_images" :key="index" style="height:50vh;">
          <template #img>
            <img class="d-block img-fluid w-100" style="width:100%; height:100%; object-fit:cover" :src="getProgressData.project_images && getProgressData.project_images.length > 0 && image && image.project_image_path ? require(`../../../../../public/storage/project/progress_images/${image.project_image_path}`) : ''" alt="image slot">
          </template>
        </b-carousel-slide>        
      </b-carousel>     
      <div class="d-flex justify-content-between align-items-baseline" style="width:15%;">
        <h4>Trader's Initial Price: </h4>
        <h5 v-if="getProgressData.contract_estimatedPrice">{{ getProgressData.contract_estimatedPrice.toFixed(2) }}</h5>
      </div>
      <div class="d-flex justify-content-between align-items-baseline" style="width:15%;">
        <h4>Estimated Harvest:</h4>
        <h5 v-if="getProgressData.contract_estimatedHarvest">{{ getProgressData.contract_estimatedHarvest.toFixed(2) }}</h5>
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
        var dateKeys = keys.splice(8,8)                     
        var dates = []  
        for(var ii = 0; ii < dateKeys.length; ii = ii + 2){
          if(this.getProgressData[dateKeys[ii]] && this.getProgressData[dateKeys[ii+1]]){                           
            dates.push(dateKeys[ii])
            dates.push(dateKeys[ii+1])
          }            
        }   
        if(dates.length > 0){          
          var start = this.getProgressData[dates[0]]
          var end = this.getProgressData[dates[dates.length - 1]]
          
          this.dateTotal += eachDayOfInterval({
            start: new Date(start),
            end: new Date(end)
          }).length

          if(!isBefore(new Date().setHours(8,0,0,0), new Date(start).setHours(8,0,0,0))){           
            this.currentDate += eachDayOfInterval({
              start: new Date(start),
              end: new Date()
            }).length              
          }
        }
        var stages = []        
        var check = false                   
        for(var i = 0; i < dateKeys.length; i++){            
          if(this.getProgressData[dateKeys[i]] && i < dateKeys.length - 2){
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
            stages.push(dateKeys[i]) 
            var string = null;                              
            if((checkStartDate && checkEndDate) || (isStartEqual || isEndEqual)){                  
              // this.currentStage =  dateKeys[i] 
              if(isStartEqual){
                string = dateKeys[i]
              }
              else if(isEndEqual){
                string = dateKeys[i+1]
              }
              if(!string){
                string = dateKeys[i]
              }
              if(string == 'project_floweringStart' || string == 'project_floweringEnd'){
                this.currentStage =  'Flowering'    
                break                                  
              }
              else if(string == 'project_fruitBuddingStart' || string == 'project_fruitBuddingEnd'){
                this.currentStage =  'Fruit Budding'   
                break                                                
              }
              else if(string == 'project_devFruitStart' || string == 'project_devFruitEnd'){
                this.currentStage =  'Developing Fruit'    
                break                                          
              }
              else if(string == 'project_harvestableStart' || string == 'project_harvestableEnd'){
                this.currentStage =  'Harvestable'     
                break                                             
              }                    
                check = true
            }
            else{
              if(!check){
                this.currentStage = null
              } 
              // if(dateKeys[i-2] == 'project_floweringStart'){
              //   this.currentStage =  'Flowering'   
              //   break        
              // }
              // else if(dateKeys[i-2] == 'project_fruitBuddingStart'){
              //   this.currentStage =  'Fruit Budding'   
              //   break        
              // }
              // else if(dateKeys[i-2] == 'project_devFruitStart'){
              //   this.currentStage =  'Developing Fruit'  
              //   break         
              // }
              // else if(dateKeys[i-2] == 'project_harvestableStart'){
              //   this.currentStage =  'Harvestable'     
              //   break      
              // }
            }                                                                                                    
          }          
        }
        this.stages = stages                                                  
        this.readyApp()
      })
      .catch((err) => {
        console.error(err)
        this.$router.push({ name:'Catalog' })
      })
      
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProjectProgress']),
        getStageEnd(){
            if(this.currentStage == 'Flowering'){
              return this.getProgressData.project_floweringEnd
            }
            else if(this.currentStage == 'Fruit Budding'){
              return this.getProgressData.project_fruitBuddingEnd
            }
            else if(this.currentStage == 'Developing Fruit'){
              return this.getProgressData.project_devFruitEnd
            }
            else{
              return this.getProgressData.project_harvestableEnd
            }
        },                       
        getStageStart(){
            if(this.currentStage == 'Flowering'){
              return this.getProgressData.project_floweringStart
            }
            else if(this.currentStage == 'Fruit Budding'){
              return this.getProgressData.project_fruitBuddingStart
            }
            else if(this.currentStage == 'Developing Fruit'){
              return this.getProgressData.project_devFruitStart
            }
            else{
              return this.getProgressData.project_harvestableStart
            }
        },
        proceedToBid(){
          this.$router.push({ path: `/bid_order/project/${this.$route.params.id}` })
        }                       
    },
    computed: {
      ...mapGetters(['getProgressData']),
      getProgressDate(){
        if(!this.getProgressData.project_harvestableEnd){
          var harvestDate = add(new Date(this.getProgressData.project_commenceDate), {
              weeks: 1
          })
          var formattedDate = format(harvestDate, 'yyyy-MM-dd');       
          return formattedDate                    
        }
        else{
          return format(new Date(this.getProgressData.project_harvestableEnd), 'MMM. dd, yyyy')
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
          var dateKeys = keys.splice(8,8)          
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
        currentStage: null,
        stages: null
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
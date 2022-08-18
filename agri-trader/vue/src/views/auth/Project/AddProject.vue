<template>
  <div class="addProject">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Add Project</h3>        
    </div>        
    <form action="" @submit.prevent="sendProject()" class="container-fluid d-flex flex-wrap p-0" style="height:90%;">     
      <div class="row px-5 w-100 m-0" style="height:30%;">
        <div class="col-6 d-flex flex-column justify-content-evenly">
          <div class="d-flex align-items-baseline">
            <label for="farm_id" class="form-label me-4">Select Farm: </label>
            <select name="farm_id" class="form-select" id="" style="width:200px;" @change="setFarm($event)">
              <option :value="farm.id" v-for="(farm, index) in getFarmsForProject" :key="index">{{ farm.farm_name }}</option>
            </select>
          </div>
          <div class="d-flex align-items-baseline">
           <p>Farm Owner: {{ owner_name }}</p>
          </div>
          <div class="d-flex align-items-baseline">
            <p>Estimated Sales: {{ data.contract_estimatedSales }}</p>
          </div>
          <div></div>
          <div></div>
        </div>
        <div class="col-6 d-flex flex-column justify-content-evenly">
          <div class="d-flex align-items-baseline">
            <label for="contract_estimatedHarvest" class="form-label me-4">Estimated Harvest (in kg): </label>
            <input type="number" name="contract_estimatedHarvest" id="" class="form-control" style="width:200px" v-model="data.contract_estimatedHarvest" step=".1">
          </div>
          <div class="d-flex align-items-baseline">
            <label for="contract_estimatedPrice" class="form-label me-4">Estimated Price (per kg): </label>
            <input type="number" name="contract_estimatedPrice" id="" class="form-control" style="width:200px" v-model="data.contract_estimatedPrice" step=".1">                    
          </div>
          <div class="d-flex align-items-baseline">
            <label for="contractShare_type" class="form-label me-4">Profit Share: </label>
            <select name="contractShare_type" id="" class="form-select" style="width:200px" @change="resetShareAmount($event)">
              <option value="Percentage" selected>Percentage</option>
              <option value="Amount">Amount</option>
            </select>
            <input type="number" name="contractShare_amount" id="" class="form-control" style="width:200px" v-model="data.contractShare_amount">    
            <p>(owner)</p>                           
          </div>
          <div class="d-flex align-items-baseline" style="">
            <p>Estimated Sales Sharing: {{ data.contract_traderShare }} (trader) - {{ data.contract_ownerShare }} (owner)</p>
          </div>
        </div>
      </div>
      <div class="row px-5 w-100 m-0" style="height:70%;">
        <div class="col-9 d-flex flex-column justify-content-evenly">
          <div class="row w-100 m-0">
            <div class="col-4 p-0 d-flex align-items-baseline">
              <label for="produce_trader_id" class="form-label me-4">Select Produce:</label>
              <select name="produce_trader_id" id="" class="form-select" style="width:250px" @change="setProduce($event)">
                <option :value="produce.produce_trader_id" v-for="(produce, index) in getProducesForProject" :key="index">{{ produce.prod_name }}</option>
              </select>
            </div>
            <div class="col-4 p-0 d-flex align-items-baseline">
              <label for="project_commenceDate" class="form-label me-4">Project Start:</label>
              <input type="date" onkeydown="return false" name="project_commenceDate" id="" v-model="data.project_commenceDate" class="form-control" style="width:200px">
            </div>            
          </div>
          <div class="row w-100 m-0">
            <div class="col-4 p-0">
              Stages: 
            </div>
            <div class="col-4 p-0">
              From
            </div>
            <div class="col-4 p-0">
              To
            </div>
          </div>
          <div class="row w-100 m-0">
            <div class="col-4 d-flex align-items-baseline">
               <input type="checkbox" name="stage1" id="" class="form-check-input" v-model="stage1">
               <label for="stage1" class="form-label ms-2">Flowering:</label>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_floweringStart" @change="fillDates($event)" v-model="data.project_floweringStart" id="project_floweringStart" class="form-control" style="width:200px" disabled>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_floweringEnd" v-model="data.project_floweringEnd" id="project_floweringEnd" class="form-control" style="width:200px" disabled>
            </div>
          </div>
          <div class="row w-100 m-0">
            <div class="col-4 d-flex align-items-baseline">
               <input type="checkbox" name="stage2" id="" class="form-check-input" v-model="stage2">
               <label for="stage2" class="form-label ms-2">Fruit Budding:</label>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_fruitBuddingStart" @change="fillDates($event)" v-model="data.project_fruitBuddingStart" id="project_fruitBuddingStart" class="form-control" style="width:200px" disabled>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_fruitBuddingEnd" v-model="data.project_fruitBuddingEnd" id="project_fruitBuddingEnd" class="form-control" style="width:200px" disabled>
            </div>
          </div>
          <div class="row w-100 m-0">
            <div class="col-4 d-flex align-items-baseline">
               <input type="checkbox" name="stage3" id="" class="form-check-input" v-model="stage3">
               <label for="stage3" class="form-label ms-2">Developing Fruit:</label>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_devFruitStart" @change="fillDates($event)" v-model="data.project_devFruitStart" id="project_devFruitStart" class="form-control" style="width:200px" disabled>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_devFruitEnd" v-model="data.project_devFruitEnd" id="project_devFruitEnd" class="form-control" style="width:200px" disabled>
            </div>
          </div>
          <div class="row w-100 m-0">
            <div class="col-4 d-flex align-items-baseline">
               <input type="checkbox" name="stage4" id="" class="form-check-input" v-model="stage4">
               <label for="stage4" class="form-label ms-2">Harvestable:</label>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_harvestableStart" @change="fillDates($event)" v-model="data.project_harvestableStart" id="project_harvestableStart" class="form-control" style="width:200px" disabled>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_harvestableEnd" v-model="data.project_harvestableEnd" id="project_harvestableEnd" class="form-control" style="width:200px" disabled>
            </div>
          </div>          
        </div>        
      </div>
      <input type="submit" value="Add Project" class="btn btn-primary" style="position: absolute; bottom:5%; right:5%;">               
    </form>
  </div>
</template>

<script>
import { add, format, sub } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "AddProject",
    created(){       
      this.fetchAllFarmsForProject()
      .then(() => {
        this.data.farm_id = this.getFarmsForProject[0].id
        this.fetchAllProducesForProject(this.data.farm_id)
        .then(() => {
          this.data.produce_trader_id = this.getProducesForProject[0].produce_trader_id
          this.owner_name = this.getOwnersForProject[0].owner_firstName + ' ' + this.getOwnersForProject[0].owner_lastName
          this.maxDays = this.getTimeOfHarvest[0].prod_timeOfHarvest.split('-')[1].split(' ')[0]          
          this.readyApp()
        })
      })      
    },
    watch: {
      'data.contract_estimatedPrice'(newVal){
        this.data.contractShare_amount = '0.00'
        this.data.contract_ownerShare = '0.00'
        this.data.contract_traderShare = '0.00'
        this.data.contractShare_amount = '0.00'
        if(parseFloat(newVal) <= 0 || newVal.trim() == ''){
          this.data.contract_estimatedPrice = '0.00'
          this.data.contract_estimatedSales = '0.00'
        }
        this.data.contract_estimatedSales = (parseFloat(newVal) * parseFloat(this.data.contract_estimatedHarvest)).toFixed(2)
      },
      'data.contract_estimatedHarvest'(newVal){
        this.data.contractShare_amount = '0.00'
        this.data.contract_ownerShare = '0.00'
        this.data.contract_traderShare = '0.00'
        if(parseFloat(newVal) <= 0 || newVal.trim() == ''){
          this.data.contract_estimatedHarvest = '0.00'
          this.data.contract_estimatedSales = '0.00'
        }
        this.data.contract_estimatedSales = (parseFloat(newVal) * parseFloat(this.data.contract_estimatedPrice)).toFixed(2)
      },
      'data.contractShare_amount'(newVal){
        if(parseFloat(newVal) <= 0 || newVal.trim() == '' || (this.data.contractShare_type == 'Percentage' && parseFloat(newVal) > 100)){
          this.data.contractShare_amount = '0.00'
          this.data.contract_ownerShare = '0.00'
          this.data.contract_traderShare = '0.00'
        }
        else{
          if(this.data.contractShare_type == 'Percentage'){
            this.data.contract_traderShare = (parseFloat(this.data.contract_estimatedSales) - (parseFloat(this.data.contract_estimatedSales) * parseFloat(this.data.contractShare_amount / 100))).toFixed(2)
            this.data.contract_ownerShare = (parseFloat(this.data.contract_estimatedSales) * parseFloat(this.data.contractShare_amount / 100)).toFixed(2)        
          }
          else{
            this.data.contract_traderShare = (parseFloat(this.data.contract_estimatedSales) - parseFloat(newVal)).toFixed(2)
            this.data.contract_ownerShare = parseFloat(newVal).toFixed(2)
          }          
        }      
      },    
      'stage1'(newVal){
        var project_floweringStart = document.getElementById('project_floweringStart')
        var project_fruitBuddingStart = document.getElementById('project_fruitBuddingStart')
        var project_devFruitStart = document.getElementById('project_devFruitStart')
        var project_harvestableStart = document.getElementById('project_harvestableStart')        
        var checkboxes = document.querySelectorAll('input[type="checkbox"]')        
        if(newVal){
          project_floweringStart.disabled = false
          project_fruitBuddingStart.disabled = true
          project_devFruitStart.disabled = true
          project_harvestableStart.disabled = true
          this.stage2 = true
          checkboxes[1].disabled = true
          this.stage3 = true
          checkboxes[2].disabled = true
          this.stage4 = true          
          checkboxes[3].disabled = true
        }
        else{
          this.data.project_floweringStart = null
          this.data.project_floweringEnd = null
          project_floweringStart.disabled = true          
          this.stage2 = false
          checkboxes[1].disabled = false
          this.stage3 = false
          checkboxes[2].disabled = false
          this.stage4 = false          
          checkboxes[3].disabled = false 
        }        
      },
      'stage2'(newVal){        
        var project_fruitBuddingStart = document.getElementById('project_fruitBuddingStart')
        var project_devFruitStart = document.getElementById('project_devFruitStart')
        var project_harvestableStart = document.getElementById('project_harvestableStart')        
        var checkboxes = document.querySelectorAll('input[type="checkbox"]')        
        if(newVal){                    
          this.stage3 = true
          checkboxes[2].disabled = true
          this.stage4 = true          
          checkboxes[3].disabled = true  
          project_harvestableStart.disabled = true   
          if(this.stage1){
            project_fruitBuddingStart.disabled = true
          } 
          else{
            project_fruitBuddingStart.disabled = false
            project_devFruitStart.disabled = true
            project_harvestableStart.disabled = true
          }        
        }       
        else{
          this.data.project_fruitBuddingStart = null
          this.data.project_fruitBuddingEnd = null
          project_fruitBuddingStart.disabled = true          
          this.stage3 = false
          checkboxes[2].disabled = false
          this.stage4 = false          
          checkboxes[3].disabled = false           
        }
      },
      'stage3'(newVal){
        var project_devFruitStart = document.getElementById('project_devFruitStart')
        var project_harvestableStart = document.getElementById('project_harvestableStart')        
        var checkboxes = document.querySelectorAll('input[type="checkbox"]')        
        if(newVal){                  
          this.stage4 = true          
          checkboxes[3].disabled = true  
          project_harvestableStart.disabled = true     
          if(this.stage2){
            project_devFruitStart.disabled = true
          } 
          else{
            project_devFruitStart.disabled = false
            project_harvestableStart.disabled = true
          }      
        }       
        else{
          this.data.project_devFruitStart = null
          this.data.project_devFruitEnd = null
          project_devFruitStart.disabled = true          
          this.stage4 = false          
          checkboxes[3].disabled = false                    
        }
      },
      'stage4'(newVal){
        var project_harvestableStart = document.getElementById('project_harvestableStart')
        if(newVal){
          if(this.stage3){
            project_harvestableStart.disabled = true
          }
          else{
            project_harvestableStart.disabled = false
          }                    
        }
        else{                 
          this.data.project_harvestableStart = null
          this.data.project_harvestableEnd = null
          project_harvestableStart.disabled = true
        }
      },
    },
    methods: {
        ...mapActions(['readyApp', 'fetchAllFarmsForProject', 'fetchAllProducesForProject', 'addProject']),
        resetShareAmount(e){
          this.data.contractShare_type = e.target.value
          this.data.contractShare_amount = '0.00'
        },
        setFarm(e){
          this.data.farm_id = e.target.value
          this.fetchAllProducesForProject(this.data.farm_id)   
          .then(() => {
            this.data.contract_estimatedHarvest = '0.00'
            this.data.contract_estimatedPrice = '0.00'
            this.data.contractShare_type = 'Percentage'
            this.data.contractShare_amount = '0.00'
            this.data.contract_ownerShare = '0.00'
            this.data.contract_traderShare = '0.00'
            this.data.project_completionDate = null
            this.data.project_commenceDate = null
            this.stage1 = false
            this.stage2 = false
            this.stage3 = false
            this.stage4 = false
            this.project_floweringStart = null,
            this.project_floweringEnd = null,
            this.project_fruitBuddingStart = null,
            this.project_fruitBuddingEnd = null,
            this.project_devFruitStart = null,
            this.project_devFruitEnd = null,
            this.project_harvestableStart = null,
            this.project_harvestableEnd = null            
            var farmObj = this.getFarmsForProject.filter((farm) => {              
              return parseInt(farm.id) === parseInt(this.data.farm_id)
            })            
            var owner_id = farmObj[0].farm_owner_id
            var ownerObj = this.getOwnersForProject.filter((owner) => {
              return parseInt(owner.id) === parseInt(owner_id)
            })
            this.owner_name = ownerObj[0].owner_firstName + " " + ownerObj[0].owner_lastName
            this.data.produce_trader_id = this.getProducesForProject[0].produce_trader_id
          })     
        },
        setProduce(e){
          this.data.produce_trader_id = e.target.value
          this.data.contract_estimatedHarvest = '0.00'
          this.data.contract_estimatedPrice = '0.00'
          this.data.contractShare_type = 'Percentage'
          this.data.contractShare_amount = '0.00'
          this.data.contract_ownerShare = '0.00'
          this.data.contract_traderShare = '0.00'
          this.data.project_completionDate = null
          this.data.project_commenceDate = null
          this.stage1 = false
          this.stage2 = false
          this.stage3 = false
          this.stage4 = false
          this.project_floweringStart = null
          this.project_floweringEnd = null
          this.project_fruitBuddingStart = null
          this.project_fruitBuddingEnd = null
          this.project_devFruitStart = null
          this.project_devFruitEnd = null
          this.project_harvestableStart = null
          this.project_harvestableEnd = null          
        },
        sendProject(){
          this.data.contract_estimatedSales = parseFloat(this.data.contract_estimatedSales).toFixed(2)
          this.data.contract_estimatedPrice = parseFloat(this.data.contract_estimatedPrice).toFixed(2)
          this.data.contract_estimatedHarvest = parseFloat(this.data.contract_estimatedHarvest).toFixed(2)
          this.data.contract_ownerShare = parseFloat(this.data.contract_ownerShare).toFixed(2)
          this.data.contract_traderShare = parseFloat(this.data.contract_traderShare).toFixed(2)
          this.addProject(this.data)
          .then(() => {
            this.$router.push({ name: 'AllProjects' })
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
        },
        fillDates(e){      
          var stages = [            
            'project_floweringEnd', 
            'project_fruitBuddingStart', 
            'project_fruitBuddingEnd', 
            'project_devFruitStart',
            'project_devFruitEnd',
            'project_harvestableStart',
            'project_harvestableEnd'
          ]            
          var interval = null   
          interval = Math.round((parseInt(this.maxDays) - 20) / 7);          
                    
          var year = parseInt(e.target.value.split('-')[0])
          var month = parseInt(e.target.value.split('-')[1])
          var day = parseInt(e.target.value.split('-')[2])
          var currDay = new Date(year, month-1, day, 0, 0, 0, 0) 
          console.log(currDay)
          var formattedDate = null; 
          var prevDay = null;
          if(e.target.id != 'project_floweringStart'){            
            console.log(interval)
            for(var i = stages.indexOf(e.target.id); i >= 0; i--){
              if(i == stages.indexOf(e.target.id)){
                console.log(currDay)
                formattedDate = format(new Date(currDay), 'yyyy-MM-dd') 
                this.data[stages[i]] = formattedDate                           
              }
              else{
                if(prevDay){
                  prevDay = sub(prevDay, {
                    days: interval
                  }) 
                }
                else{
                  prevDay = sub(currDay, {
                    days: interval
                  })                  
                }
                formattedDate = format(new Date(prevDay), 'yyyy-MM-dd') 
                this.data[stages[i]] = formattedDate                
              }                         
            }
            prevDay = sub(prevDay, {
              days: interval
            })
            formattedDate = format(new Date(prevDay), 'yyyy-MM-dd') 
            this.data.project_floweringStart = formattedDate                          
            for(var ii = stages.indexOf(e.target.id) + 1; ii < stages.length; ii++){
              currDay = add(currDay, {
                days: interval
              })
              formattedDate = format(new Date(currDay), 'yyyy-MM-dd')
              this.data[stages[ii]] = formattedDate                                      
            }
          } 
          else{
            console.log(interval)                                                        
            stages.forEach((stage) => {             
              currDay = add(currDay, {
                days: interval
              })
              formattedDate = format(new Date(currDay), 'yyyy-MM-dd')
              this.data[stage] = formattedDate
            })
          }      
        }
    },
    computed: {
      ...mapGetters(['getFarmsForProject', 'getProducesForProject', 'getOwnersForProject', 'getTimeOfHarvest'])
    },
    data(){
      return{
        data: {
          farm_id: null,
          produce_trader_id: null,
          project_status_id: 1,
          contract_estimatedHarvest: '0.00',
          contract_estimatedPrice: '0.00',
          contractShare_amount: '0.00',          
          project_commenceDate: null,
          contractShare_type: 'Percentage',
          contract_estimatedSales: '0.00',
          contract_ownerShare: '0.00',
          contract_traderShare: '0.00',
          project_floweringStart: null,
          project_floweringEnd: null,
          project_fruitBuddingStart: null,
          project_fruitBuddingEnd: null,
          project_devFruitStart: null,
          project_devFruitEnd: null,
          project_harvestableStart: null,
          project_harvestableEnd: null
        },
        owner_name: null,
        stage1: false,
        stage2: false,
        stage3: false,
        stage4: false,
        errors: null,
        maxDays: null
      }
    }
}
</script>

<style>

</style>
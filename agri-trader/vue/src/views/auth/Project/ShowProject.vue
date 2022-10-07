<template>
  <div class="showProject">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>About Project</h3>        
    </div>           
    <form action="" @submit.prevent="sendProject()" class="container-fluid d-flex flex-wrap p-0" style="height:90%;">         
      <div class="row px-4 w-100 m-0" style="height:30%;">
        <div class="col-6 p-0 d-flex flex-column justify-content-evenly">
          <div class="d-flex align-items-baseline">
            <p>Project #: {{ $route.params.id }}</p>          
          </div>                           
          <div class="d-flex align-items-baseline">
            <p v-if="getFarm">Farm Name: {{ getFarm.farm_name }}</p>            
          </div>
          <div class="d-flex align-items-baseline">           
           <p v-if="getOwner">Farm Owner: {{ getOwner.owner_firstName + " " + getOwner.owner_lastName}}</p>
          </div>
          <div class="d-flex align-items-baseline">            
            <p v-if="getContract">Estimated Sales: {{ getContract.contract_estimatedSales.toFixed(2) }} </p>
          </div>          
          <div></div>
          <div v-if="(getStockOutForProject && getStockOutForProject.length > 0) && (getExpenditureForProject && getExpenditureForProject.length > 0)" class="d-flex align-items-baseline">            
            <p v-if="getContract">Total Expenses: {{ getTotalExpense }} </p>
          </div>
          <div></div>
        </div>
        <div class="col-6 p-0 d-flex flex-column justify-content-evenly">
          <div class="d-flex align-items-baseline">
            <p v-if="getContract">Estimated Harvest (in kg): {{ getContract.contract_estimatedHarvest.toFixed(2) }} </p>
          </div>
          <div class="d-flex align-items-baseline">            
            <p v-if="getContract">Estimated Price (per kg): {{ getContract.contract_estimatedPrice.toFixed(2) }} </p>
          </div>
          <div class="d-flex align-items-baseline" v-if="getShare">
            <p v-if="getShare.contractShare_type == 'Percentage'">Profit Share: {{ getShare.contractShare_amount + "% (Owner)"}} </p>
            <p v-else>Profit Share: {{ getShare.contractShare_amount + " (Owner)"}} </p>                                                 
          </div>
          <div class="d-flex align-items-baseline" style="">            
            <p v-if="getContract">Estimated Sales Sharing: {{ getContract.contract_traderShare.toFixed(2) }} (trader) - {{ getContract.contract_ownerShare.toFixed(2) }} (owner)</p>
          </div>
        </div>
      </div>
      <div class="row px-4 w-100 m-0" style="height:70%;">
        <div class="col-9 p-0 d-flex flex-column justify-content-evenly">
          <div class="row w-100 m-0">
            <div class="col-4 p-0 d-flex align-items-baseline">
              <p v-if="getProduce">Produce: {{ getProduce.prod_name + ' ' + getProduce.prod_type }}</p>
            </div>
            <div class="col-4 p-0 d-flex align-items-baseline">
              <p v-if="getProject">Project Start: {{ getProject.project_commenceDate }}</p>
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
            <div class="col-4 p-0 d-flex align-items-baseline">                          
               <label for="stage1" class="form-label">Flowering:</label>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_floweringStart" v-model="data.project_floweringStart" id="project_floweringStart" class="form-control" style="width:200px" disabled>              
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_floweringEnd" v-model="data.project_floweringEnd" id="project_floweringEnd" class="form-control" style="width:200px" disabled>
            </div>            
          </div>
          <div class="row w-100 m-0">
            <div class="col-4 p-0 d-flex align-items-baseline">                        
               <label for="stage2" class="form-label">Fruit Budding:</label>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_fruitBuddingStart" v-model="data.project_fruitBuddingStart" id="project_fruitBuddingStart" class="form-control" style="width:200px" disabled>              
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_fruitBuddingEnd" v-model="data.project_fruitBuddingEnd" id="project_fruitBuddingEnd" class="form-control" style="width:200px" disabled>              
            </div>
          </div>
          <div class="row w-100 m-0">
            <div class="col-4 p-0 d-flex align-items-baseline">               
               <label for="stage3" class="form-label">Developing Fruit:</label>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_devFruitStart" v-model="data.project_devFruitStart" id="project_devFruitStart" class="form-control" style="width:200px" disabled>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_devFruitEnd" v-model="data.project_devFruitEnd" id="project_devFruitEnd" class="form-control" style="width:200px" disabled>              
            </div>
          </div>
          <div class="row w-100 m-0">
            <div class="col-4 p-0 d-flex align-items-baseline">               
               <label for="stage4" class="form-label">Harvestable:</label>
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_harvestableStart" v-model="data.project_harvestableStart" id="project_harvestableStart" class="form-control" style="width:200px" disabled>              
            </div>
            <div class="col-4 p-0">
              <input type="date" onkeydown="return false" name="project_harvestableEnd" v-model="data.project_harvestableEnd" id="project_harvestableEnd" class="form-control" style="width:200px" disabled>              
            </div>
          </div> 
          <div class="row w-100 m-0">
            <div class="col-4 p-0">
              
            </div>            
          </div>         
        </div>  
        <div class="col-3 d-flex flex-column justify-content-evenly">
          <div class="row w-100 m-0">
            <div v-if="getProject && getProject.project_status_id == 2" class="col-12 p-0 d-flex align-items-baseline">
              <label for="project_status_id" class="form-label me-4">Project Status:</label>
              <select name="project_status_id" class="form-select" style="width:250px" id="" @change="setStatus($event)" v-if="getProject">               
                  <!-- <option v-if="getProject.project_status_id == 1" value="2" selected>Approved</option>
                  <option v-if="getProject.project_status_id == 1" value="3">Cancelled</option>                                 -->
                  <option v-if="getProject.project_status_id == 2" value="4" selected>Terminated Successfully</option>
                  <option v-if="getProject.project_status_id == 2" value="5">Terminated w/ Complications</option>                                
              </select>
            </div>
          </div>
          <div class="row w-100 m-0" v-if="getHistory">
            <div class="col-12 p-0">
              <table>
                <thead>
                  <tr>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(history, index) in getHistory" :key="index">
                    <td v-if="history.project_status_id == 1" :style="[history.project_status_id == getProject.project_status_id ? {color: 'blue'} : {color : 'black'}]">For Approval</td>
                    <td v-if="history.project_status_id == 2" :style="[history.project_status_id == getProject.project_status_id ? {color: 'blue'} : {color : 'black'}]">Approved</td>
                    <td v-if="history.project_status_id == 3" :style="[history.project_status_id == getProject.project_status_id ? {color: 'blue'} : {color : 'black'}]">Cancelled</td>
                    <td v-if="history.project_status_id == 4" :style="[history.project_status_id == getProject.project_status_id ? {color: 'blue'} : {color : 'black'}]">Terminated Successfully</td>
                    <td v-if="history.project_status_id == 5" :style="[history.project_status_id == getProject.project_status_id ? {color: 'blue'} : {color : 'black'}]">Terminated w/ Complications</td>
                    <td>{{ getDate(history) }}</td>
                  </tr>                  
                </tbody>
              </table>               
            </div>             
          </div>
          <div class="row w-100 m-0"></div> 
          <div class="row w-100 m-0"></div> 
          <div class="row w-100 m-0"></div> 
          <div class="row w-100 m-0"></div> 
          <div class="row w-100 m-0"></div> 
          <div v-if="getProject">
            <div class="d-flex justify-content-between align-items-center" style="position:absolute; bottom:3%; right:7%; width:40vw;" v-if="getProject.project_status_id == 2">
              <input type="button" value="Add Expenditures" class="btn btn-primary" @click="$router.push({ path: `/project/expenditures/${$route.params.id}` })">                        
              <input type="button" value="Add Supplies to Project" class="btn btn-primary" @click="$router.push({ path: `/stockOut/${$route.params.id}` })">
              <input type="button" value="Upload Image" class="btn btn-primary">                        
              <input type="button" value="Harvest" class="btn btn-primary" @click="$router.push({ path: `/harvest/${$route.params.id}` })">                        
              <input type="button" :value="getProfitSharingForProject ? 'View Profit Sharing' : 'Update Project'" @click="seeProfitSharing()" class="btn btn-primary">                                                                  
            </div>
            <!-- <div class="d-flex justify-content-end align-items-center" style="position:absolute; bottom:7%; right:25%; width:30vw;" v-else>                                                  
              <input type="submit" value="Update Project" class="btn btn-primary">                                      
            </div>  -->
          </div>    
        </div>                
      </div>
    </form>        
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "ShowProject",
    created(){
        this.fetchProject(this.$route.params.id)
        .then(() => {          
          var dates = document.querySelectorAll('input[type="date"]')
          dates.forEach((d) => {
            if(d.name == 'project_floweringStart'){
              this.data.project_floweringStart = this.getProject.project_floweringStart
            }
            else if(d.name == 'project_floweringEnd'){
              this.data.project_floweringEnd = this.getProject.project_floweringEnd
            }
            else if(d.name == 'project_fruitBuddingStart'){
              this.data.project_fruitBuddingStart = this.getProject.project_fruitBuddingStart
            }
            else if(d.name == 'project_fruitBuddingEnd'){
              this.data.project_fruitBuddingEnd = this.getProject.project_fruitBuddingEnd
            }
            else if(d.name == 'project_devFruitStart'){
              this.data.project_devFruitStart = this.getProject.project_devFruitStart
            }
            else if(d.name == 'project_devFruitEnd'){
              this.data.project_devFruitEnd = this.getProject.project_devFruitEnd
            }
            else if(d.name == 'project_harvestableStart'){
              this.data.project_harvestableStart = this.getProject.project_harvestableStart
            }
            else if(d.name == 'project_harvestableEnd'){
              this.data.project_harvestableEnd = this.getProject.project_harvestableEnd
            }
          })  
          if(this.getProject.project_status_id == 1){
            this.data.project_status_id = 2
          } 
          else{
            this.data.project_status_id = 4
          }              
          this.readyApp()    
        })      
    },
    watch: {
      edit(newVal){        
        var project_floweringStart = document.getElementById('project_floweringStart')
        var project_floweringEnd = document.getElementById('project_floweringEnd') 
        var project_fruitBuddingStart = document.getElementById('project_fruitBuddingStart')
        var project_fruitBuddingEnd = document.getElementById('project_fruitBuddingEnd')                     
        var project_devFruitStart = document.getElementById('project_devFruitStart')
        var project_devFruitEnd = document.getElementById('project_devFruitEnd')           
        var project_harvestableStart = document.getElementById('project_harvestableStart')
        var project_harvestableEnd = document.getElementById('project_harvestableEnd')              
        if(newVal){            
          if(this.data.project_floweringStart && this.data.project_floweringEnd){              
            project_floweringStart.disabled = false
            project_floweringEnd.disabled = false
          }
          if(this.data.project_fruitBuddingStart && this.data.project_fruitBuddingEnd){              
            project_fruitBuddingStart.disabled = false
            project_fruitBuddingEnd.disabled = false
          }
          if(this.data.project_devFruitStart && this.data.project_devFruitEnd){              
            project_devFruitStart.disabled = false
            project_devFruitEnd.disabled = false
          }
          if(this.data.project_harvestableStart && this.data.project_harvestableEnd){              
            project_harvestableStart.disabled = false
            project_harvestableEnd.disabled = false
          }                        
        }
        else{
          project_floweringStart.disabled = true
          project_floweringEnd.disabled = true   
          project_fruitBuddingStart.disabled = true
          project_fruitBuddingEnd.disabled = true                 
          project_devFruitStart.disabled = true
          project_devFruitEnd.disabled = true    
          project_harvestableStart.disabled = true
          project_harvestableEnd.disabled = true                         
        }
      },
      'data.project_floweringStart'(newVal, oldVal){        
        if(newVal == null){
          this.data.project_floweringStart = oldVal          
        }
      },
      'data.project_floweringEnd'(newVal, oldVal){        
        if(newVal == null){
          this.data.project_floweringEnd = oldVal          
        }
      },
      'data.project_fruitBuddingStart'(newVal, oldVal){        
        if(newVal == null){
          this.data.project_fruitBuddingStart = oldVal          
        }
      },
      'data.project_fruitBuddingEnd'(newVal, oldVal){        
        if(newVal == null){
          this.data.project_fruitBuddingEnd = oldVal          
        }
      },
      'data.project_devFruitStart'(newVal, oldVal){        
        if(newVal == null){
          this.data.project_devFruitStart = oldVal          
        }
      },
      'data.project_devFruitEnd'(newVal, oldVal){        
        if(newVal == null){
          this.data.project_devFruitEnd = oldVal          
        }
      },
      'data.project_harvestableStart'(newVal, oldVal){               
        if(newVal == null){
          this.data.project_harvestableStart = oldVal          
        }
      },
      'data.project_harvestableEnd'(newVal, oldVal){        
        if(newVal == null){
          this.data.project_harvestableEnd = oldVal          
        }
      },
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProject', 'updateProject', 'initStatusForProfit']),
        setStatus(e){
          console.log(e.target.value)
          this.data.project_status_id = parseInt(e.target.value)
        },
        getDate(history){
          var dateTime = history.created_at.split(' ')
          var date = dateTime[0]
          var time = dateTime[1].split(':')
          var hr = time[0]
          var min = time[1]
          var sec = time[2]
          var meridiem = null
          if(parseInt(hr) >= 12){
            if(parseInt(hr) != 12){
              hr = parseInt(hr) - 12
            }    
            meridiem = 'PM'
          }
          else{           
            meridiem = 'AM'
            if(parseInt(hr) == 0){
              hr = 1
            }
          }
          return date + ' ' + hr + ':' + min + ':' + sec + ' ' + meridiem
        },
        sendProject(){
          console.log(this.$route.params.id)
          var data = {
            data: this.data,
            id: this.$route.params.id
          }         
          this.updateProject(data)
          .then(() => {
            this.$router.push({ 'name' : 'AllProjects' })
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
              this.errors = err.response.data.error                          
              this.$toastr.e(this.errors.toString())
            }           
          })          
        }, 
        seeProfitSharing(){
          this.initStatusForProfit(this.data.project_status_id)
          this.$router.push({ path: `/project/profit/sharing/${this.$route.params.id}` })
        }        
    },
    computed: {
      ...mapGetters([
        'getProject', 
        'getContract',       
        'getFarm',
        'getProduce',
        'getOwner',
        'getShare',
        'getHistory',
        'getExpenditureForProject',
        'getStockOutForProject',
        'getSuppliesForProject',
        'getProfitSharingForProject'
        ]),
        getTotalExpense(){
          var supplyTotalValue = 0
          var expTotalValue = 0      
          var supplyObj = null
          if(this.getExpenditureForProject && this.getStockOutForProject){
            this.getStockOutForProject.forEach((s) => {
              supplyObj = this.getSuppliesForProject.filter((ss) => {
                return parseInt(s.supply_id) === parseInt(ss.id)
              })
              supplyTotalValue += parseFloat(supplyObj[0].supply_initialPrice) * parseInt(s.supply_qty)
            })
            var expAmounts = [];
            this.getExpenditureForProject.forEach((e) => {
              expAmounts.push(e.exp_amount)
            })              
            expTotalValue = expAmounts.reduce((a, b) => a + b, 0)                      
          }         
          return parseFloat(expTotalValue + supplyTotalValue).toFixed(2)
        },
        
    },
    data(){
      return {
        errors: null,
        edit: false,        
        stage1: false,
        stage2: false,
        stage3: false,
        stage4: false,
        data: {
          project_status_id: null,
          project_floweringStart: null,
          project_floweringEnd: null,
          project_fruitBuddingStart: null,
          project_fruitBuddingEnd: null,
          project_devFruitStart: null,
          project_devFruitEnd: null,
          project_harvestableStart: null,
          project_harvestableEnd: null,
        }        
      }
    }
}
</script>

<style scoped>
table, th, td{
  border: 1px solid black;
  border-collapse: collapse;
  text-align: center;
}
table{
  background:lightgreen;
  table-layout: fixed;
  width:100%
}

</style>
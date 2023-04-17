<template>
  <div class="showBidOrder h-100 w-100">
    <form @submit.prevent="sendBid()" action="" class="h-100 w-100 p-3" style="position:relative;">
      <div class="d-flex align-items-baseline justify-content-between" style="width:70%;">
        <div class="d-flex align-items-baseline justify-content-between" style="width:30%">
          <!-- <h2>{{ getProgressData.prod_name }}</h2> -->
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
      <div v-if="$route.name == 'BidOrderProject'">
        <h3 v-if="getProgressData.produce_trader">{{ getProduceName }}</h3>
        <p>{{ getProgressData.prod_class }}</p>
      </div>
      <div v-else>
        <h3 v-if="getOnHandData.farm_produce">{{ getProduceName }}</h3>
        <!-- <p>{{ getProgressData.prod_class }}</p> -->
      </div>
      <!-- <p>Transaction # 123-345-546</p> -->
      <div class="d-flex align-items-baseline justify-content-between" style="width:80%;">
        <div class="d-flex align-items-baseline" style="width:25%">
          <h5 class="me-2">Trader Name: </h5>
          <p>{{ getProgressData.trader_name ? getProgressData.trader_name : getOnHandData.trader ? getOnHandData.trader.trader_firstName + ' ' + getOnHandData.trader.trader_lastName  : ''}}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:25%">
          <h5 class="me-2">{{ $route.name == 'BidOrderProject' ? 'Expected Date of Harvest:' : 'Last Date of Harvest:'}} </h5>
          <p>{{ getProgressDate }}</p>
        </div>
        <div v-if="$route.name != 'BidOrderProject'" class="d-flex align-items-baseline" style="width:25%">
          <h5 class="me-2"> Available Quantity: </h5>
          <p v-if="getOnHandData.selectedProduce">{{ getOnHandData.selectedProduce.on_hand_qty + ' kg/s' }}</p>
        </div>
        <div v-if="$route.name == 'BidOrderProject'" class="d-flex align-items-baseline" style="width:25%">
          <h5 class="me-2" >Estimated Harvest: </h5>
          <p>{{ getProgressData.contract_estimatedHarvest + " kg"}}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:25%">
          <h5 v-if="(getProgressData.produce_trader && getProgressData.produce_trader.length > 1) || (getOnHandData.produce_yields && getOnHandData.produce_yields.length > 1)" class="me-2">Class: </h5>
          <select @change="setProduceTraderId($event)" v-if="getProgressData.produce_trader && getProgressData.produce_trader.length > 1" name="" id="" class="form-select" style="width:150px;">
            <option v-for="(c, index) in getProgressData.produce_trader" :key="index" :value="c.id">{{ index == 0 ? 'A' : index == 1 ? 'B' : 'C'}}</option>
          </select>
          <select @change="setProduceTraderId($event)" v-else-if="getOnHandData.produce_yields && getOnHandData.produce_yields.length > 1" name="" id="" class="form-select" style="width:150px;">
            <option v-for="(c, index) in getOnHandData.produce_yields" :key="index" :selected="c.produce_trader_id == data.produce_trader_id" :value="c.produce_trader_id">{{ c.produce_yield_class }}</option>
          </select>
        </div>
      </div>
      <div class="d-flex align-items-baseline justify-content-between" style="width:80%;">
        <div class="d-flex align-items-baseline" style="width:25%">
          <h5 class="me-2">Contact Number: </h5>
          <p>{{ getProgressData.trader_contactNum ? getProgressData.trader_contactNum : getOnHandData.trader_contactNum ? getOnHandData.trader_contactNum.trader_contactNum : '' }}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:25%">
          <h5 class="me-2">Email Address: </h5>
          <p>{{ getProgressData.trader_email ? getProgressData.trader_email : getOnHandData.trader ? getOnHandData.trader.trader_email : '' }}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:25%">
          <h5 class="me-2">Farm Originated: </h5>
          <p>{{ getProgressData.farm_name ? getProgressData.farm_name : getOnHandData.selectedProduce ? getOnHandData.selectedProduce.farm_name : '' }}</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:25%">
          <h5 v-if="$route.name == 'BidOrderProject'" class="me-2">Cut-off Date: </h5>
          <p v-if="$route.name == 'BidOrderProject'">{{ cutoffDate }}</p>
        </div>
      </div>
      <div class="d-flex align-items-center justify-content-between w-100 mb-2">
        <div style="height:45vh; width:50%; background:transparent" class="d-flex justify-content-center align-items-center">
            <line-chart style="width:95%;" v-if="chart_data.labels.length > 0 && chart_data.datasets.length > 0" :chartData="chart_data" :label="$route.name == 'BidOrderProject' ? 'Negotiated Price' : 'Market Price'"></line-chart>
            <h5 v-else>No Graphical Data</h5>
        </div>
        <div style="height:45vh; background:grey; width:50%">
          <b-carousel id="carousel-1" :interval="4000"
          controls indicators background="#ababab" style="text-shadow: 1px 1px 2px #333; width:100%; height:100%;">

            <b-carousel-slide v-for="(image, index) in getProgressData.project_images" :key="index" style="height:45vh; width:100%">
              <template #img>
                <img class="d-block img-fluid w-100" style="width:100%; height:100%; object-fit:cover" :src="getProgressData.project_images && getProgressData.project_images.length > 0 && image && image.project_image_path ? require(`../../../../../public/storage/project/progress_images/${image.project_image_path}`) : ''" alt="image slot">
              </template>
            </b-carousel-slide>
          </b-carousel>
        </div>
      </div>
      <div class="d-flex align-items-baseline justify-content-between" style="width:60%;">
        <div class="d-flex align-items-baseline">
          <h5 class="me-2">Distributor's Initial Bid Price:</h5>
          <input v-if="$route.name == 'BidOrderProject'" :disabled="disabled" type="number" class="form-control" style="width:150px;" min="0" step="0.5" :max="parseFloat(getProgressData.contract_estimatedPrice)" v-model="data.order_initialPrice" @change="setTotal()" onkeydown="return false">
          <input v-else-if="getOnHandData.farm_produce && $route.name == 'BidOrderOnHand'" disabled type="number" class="form-control" style="width:150px;" min="0" step="0.5" :max="parseFloat(getMaxPrice).toFixed(2)" v-model="data.order_initialPrice" @change="setTotal()" onkeydown="return false">
        </div>
        <div class="d-flex align-items-baseline">
          <h5 class="form-h5 me-2">Expected Dates Needed: </h5>
          <input type="date" class="form-control me-3" style="width:150px;" :disabled="disabled" onkeydown="return false" v-model="data.order_dateNeededFrom">
          <input type="date" class="form-control" style="width:150px;" :disabled="disabled" onkeydown="return false" v-model="data.order_dateNeededTo">
        </div>
      </div>
      <div class="d-flex align-items-baseline mt-3">
        <h5 class="me-4">Quantity Needed:</h5>
        <div class="d-flex align-items-baseline" v-if="$route.name == 'BidOrderProject'">
          <!-- <input type="number" class="form-control me-4" id="minQty" style="width:100px;" :disabled="disabled" step="1" value="1" min="1" @change="setMinQty($event)" onkeydown="return false"> -->
          <!-- <h5 class="me-4">To</h5> -->
          <input type="number" class="form-control" id="maxQty" style="width:100px;" :disabled="disabled" step="1" value="5" min="1" @change="setMaxQty($event)" onkeydown="return false">
        </div>
        <div v-else class="d-flex align-items-baseline">
          <input type="number" class="form-control me-3" style="width:100px;" step="1" value="1" min="1" @change="setOnHandQty($event)" onkeydown="return false">
          <p>kgs</p>
        </div>
      </div>
      <div class="d-flex align-items-baseline justify-content-between mt-3" style="width:60%">
        <div class="d-flex align-items-baseline">
          <h5 class="me-4" v-if="$route.name == 'BidOrderProject'">Current Stage:</h5>
          <div v-if="$route.name == 'BidOrderProject'">
            <p v-if="currentStage">{{ currentStage }}</p>
            <p v-else>Produce Hasn't Entered Flowering Stage Yet</p>
          </div>
        </div>
        <div class="d-flex align-items-baseline">
          <h5 class="me-4">Total Price:</h5>
          <h5>{{ $route.name == 'BidOrderProject' ? data.project_bid_total : data.on_hand_bid_total }}</h5>
        </div>
      </div>
      <input type="submit" value="Send Order Bid" :disabled="disabled" class="btn btn-success" style="position:absolute; bottom:0; right:5%;">
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { add, format, isEqual, isAfter, isBefore, sub } from 'date-fns'
import LineChart from '../../../components/BidOrderGraph.vue'
export default {
    name: 'ShowBidOrder',
    components: { LineChart },
    created(){
      console.log(this.$route)
      if(this.$route.name == 'BidOrderProject'){
        this.fetchProjectProgress(this.$route.params.id)
        .then(() => {
          var keys = Object.keys(this.getProgressData);
          var dateKeys = keys.splice(8,8)
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
              }
            }
          }
          this.data.order_initialPrice = this.getProgressData.contract_estimatedPrice.toFixed(2)
          this.data.project_bid_total = (this.data.order_initialPrice * this.data.project_bid_maxQty).toFixed(2)
          if(this.getProgressData.produce_trader.length > 1){
            this.data.order_grade = 'A'
          }
          this.data.project_id = this.$route.params.id
          this.data.trader_id = this.getProgressData.trader_id
          if(!this.getProgressData.project_harvestableEnd){
            var harvestDate = add(new Date(this.getProgressData.project_commenceDate), {
                weeks: 1
            })
            var formattedDate = format(harvestDate, 'yyyy-MM-dd');
            this.data.exp_dateHarvest = formattedDate
          }
          else{
            this.data.exp_dateHarvest = this.getProgressData.project_harvestableEnd
          }
          this.data.produce_trader_id = this.getProgressData.produce_trader[0].id
          var chartData = this.getProgressData.chart_data.filter((c) => {
            return (this.data.order_grade) === (c.order_grade)
          })
          chartData.forEach((c) => {
            this.chart_data.labels.push(c.created_at.split(' ')[0])
            this.chart_data.datasets.push(c['avg(order_negotiatedPrice)'])
          })
          var oneMonthBefore = sub(new Date(this.data.exp_dateHarvest).setHours(8,0,0,0), {
            months: 1
          })
          this.cutoffDate = format(oneMonthBefore, 'MMM. dd, yyyy')
          if(isAfter(new Date().setHours(8,0,0,0), oneMonthBefore) || isEqual(new Date().setHours(8,0,0,0), oneMonthBefore)){
            this.disabled = true
          }
          else{
            this.disabled = false
          }
          console.log(this.disabled)
          this.readyApp()
        })
      }
      else if(this.$route.name == 'BidOrderOnHand'){
        console.log(1)
        var data = {
          farm_id: this.$route.params.farm_id,
          produce_trader_id: this.$route.params.produce_trader_id,
        }
        this.fetchOnHandDetails(data)
        .then(() => {
          var prodYieldObj = this.getOnHandData.produce_yields.filter((y) => {
            return parseInt(this.$route.params.produce_trader_id) === parseInt(y.produce_trader_id)
          })
          this.data.project_id = prodYieldObj[0].project_id
          this.data.trader_id = this.getOnHandData.trader.id
          this.data.produce_trader_id = this.$route.params.produce_trader_id
          var farmProdObj = this.getOnHandData.farm_produce.filter((p) => {
            return parseInt(this.$route.params.produce_trader_id) === parseInt(p.produce_trader_id) && parseInt(this.$route.params.farm_id) === parseInt(p.farm_id)
          })
          this.data.order_initialPrice = farmProdObj[0].on_hand_latestPrice
          this.data.order_traderPrice = this.data.order_initialPrice
          this.data.on_hand_bid_total = (this.data.order_initialPrice * this.data.on_hand_bid_qty).toFixed(2)
          if(prodYieldObj[0].produce_yield_class != 'N/A'){
            this.data.order_grade = prodYieldObj[0].produce_yield_class
          }
          else{
            this.data.order_grade = null
          }
          this.getOnHandData.chart_data.forEach((d) => {
            this.chart_data.labels.push(d.produce_yield_dateHarvestTo)
            this.chart_data.datasets.push(d.produce_yield_price)
          })
          this.readyApp()
        })
      }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProjectProgress' ,'sendBidOrderProject', 'sendBidOrderOnHand', 'fetchOnHandDetails']),
        setMinQty(e){
          var maxQty = document.getElementById("maxQty")
          this.data.project_bid_minQty = parseFloat(e.target.value)
          maxQty.value = parseFloat(e.target.value) + 4
          this.data.project_bid_maxQty = parseFloat(maxQty.value)
          this.data.project_bid_total = (this.data.order_initialPrice * this.data.project_bid_minQty).toFixed(2) + ' - ' + (this.data.order_initialPrice * this.data.project_bid_maxQty).toFixed(2)
        },
        setMaxQty(e){
          // var minQty = document.getElementById("minQty")
          // if(parseFloat(e.target.value) <= parseFloat(minQty.value)){
          //   e.target.value = parseFloat(minQty.value) + 4
          // }
          this.data.project_bid_maxQty = parseFloat(e.target.value)
          this.data.project_bid_total = (this.data.order_initialPrice * this.data.project_bid_maxQty).toFixed(2)
        },
        setOnHandQty(e){
          this.data.on_hand_bid_qty = parseFloat(e.target.value)
          this.data.on_hand_bid_total = (this.data.order_initialPrice * this.data.on_hand_bid_qty).toFixed(2)
        },
        sendBid(){
          if(this.$route.name == 'BidOrderProject'){
            this.sendBidOrderProject(this.data)
            .then(() => {
              this.$toastr.s('Bid Order Sent Successfully!')
              setTimeout(() => {
                this.$router.push({ name: 'BidOrderHistory' })
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
          else if(this.$route.name == 'BidOrderOnHand'){
            this.sendBidOrderOnHand(this.data)
            .then(() => {
              this.$toastr.s('Bid Order Sent Successfully!')
              setTimeout(() => {
                this.$router.push({ name: 'BidOrderHistory' })
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
        setTotal(){
          if(this.$route.name == 'BidOrderProject'){
            // var minTotal = parseFloat(parseFloat(this.data.order_initialPrice) * parseFloat(this.data.project_bid_minQty)).toFixed(2)
            var maxTotal = parseFloat(parseFloat(this.data.order_initialPrice) * parseFloat(this.data.project_bid_maxQty)).toFixed(2)
            this.data.project_bid_total = maxTotal
          }
          else{
            this.data.on_hand_bid_total = (this.data.order_initialPrice * this.data.on_hand_bid_qty)
          }

        },
        setProduceTraderId(e){
          this.data.produce_trader_id = e.target.value
          if(this.$route.name == 'BidOrderProject'){
            var prodTraderObj = this.getProgressData.produce_trader.filter((p) => {
              return parseInt(e.target.value) === parseInt(p.id)
            })
            if(parseInt(prodTraderObj[0].produce_numOfGrades) > 1){
              var container = this.getProgressData.produce_trader.filter((p) => {
                return parseInt(prodTraderObj[0].produce_id) === parseInt(p.produce_id)
              })
              container.forEach((c, i) => {
                if(parseInt(c.id) === parseInt(e.target.value)){
                  if(i == 0){
                    this.data.order_grade = 'A'
                  }
                  else if(i == 1){
                    this.data.order_grade = 'B'
                  }
                  else{
                    this.data.order_grade = 'C'
                  }
                }
              })
            }
          }
          else if(this.$route.name == 'BidOrderOnHand'){
            this.$router.push({ path: `/bid_order/on_hand/${this.$route.params.farm_id}/${this.data.produce_trader_id}` })
            location.reload()
            // var prodYieldObj = this.getOnHandData.produce_yields.filter((y) => {
            //   return parseInt(this.data.produce_trader_id) === parseInt(y.produce_trader_id)
            // })
            // if(prodYieldObj[0].produce_yield_class != 'N/A'){
            //   this.data.order_grade = prodYieldObj[0].produce_yield_class
            // }
            // else{
            //   this.data.order_grade = null
            // }
            // var farmProdObj = this.getOnHandData.farm_produce.filter((p) => {
            //   return parseInt(this.data.produce_trader_id) === parseInt(p.produce_trader_id)
            // })
            // console.log(farmProdObj)
            // this.data.order_initialPrice = farmProdObj[0].on_hand_latestPrice
            // this.data.on_hand_bid_total = (this.data.order_initialPrice * this.data.on_hand_bid_qty).toFixed(2)
            // this.data.order_traderPrice = this.data.order_initialPrice
          }
        }
    },
    computed:{
      ...mapGetters(['getProgressData', 'getOnHandData']),
      getProgressDate(){
        var formattedDate = null
        // var prodYieldObj = null
        if(this.$route.name == 'BidOrderProject'){
          if(!this.getProgressData.project_harvestableEnd){
            var harvestDate = add(new Date(this.getProgressData.project_commenceDate), {
                weeks: 1
            })
            formattedDate = format(harvestDate, 'yyyy-MM-dd');
            return formattedDate
          }
          else{
            return format(new Date(this.getProgressData.project_harvestableEnd), 'MMM. dd, yyyy')
          }
        }
        else if(this.$route.name == 'BidOrderOnHand'){
          if(this.getOnHandData.farm_produce && this.getOnHandData.farm_produce.length > 0){
            var farmProduceObj = this.getOnHandData.farm_produce.filter((p) => {
              return parseInt(p.produce_trader_id) === parseInt(this.data.produce_trader_id) && parseInt(p.farm_id) === parseInt(this.$route.params.farm_id)
            })
            if(farmProduceObj.length > 0){
              return format(new Date(farmProduceObj[0].prod_lastDateOfHarvest), 'MMM. dd, yyyy')
            }
          }
        }
        return null
      },
      getProduceName(){
        if(this.$route.name == 'BidOrderProject'){
          var prodTraderObj = this.getProgressData.produce_trader.filter((p) => {
            return parseInt(p.id) === parseInt(this.data.produce_trader_id)
          })

          if(prodTraderObj.length > 0){
            var arr = prodTraderObj[0].prod_name.split('(Class')
            if(arr.indexOf('(Class') != -1){
              arr.splice(arr.indexOf('(Class'), 0, this.getProgressData.prod_type)
              return arr.join(' ')
            }
            else{
              return prodTraderObj[0].prod_name + ' ' + this.getProgressData.prod_type
            }
          }
          return null
        }
        else {
          console.log(this.getOnHandData.farm_produce)
          var farmProduceObj = this.getOnHandData.farm_produce.filter((p) => {
            return parseInt(this.data.produce_trader_id) === parseInt(p.produce_trader_id) && parseInt(this.$route.params.farm_id) === parseInt(p.farm_id)
          })
          console.log(this.data.produce_trader_id)

          if(farmProduceObj.length > 0){
            var arrr = farmProduceObj[0].prod_name.split('(Class')
            if(arrr.indexOf('(Class') != -1){
              arrr.splice(arrr.indexOf('(Class'), 0, this.getOnHandData.produce.prod_type)
              return arrr.join(' ')
            }
            else{
              return farmProduceObj[0].prod_name + ' ' + this.getOnHandData.produce.prod_type
            }
          }
          return null
        }

      },
      getMaxPrice(){
        console.log(this.getOnHandData.farm_produce)
        var farmProduceObj = this.getOnHandData.farm_produce.filter((p) => {
          return parseInt(this.data.produce_trader_id) === parseInt(p.produce_trader_id) && parseInt(this.$route.params.farm_id) === parseInt(p.farm_id)
        })
        if(farmProduceObj.length > 0){
          return farmProduceObj[0].on_hand_latestPrice
        }
        return null
      }
    },
    data(){
      return {
        currentStage: null,
        errors: null,
        chart_data: {
          labels: [],
          datasets: [],
        },
        cutoffDate: null,
        disabled: false,
        data: {
          exp_dateHarvest: null,
          order_grade: null,
          project_id: null,
          trader_id: null,
          produce_trader_id: null,
          order_dateNeededTo: null,
          order_dateNeededFrom: null,
          bid_order_status_id: 1,
          order_initialPrice: null,
          project_bid_minQty: 1,
          project_bid_maxQty: 5,
          project_bid_total: null,
          on_hand_bid_qty: 1,
          on_hand_bid_total: null,
          order_traderPrice: null
        },
      }
    }
}
</script>

<style>

</style>

<template>
  <div class="showBidOrder h-100 w-100">
    <form action="" class="h-100 w-100 p-3" style="position:relative;">
      <div class="d-flex align-items-baseline justify-content-between" style="width:70%;">
        <div class="d-flex align-items-baseline justify-content-between" style="width:30%">
          <h2>Strawberry</h2>
          <div class="d-flex align-items-baseline">
            <label for="" class="form-label me-4">Class</label>
            <select name="" class="form-select" id="" style="width:100px">
              <option value="">A</option>
              <option value="">B</option>
              <option value="">C</option>
            </select>
          </div>          
        </div>
        <a href="">Upcoming Projects</a>
      </div>
      <p>Class A</p>
      <p>Transaction # 123-345-546</p>
      <div class="d-flex align-items-baseline justify-content-between" style="width:60%;">
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Trader Name: </h5>
          <p>Joshua N. Estremos</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Expected Date of Harvest: </h5>
          <p>01/15/22</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Estimated Harvest: </h5>
          <p>100 kgs</p>
        </div>
      </div>
      <div class="d-flex align-items-baseline justify-content-between" style="width:60%;">
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Contact Number: </h5>
          <p>09452041874</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Email Address: </h5>
          <p>estremosjoshua@gmail.com</p>
        </div>
        <div class="d-flex align-items-baseline" style="width:33.33%">
          <h5 class="me-2">Farm Originated: </h5>
          <p>Bemwa Farm</p>
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
          <input type="number" class="form-control" style="width:150px;" value="100.00">
        </div>
        <div class="d-flex align-items-baseline">
          <h5 class="form-h5 me-2">Expected Dates Needed: </h5>
          <input type="date" class="form-control me-3" style="width:150px;">
          <input type="date" class="form-control" style="width:150px;">
        </div>          
      </div>
      <div class="d-flex align-items-baseline mt-3">
        <h5 class="me-4">Quantity Needed:</h5>
        <div class="d-flex align-items-baseline">
          <input type="number" class="form-control me-4" style="width:100px;" value="0">
          <h5 class="me-4">To</h5>
          <input type="number" class="form-control" style="width:100px;" value="10">
        </div>
      </div>
      <div class="d-flex align-items-baseline justify-content-between mt-3" style="width:60%">
        <div class="d-flex align-items-baseline">
          <h5 class="me-4">Current Stage:</h5>          
          <p>Fruit Budding</p>
        </div>
        <div class="d-flex align-items-baseline">
          <h5 class="me-4">Total Price:</h5>          
          <h5>Php 10,000.00</h5>
        </div>
      </div>
      <input type="submit" value="Send Order Bid" class="btn btn-success" style="position:absolute; bottom:0; right:5%;">
    </form>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: 'ShowBidOrder',
    created(){
        this.fetchProjectProgress(this.$route.params.id)
        .then(() => {
          this.readyApp()
        })
        
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProjectProgress'])
    },
    data(){
      return {
        data: {
          order_dateNeededTo: null,
          order_dateNeededFrom: null,
          bid_status_id: 1,
          order_initialPrice: null,
          project_bid_minQty: null,
          project_bid_maxQty: null,
          project_bid_total: null
        }
      }
    }    
}
</script>

<style>

</style>
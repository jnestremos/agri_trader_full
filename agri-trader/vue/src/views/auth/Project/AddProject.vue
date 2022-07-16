<template>
  <div class="addProject">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Add Project</h3>        
    </div>
    <form action="" class="container-fluid d-flex flex-wrap p-0" style="height: 90vh; ">     
      <div class="row px-5 w-100 m-0" style="height:30%; background:yellow;">
        <div class="col-6 d-flex flex-column justify-content-evenly" style="background:aqua">
          <div class="d-flex align-items-baseline">
            <label for="farm_id" class="form-label me-4">Select Farm: </label>
            <select name="farm_id" class="form-select" id="" style="width:200px;">
              <option value="1">Bimbim Farm</option>
            </select>
          </div>
          <div class="d-flex align-items-baseline">
           <p>Farm Owner: Vianca Tingson</p>
          </div>
          <div class="d-flex align-items-baseline">
            <p>Estimated Sales: 150000.00</p>
          </div>
          <div></div>
          <div></div>
        </div>
        <div class="col-6 d-flex flex-column justify-content-evenly" style="background:green">
          <div class="d-flex align-items-baseline">
            <label for="contract_estimatedHarvest" class="form-label me-4">Estimated Harvest (in kg): </label>
            <input type="number" name="contract_estimatedHarvest" id="" class="form-control" style="width:200px">
          </div>
          <div class="d-flex align-items-baseline">
            <label for="contract_estimatedPrice" class="form-label me-4">Estimated Price (per kg): </label>
            <input type="number" name="contract_estimatedPrice" id="" class="form-control" style="width:200px">                    
          </div>
          <div class="d-flex align-items-baseline">
            <label for="contract_ownerShare" class="form-label me-4">Profit Share: </label>
            <select name="contractShare_type" id="" class="form-select" style="width:200px">
              <option value="Percentage" selected>Percentage</option>
              <option value="Amount">Amount</option>
            </select>
            <input type="number" name="contract_ownerShare" id="" class="form-control" style="width:200px">    
            <p>(owner)</p>                           
          </div>
          <div class="d-flex align-items-baseline" style="">
            <p>Estimated Sales Sharing: 135000.00 (trader) - 15000.00 (owner)</p>
          </div>
        </div>
      </div>
      <div class="row w-100 m-0" style="height:70%; background:grey;">

      </div>
    </form>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: "AddProject",
    created(){
        this.readyApp()
    },
    methods: {
        ...mapActions(['readyApp'])
    }
}
</script>

<style>

</style>
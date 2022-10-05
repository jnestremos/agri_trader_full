<template>
  <div class="SupplyList">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply List</h3>        
    </div>
    <div class="container-fluid" style="height:85%; position: relative; z-index: 9;">
        <div class="form-row mb-3 mt-2">
            <div class="col-lg-2 me-3">
                <label class="form-label me-4 fw-bold">Supplier</label>
                <select class="form-select">
                    <option value="None">Select Supplier</option>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                </select>
            </div>
            <div class="col-lg-2 me-3">
                <label class="form-label me-4 fw-bold">Supply Type</label>
                <select class="form-select">
                    <option selected value="None">Select Supply Type</option>
                    <option value="Fertilizer">Fertilizer</option>                                
                    <option value="Insecticide">Insecticide</option>                                
                    <option value="Fungicide">Fungicide</option>                                
                    <option value="Herbicide">Herbicide</option>
                </select>
            </div>
            <div class="col-lg-2 me-2">
                <label class="form-label me-4 fw-bold">Choose Supply For</label>
                <select class="form-select">
                    <option selected value="None">Select Supply For</option>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                </select>
            </div>
        </div>
        <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
            <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                <thead align="center">
                    <tr>
                        <th scope="col">Supplier</th>
                        <th scope="col">Supply Name</th>
                        <th scope="col">Supply Type</th>
                        <th scope="col">Supply For</th>
                        <th scope="col">Description</th>
                        <th scope="col">Price</th>
                        <th scope="col">Unit</th>
                    </tr>
                </thead>
                <tbody align="center">
                                                                                            
                </tbody>
            </table>
        </div>
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group me-3">
                <router-link to="/supply/add"> <button class="btn btn-success" style="width:200px; height: 50px;"> Add Supply Profile </button> </router-link>
            </div> 
        </div>     
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
export default {
    name: "SupplyList",
    created() {
        this.readyApp()
    },
    methods: {
        ...mapActions(['readyApp'])
    }
}
</script>

<style>

</style>
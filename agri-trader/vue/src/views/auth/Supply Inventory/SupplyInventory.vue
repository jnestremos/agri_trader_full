<template>
  <div class="SupplyInventory">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Supply Inventory</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
            <div class="form-row mt-3">
                <div class="col-lg-3 me-3">
                    <label for="stockInHitsory_supplierList" class="form-label me-4">Choose Supplier</label>
                    <select class="form-select" id="supplier_name">
                        <option selected value="None">Select Supplier</option>
                        <option value="Pacifica Agrivet">Pacifica Agrivet</option>                                
                        <option value="Agway">Agway</option>                                
                        <option value="Inson Agro Farm">Inson Agro Farm</option>  
                    </select>                    
                </div>
                <div class="col-lg-3 me-3">
                    <label for="stockInHistory_supplyType" class="form-label me-4" >Choose Supply Type</label>
                    <select class="form-select" id="supply_type">
                        <option selected value="None">Select Supply Type</option>
                        <option value="Fertilizer">Fertilizer</option>                                
                        <option value="Insecticide">Insecticide</option>                                
                        <option value="Fungicide">Fungicide</option>                                
                        <option value="Herbicide">Herbicide</option>
                    </select>
                </div>
                <div class="col-lg-3 me-3">
                    <label for="stockInHistory_SupplyFor" class="form-label me-4">Choose Supply For</label>
                    <select class="form-select" id="supply_type">
                        <option selected value="None">Select Supply Type</option> 
                        <option value="Mango">Mango</option>                                
                        <option value="Banana">Banana</option>                                
                        <option value="Pineapple">Pineapple</option>                                
                        <option value="Grapes">Grapes</option>                    
                    </select>
                </div>
            </div>
            <div class="mb-2 mt-4" style="width:100% height:90%; clear:left;">
                <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:100%;">
                    <thead align="center">
                        <tr>
                            <th scope="col">Supplier Name</th>
                            <th scope="col">Supply Name</th>
                            <th scope="col">Supply Type</th>
                            <th scope="col">Supply For</th>                                    
                            <th scope="col">Reorder Level</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Unit</th>
                            <th scope="col">Date Updated</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <tr>
                            <td>Pacifica Agrivet Supply</td>
                            <td>Yara Mila Unik-16</td>
                            <td>Fertilizer</td>
                            <td>Mango</td>
                            <td>5</td>
                            <td>15</td>
                            <td>Sack</td>
                            <td>10/05/2022</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="btn-toolbar pt-4" role="toolbar">
                <div class="btn-group me-3">
                <router-link to="/supplyInventory/stockInHistory"> <b-button variant="info" style="width:200px; height:60px">Stock In History</b-button> </router-link>
            </div>
        </div>
        </div>
    </div>
  </div>
  
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: "SupplyInventory",
    created() {
        this.readyApp()
    },
    data(){
        return{
            stockOut: {
                quantity: '',
                remarks: '',
            }
        }
    },
    
    methods: {
        ...mapActions(['readyApp'])
    }
  }
</script>

<style>

</style>
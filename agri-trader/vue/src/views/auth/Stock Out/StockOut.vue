<template>
    <div class="Stock Out">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
              <h3>Supply Inventory | Stock Out</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
          <div style="width:80%; height:65%" class="pb-5">
              <form class="d-flex flex-column justify-content-between mt-2 ms-2" style="height:20%">
                  <div class="form-row mb-2">
                      <div class="col-lg-3 me-3">
                          <label for="stockOut_date" class="form-label me-4" >Date</label>
                          <input type="date" name="stockIn_Date" id="" class="form-control" placeholder="09/05/2022">
                      </div>
                      <div class="col-lg-3 me-3">
                          <label for="stockIn_purchaseOrderNum" class="form-label me-4" >Project ID:</label>
                          <input type="text" name="stockIn_Date" id="" class="form-control" placeholder="022654" disabled>
                      </div>
                  </div>
                  <div class="form-row mt-3">
                        <div class="col-lg-3 me-3">
                            <label for="stockInHitsory_supplierList" class="form-label me-4">Choose Supplier</label>
                            <select class="form-select" id="supplier_name">
                                <option selected value="None">Select Supplier</option>
                                <option value="Pacifica Agrivet">Fertilizer</option>                                
                                <option value="Agway">Insecticide</option>                                
                                <option value="Inson Agro Farm">Fungicide</option>  
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
                      <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:90%;">
                          <thead align="center">
                              <tr>
                                  <th scope="col">Select</th>
                                  <th scope="col">Supply Name</th>
                                  <th scope="col">Supply Type</th>
                                  <th scope="col">Supply For</th>                                    
                                  <th scope="col">Available Quantity</th>
                                  <th scope="col">Unit</th>
                              </tr>
                          </thead>
                          <tbody align="center">
                              <tr>
                                  <td><input type="checkbox"></td>
                                  <td>Yara Mila Unik-16</td>
                                  <td>Fertilizer</td>
                                  <td>Mango</td>
                                  <td>15</td>
                                  <td>Sack</td>
                              </tr>
                              <tr>
                                  <td><input type="checkbox"></td>
                                  <td>Yara Mila Unik-16</td>
                                  <td>Fertilizer</td>
                                  <td>Banana</td>
                                  <td>15</td>
                                  <td>Sack</td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
                  <div class="form-row mt-2 mb-2">
                      <div class="col-sm-2">
                          <label for="orderSummary_quantityReceived" class="form-label me-4" >Amount</label>
                          <input type="text" name="orderSummary_totalAmount" id="" class="form-control" style="width: 40%" v-model="stockOut_quantity">
                      </div>
                  </div>
                  <div class="form-row">
                      <div class="col-lg-3 me-3">
                          <label for="stockOut_transactedBy" class="form-label me-4" >Transacted By</label>
                          <input type="text" name="stockIn_transactedBy" id="" class="form-control" disabled>
                      </div>
                      <div class="col-lg-3 me-3">
                          <label for="stockOut_Remarks" class="form-label me-4" >Remarks</label>
                          <input type="text" name="stockIn_transactedBy" id="" class="form-control" v-model="stockOut_remarks">
                      </div>
                  </div>
                  <div class="btn-toolbar pt-4" role="toolbar">
                      <div class="btn-group me-3">
                        <b-button variant="success" class="fw-bold" style="width:200px; height:60px">Stock Out To Project</b-button>
                      </div>
                  </div>
              </form>
          </div>
          <div style="width:60%; height:65%" class="pb-5">
            <div class="mb-2 mt-5" style="width:100% height:90%; clear:left;">
                <h4 align="center">Supplies used for Project No.</h4>
                <table id="supplySelect" class="table table-striped table-success table-bordered align-middle" style="width:100%;">
                    <thead align="center">
                        <tr>
                            <th scope="col">Supply Name</th>
                            <th scope="col">Supply Type</th>
                            <th scope="col">Supply For</th>                                    
                            <th scope="col">Quantity</th>
                            <th scope="col">Unit</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <tr>
                            <td>Yara Mila Unik-16</td>
                            <td>Fertilizer</td>
                            <td>Banana</td>
                            <td>8</td>
                            <td>Sack</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
  </template>
  
  <script>
  import { mapActions } from 'vuex'
  export default {
      name: "StockOut",
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
<template>
    <div class="ForReturns">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
              <h3>Supply | Returns</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
          <div style="width:85%; height:65%" class="pb-5">
              <form class="d-flex flex-column justify-content-between mt-2 ms-2" style="height:20%">
                  <div class="form-row mb-2">
                      <div class="col-lg-3 me-3">
                          <label for="stockIn_date" class="form-label me-4" >Date</label>
                          <input type="text" name="stockIn_Date" id="" class="form-control" placeholder="09/05/2022">
                      </div>
                      <div class="col-lg-3 me-3">
                          <label for="stockIn_purchaseOrderNum" class="form-label me-4" >Purchase Order No.:</label>
                          <input type="text" name="stockIn_purchaseOrderNum" id="" disabled class="form-control"  placeholder="PO-0000002">
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-3">
                          <p class="font-weight-bold" style="font-size:110%">Supplier Name: Pacifica Agrivet</p>
                      </div>
                      <div class="col-sm-3">
                          <p class="font-weight-bold" style="font-size:110%">Contact Person: Juan dela Cruz</p>
                      </div>
                      <div class="col-sm-3">
                          <p class="font-weight-bold" style="font-size:110%">Contact Number: 09123456789</p>
                      </div>
                  </div>
                  <div class="mb-2" style="width:100% height:90%; clear:left;">
                      <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:100%;">
                          <thead align="center">
                              <tr>
                                  <th scope="col">Supply Name</th>
                                  <th scope="col">Supply Type</th>
                                  <th scope="col">Supply For</th>                                    
                                  <th scope="col">Quantity for Return / Refund</th>
                                  <th scope="col">Unit</th>
                                  <th scope="col">Subtotal</th>
                                  <!-- <th scope="col">Received</th> -->
                              </tr>
                          </thead>
                          <tbody align="center">
                              <tr>
                                  <td>Yara Mila Unik-16</td>
                                  <td>Fertilizer</td>
                                  <td>Mango</td>
                                  <td>5</td>
                                  <td>Sack</td>
                                  <td>2,500.00</td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
                  <div class="form-row mt-2 mb-2">
                     <div class="col-lg-3 me-3">
                        <label>Total Refundable Amount</label>
                        <input type="number" name="stockIn_transactedBy" id="" class="form-control" disabled placeholder="2,500.00">
                     </div>
                    </div>
                  <div class="form-row">
                      <div class="col-lg-3 me-3">
                          <label for="stockIn_transactedBy" class="form-label me-4" >Transacted By</label>
                          <input type="text" name="stockIn_transactedBy" id="" class="form-control" disabled>
                      </div>
                      <div class="col-lg-3 me-3">
                          <label for="stockIn_Remarks" class="form-label me-4" >Remarks</label>
                          <input type="text" name="stockIn_transactedBy" id="" class="form-control" v-model="forReturns_remarks">
                      </div>
                  </div>
                  <div class="btn-toolbar pt-4" role="toolbar">
                      <div class="btn-group me-3">
                          <b-button variant="danger" style="width:200px; height:60px">Proceed to Refund / Return</b-button> <!--disable this button if there is no returns-->
                      </div>
                  </div>
              </form>
          </div>
      </div>
    </div>
  </template>
  
  <script>
  import { mapActions } from 'vuex'
  export default {
      name: "ForReturns",
      created() {
          this.readyApp()
      },
      data(){
          return{
              forReturns: {
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
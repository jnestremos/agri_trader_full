<template>
    <div class="SupplyPurchaseOrderReports">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Supply Purchase Order Report</h3>
        <div class="d-flex">
            <router-link to="/reports/dashboard"><button class="btn btn-info text-right">Return to Reports Dashboard</button></router-link>
        </div>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Purchase Order Number</label>
                  <select class="form-select">
                      <option value="None">Select PO #</option>
                      <option>1</option>
                      <option>2</option>
                      <option>3</option>
                  </select>
              </div>
          </div>
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">From</label>
                <input type="date" class="form-control">
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">To</label>
                <input type="date" class="form-control">
            </div>
          </div>
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Purchase Order No.</th>
                          <th scope="col">Supplier</th>
                          <th scope="col">Items</th>
                          <th scope="col">Items Ordered</th>
                          <th scope="col">Total Amount</th>
                          <th scope="col">Status</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                                                                                              
                  </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { mapActions } from 'vuex';
  export default {
      name: "SupplyPurchaseOrderReports",
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
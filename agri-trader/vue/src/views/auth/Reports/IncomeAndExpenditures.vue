<template>
   <div class="IncomeAndExpenditures">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
              <h3>Overall Income and Expenditures</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
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
                          <th scope="col">Total Income</th>
                          <th scope="col">Total Expense</th>
                          <th scope="col">Net Profit</th>
                          <th scope="col">Date</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                                                                                              
                  </tbody>
              </table>
            </div>
            <div class="form-row mb-2 mt-2">
                <div class="col-lg-2 me-3">
                    <label class="form-label me-4 fw-bold">Total Income</label>
                    <input type="number" class="form-control" disabled>
                </div>
                <div class="col-lg-2 me-3">
                     <label class="form-label me-4 fw-bold">Total Expenditures</label>
                     <input type="number" class="form-control" disabled>
                </div>
                <div class="col-lg-2 me-3">
                     <label class="form-label me-4 fw-bold">Net Profit</label>
                     <input type="number" class="form-control" disabled>
                </div>
             </div>
        </div>
      </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';
  export default {
      name: "IncomeAndExpenditures",
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
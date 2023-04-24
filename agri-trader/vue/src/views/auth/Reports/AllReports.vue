<template>
  <div class="AllReports">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Report Dashboard</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div class="container-fluid mt-5" style="width:100%; height: 30vh;">
              <table id="Report Selection" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <!-- <router-link to="/income/summary"><th scope="col">Income Summary</th></router-link> -->
                          <router-link to="/reports/ProduceReport"><th scope="col">Produce</th></router-link>
                          <router-link to="/reports/ProfitSharing"><th scope="col">Profit Sharing</th></router-link>
                          <router-link to="/reports/ReceivingReport"><th scope="col">Receiving Supply Report </th></router-link>
                          <router-link to="/reports/supplyExpenditures"><th scope="col">Supply Expenditures</th></router-link>
                          <router-link to="/reports/spo"><th scope="col">Supply Purchase Order Reports</th></router-link>
                          <router-link to="/reports/salesReport"><th scope="col">Sales Report</th></router-link>
                          <router-link to="/reports/OtherExpenditures"><th scope="col">Other Expenditures Report</th></router-link>
                          <router-link to="/reports/print"><th scope="col">Overall Report</th></router-link>
                      </tr>
                  </thead>
            </table>
         </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
  export default {
      name: "AllReports",
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

<template>
  <div class="OtherExpendituresReport">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Other Expenditures Report</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
      <div style="width:85%; height:65%" class="pb-5">
        <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">Project</label>
                <select class="form-select">
                    <option value="None">Select Project</option>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                </select>
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">Expense Type</label>
                <select class="form-select">
                    <option selected value="None">Select Expense Type</option>
                    <option>Utilities</option>                                
                    <option>Food</option>                                
                    <option>Transportation</option>                                
                    <option>Communication</option>
                    <option>Maintenance</option>
                </select>
            </div>
        </div>
        <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
            <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                <thead align="center">
                    <tr>
                        <th scope="col">Project No.</th>
                        <th scope="col">Project Name</th>
                        <th scope="col">Type of Expense</th>
                        <th scope="col">Payment Date</th>
                        <th scope="col">Amount</th>
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
    name: "OtherExpendituresReport",
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
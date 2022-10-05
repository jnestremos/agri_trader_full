<template>
  <div class="OtherExpenditures">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Other Expenditures Form</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
      <div style="width:80%; height:65%" class="pb-5">
        <form class="d-flex flex-column justify-content-between mt-2 ms-2 h-100">
          <div class="form-row mt-3">
            <div class="col-lg-2 me-3">
              <label class="form-label me-4" >Project ID:</label>
              <input type="text" name="OtherExp_ID" id="" class="form-control" placeholder="1" disabled>
            </div>
            <div class="col-lg-2 me-3">
              <label class="form-label me-4" >OR/Acc Num</label>
              <input type="text" name="OtherExp_AccNum" id="" class="form-control" placeholder="022654" disabled>
            </div>
          </div>
          <div class="form-row mt-2">
            <div class="col-lg-4 me-1">
              <label class="form-label me-4">Type of Expense</label>
              <select class="form-select" @change="setExpenseType($event)">
                <option selected value="None">Select Expense</option>
                <option selected value="Utilities">Utilities</option>
                <option selected value="Food">Food</option>
                <option selected value="Transportation">Transportation</option>
                <option selected value="Communications">Communications</option>
                <option selected value="Maintenance">Maintenance</option>
              </select>
            </div>
          </div>
          <div class="form-row mt-2">
            <div class="col-lg-4 me-1">
              <label class="form-label me-4">Period from:</label>
              <input type="date" class="form-control" v-model="otherExpenditures.periodFrom">
            </div>
            <div class="col-lg-4 me-1">
              <label class="form-label me-4">To:</label>
              <input type="date" class="form-control" v-model="otherExpenditures.periodTo">
            </div>
          </div>
          <div class="form-row mt-2">
            <div class="col-lg-3">
              <label class="form-label me-4">Payment Type</label>
              <select class="form-select" @change="setPayment($event)">
                <option selected value="None">Select Payment</option>
                <option selected value="Cash">Cash</option>
                <option selected value="Bank">Bank</option>
              </select>
            </div>
            <div class="col-lg-3">
              <label class="form-label me-4">Account Number</label>
              <input type="text" class="form-control" v-model="otherExpenditures.accNum">
            </div>
            <div class="col-lg-4">
              <label class="form-label me-4">Account Name</label>
              <input type="text" class="form-control" v-model="otherExpenditures.accName">
            </div>
          </div>
          <div class="form-row mt-2">
            <div class="col-md-5">
              <label class="form-label me-4">Remarks</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="btn-toolbar pt-2">
            <div class="btn-group me-3 mt-3">
                <router-link to=""><button class="btn btn-secondary" style="width:150px; height: 40px;">Add to Table</button></router-link>
            </div>
            <div class="btn-group me-3 mt-3">
                <button class="btn btn-success"  style="width:150px; height: 40px;">Edit</button>
            </div>
        </div>
        </form>
      </div>
      <div style="width:65%; height:65%" class="pb-5">
        <h4 align="center" class="mt-3">List of Other Expenditures for Project No.</h4>
        <table id="OtherExpendituresList" class="table table-striped table-success table-bordered align-middle" style="width:100%;">
              <thead align="center">
                  <tr>
                      <th scope="col">Date</th>
                      <th scope="col">Type of Expenses</th>
                      <th scope="col">Payment Type</th>
                      <th scope="col">Amount</th>
                      <th scope="col">Remarks</th>                                    
                  </tr>
              </thead>
              <tbody align="center">
                                      
              </tbody>
          </table>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
export default {
    name: "OtherExpenditures",
    created() {
        this.readyApp()
    },
    data(){
      return{
        otherExpenditures: {
          periodFrom: '',
          periodTo: '',
          accNum: '',
          accName: '',
        }
      }
    },
    methods: {
        ...mapActions(['readyApp']),
        setExpenseType(e){

          this.filter.type=e.target.value
        }
    }
}
</script>

<style>

</style>
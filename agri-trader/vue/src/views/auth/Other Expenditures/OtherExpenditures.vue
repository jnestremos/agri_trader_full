<template>
  <div class="OtherExpenditures">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Other Expenditures Form</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
      <div style="width:80%; height:65%" class="pb-5">
        <form @submit.prevent="sendExpenditure()" class="d-flex flex-column justify-content-between mt-2 ms-2 h-100">
          <div class="form-row mt-3">
            <div class="col-lg-3 me-3">
              <label class="form-label me-4">Project ID:</label>
              <input type="text" name="OtherExp_ID" id="" class="form-control" placeholder="1" v-model="data.project_id" disabled>
            </div>
            <div class="col-lg-3 me-3">
              <label class="form-label me-4">OR/Acc Num</label>
              <input type="text" :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" name="OtherExp_AccNum" id="" class="form-control" placeholder="022654" v-model="data.exp_ORNum">
            </div>
            <div class="col-lg-3 me-3">
              <label class="form-label me-4">Project Commence Date</label>
              <input type="date" name="OtherExp_AccNum" id="" class="form-control" disabled placeholder="022654" v-model="data.project_commenceDate">
            </div>
          </div>
          <div class="form-row mt-2">
            <div class="col-lg-4 me-1">
              <label class="form-label me-4">Type of Expense</label>
              <select class="form-select" :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" @change="setExpenseType($event)">
                <option disabled selected value="None">Select Expense</option>
                <option value="Utilities">Utilities</option>
                <option value="Food">Food</option>
                <option value="Transportation">Transportation</option>
                <option value="Communications">Communications</option>
                <option value="Maintenance">Maintenance</option>
              </select>
            </div>
          </div>
          <div class="form-row mt-2">
            <div class="col-lg-4 me-1">
              <label class="form-label me-4">Period from:</label>
              <input type="date" :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" class="form-control" v-model="data.exp_dateFrom">
            </div>
            <div class="col-lg-4 me-1">
              <label class="form-label me-4">To:</label>
              <input type="date" :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" class="form-control" v-model="data.exp_dateTo">
            </div>
          </div>
          <div class="form-row mt-2">
            <div class="col-lg-3">
              <label class="form-label me-4">Payment Type</label>
              <select class="form-select" @change="setPaymentType($event)">
                <option disabled selected value="None">Select Payment</option>
                <option value="Cash">Cash</option>
                <option value="Bank">Bank</option>
              </select>
            </div>
            <div v-if="data.exp_paymentType == 'Bank'" class="col-lg-3">
              <label class="form-label me-4">Bank Name</label>
              <input :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" type="text" class="form-control" v-model="data.exp_bankName">
            </div>
            <div v-if="data.exp_paymentType == 'Bank'" class="col-lg-3">
              <label class="form-label me-4">Account Number</label>
              <input :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" type="text" class="form-control" v-model="data.exp_accNum">
            </div>
            <div class="col-lg-3">
              <label class="form-label me-4">Account Name</label>
              <input :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" type="text" class="form-control" v-model="data.exp_accName">
            </div>
          </div>
          <div class="form-row mt-2">
            <div class="col-md-5">
              <label class="form-label me-4">Amount</label>
              <input :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" type="number" class="form-control" v-model="data.exp_amount">
            </div>
            <div class="col-md-5">
              <label class="form-label me-4">Remarks</label>
              <input :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" type="text" class="form-control" v-model="data.exp_remark">
            </div>
          </div>
          <div class="btn-toolbar pt-2">
            <div class="btn-group me-3 mt-3">
              <button type="submit" :disabled="getProjectForExpenditure && (getProjectForExpenditure.project_status_id == 4 || getProjectForExpenditure.project_status_id == 5)" class="btn btn-success" style="width:150px; height: 40px;">Add to Project</button>
            </div>           
        </div>
        </form>
      </div>      
      <div :style="[getExpenditures && getExpenditures.length > 9 ? {'overflow-y':'scroll'} : {}, {'width':'65%'}, {'height': '65%'}]" class="pb-5">
        <h4 align="center" class="mt-3">List of Other Expenditures for Project # {{ $route.params.id }}</h4>
        <table id="OtherExpendituresList" class="table table-striped table-success table-bordered align-middle" style="width:100%;">
              <thead align="center">
                <tr>                
                  <th scope="col">Type of Expenses</th>
                  <th scope="col">Payment Type</th>
                  <th scope="col">Amount</th>
                  <th scope="col">Remarks</th>   
                  <th scope="col">Date</th>                                 
                </tr>
              </thead>
              <tbody align="center">
                  <tr v-for="(exp, index) in getExpenditures" :key="index">
                    <td>{{ exp.exp_type }}</td>
                    <td>{{ exp.exp_paymentType }}</td>
                    <td>{{ exp.exp_amount }}</td>
                    <td>{{ exp.exp_remark }}</td>
                    <td>{{ exp.exp_dateFrom + ' ' + exp.exp_dateTo }}</td>
                  </tr>                          
              </tbody>
          </table>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: "OtherExpenditures",
    created() {
      this.fetchProjectExpenditures(this.$route.params.id)
      .then(() => {
        this.data.project_commenceDate = this.getProjectCommenceDate
        this.data.exp_dateFrom = this.data.project_commenceDate
        this.readyApp()
      })   
      .catch((err) => {
        console.error(err)
        this.$router.push({ name: 'AllProjects' })
      })    
    },
    data(){
      return{
        data: {
          project_id: this.$route.params.id,
          exp_ORNum: null,
          exp_type: null,
          exp_dateFrom: null,
          exp_dateTo: null,
          exp_paymentType: null,
          exp_accNum: null,
          exp_bankName: null,
          exp_accName: null,
          exp_amount: null,
          exp_remark: null,
          project_commenceDate: null          
        }
      }
    },
    watch: {
      'data.exp_paymentType'(){
        this.data.exp_accName = null
        this.data.exp_bankName = null
        this.data.exp_accNum = null
      },
      'data.exp_amount'(newVal){
        this.data.exp_amount = Math.abs(newVal)
      }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProjectExpenditures', 'addProjectExpenditure']),
        sendExpenditure(){
          this.addProjectExpenditure(this.data)
          .then(() => {
            this.$toastr.s('Expenditure added successfully!')
            setTimeout(() => {
              this.$router.push({ path: `projects/${this.$route.params.id}` })
            }, 5000)
          })
        },
        setExpenseType(e){
          this.data.exp_type = e.target.value
        },
        setPaymentType(e){
          this.data.exp_paymentType = e.target.value
        }      
    },
    computed: {
      ...mapGetters(['getExpenditures', 'getProjectCommenceDate', 'getProjectForExpenditure']),  
    }
}
</script>

<style>

</style>
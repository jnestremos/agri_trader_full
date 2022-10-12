<template>
  <div class="OtherExpendituresReport">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Other Expenditures Report</h3>
        <div class="d-flex">
            <router-link to="/reports/dashboard"><button class="btn btn-info text-right">Return to Reports Dashboard</button></router-link>
        </div>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
      <div style="width:85%; height:65%" class="pb-5">
        <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">Project</label>
                <select class="form-select" @change="setProjectID($event)">
                    <option value="None">Select Project</option>
                    <option v-for="(id, index) in filterProjectIDS" :key="index" :value="id">{{ getProjectName(id) }}</option>
                </select>
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">Expense Type</label>
                <select class="form-select" @change="setType($event)">
                    <option selected value="None">Select Expense Type</option>
                    <option value="Utilities">Utilities</option>                                
                    <option value="Food">Food</option>                                
                    <option value="Transportation">Transportation</option>                                
                    <option value="Communication">Communication</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">Total</label>
                <input type="number" name="" disabled :value="getTotal" class="form-control" style="width:200px" id="">
            </div>
        </div>        
        <div class="container-fluid m-0 p-0" :style="[filterTable && filterTable.length > 6 ? {'overflow-y':'scroll'} : {}, {'width':'100%'}, {'height':'40vh'}]">
            <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                <thead align="center">
                    <tr>
                        <th scope="col">Project No.</th>
                        <th scope="col">OR No.</th>
                        <th scope="col">Project Name</th>
                        <th scope="col">Type of Expense</th>
                        <th scope="col">Payment Date</th>
                        <th scope="col">Amount</th>
                    </tr>
                </thead>
                <tbody align="center">
                    <tr v-for="(e, index) in filterTable" :key="index">
                        <td>{{ e.project_id }}</td>
                        <td>{{ e.exp_ORNum }}</td>
                        <td>{{ getProjectName(e) }}</td>
                        <td>{{ e.exp_type }}</td>
                        <td>{{ e.exp_dateFrom + ' ' + e.exp_dateTo }}</td>
                        <td>{{ e.exp_amount.toFixed(2) }}</td>
                    </tr>                                                                 
                </tbody>
            </table>
          </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
    name: "OtherExpendituresReport",
    created() {
        this.fetchExpenditureReport()
        .then(() => {
            this.readyApp()
        })        
    },
    data(){
        return {
            filter_project: 'None',
            filter_type: 'None'
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchExpenditureReport']),
        getProjectName(expenditure){
            var projectObj = null
            if(typeof(expenditure) == 'object'){
                projectObj = this.getExpenditureReport.projects.filter((p) => {
                    return parseInt(expenditure.project_id) === parseInt(p.id)
                })
            }
            else{
                projectObj = this.getExpenditureReport.projects.filter((p) => {
                    return parseInt(expenditure) === parseInt(p.id)
                })
            }
            var contractObj = this.getExpenditureReport.contracts.filter((c) => {
                return parseInt(projectObj[0].contract_id) === parseInt(c.id)
            })
            return contractObj[0].farm_name + ' Project'
        },
        setProjectID(e){
            this.filter_project = e.target.value
        },
        setType(e){
            this.filter_type = e.target.value
        },
    },
    computed: {
        ...mapGetters(['getExpenditureReport']),
        filterProjectIDS(){
            var ids = new Set();
            var arr = []
            if(this.getExpenditureReport.expenditures){
                this.getExpenditureReport.expenditures.forEach((e) => {
                    ids.add(e.project_id)
                })
                ids.forEach((id) => {
                    arr.push(id)
                })
            }
            return arr
        },
        filterTable(){
            var table = [];            
            if(this.filter_project != 'None' && this.filter_type == 'None'){
                table = this.getExpenditureReport.expenditures.filter((e) => {
                    return parseInt(this.filter_project) === parseInt(e.project_id)
                })
                return table
            }
            else if(this.filter_project == 'None' && this.filter_type != 'None'){
                table = this.getExpenditureReport.expenditures.filter((e) => {
                    return (this.filter_type) === (e.exp_type)
                })
                return table
            }
            else if(this.filter_project != 'None' && this.filter_type != 'None'){
                table = this.getExpenditureReport.expenditures.filter((e) => {
                    return (this.filter_type) === (e.exp_type) && parseInt(this.filter_project) === parseInt(e.project_id)
                })
                return table
            }
            else{
                return this.getExpenditureReport.expenditures
            }
        },
        getTotal(){
            var amounts = [];
            if(this.filterTable){
                this.filterTable.forEach((e) => [
                    amounts.push(e.exp_amount)
                ])
                var total = amounts.reduce((a, b) => a + b, 0)
            }

            return total ? total.toFixed(2) : null
        }
    }
}
</script>


<style>

</style>
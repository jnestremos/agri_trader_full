<template>
    <div class="ProfitSharingReport">
      <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Profit Sharing Report</h3>
        <!-- <div class="d-flex">
            <router-link to="/reports/dashboard"><button class="btn btn-info text-right">Return to Reports Dashboard</button></router-link>
        </div> -->
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Farm</label>
                  <select class="form-select" :disabled="this.getProfitSharingReport.profit_sharing && this.getProfitSharingReport.profit_sharing.length == 0" @change="setFarm($event)">
                      <option value="None">Select Farm</option>
                      <option v-for="(farm, index) in getProfitSharingReport.farms" :key="index" :value="farm.id">{{ getFarmName(farm) }}</option>
                  </select>
              </div>              
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Produce</label>
                  <select class="form-select" :disabled="this.getProfitSharingReport.profit_sharing && this.getProfitSharingReport.profit_sharing.length == 0" @change="setProduce($event)">
                      <option selected value="None">Select Produce</option>
                      <option v-for="(produce, index) in getProfitSharingReport.produces" :key="index" :value="produce.id">{{ produce.prod_name + ' ' + produce.prod_type }}</option>
                  </select>
              </div>              
          </div>
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">From</label>
                <input type="date" :disabled="this.getProfitSharingReport.profit_sharing && this.getProfitSharingReport.profit_sharing.length == 0" v-model="filter_dateFrom" class="form-control">
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">To</label>
                <div class="d-flex align-items-baseline">
                    <input type="date" :disabled="this.getProfitSharingReport.profit_sharing && this.getProfitSharingReport.profit_sharing.length == 0" v-model="filter_dateTo" class="form-control me-3">
                    <input type="reset" :disabled="this.getProfitSharingReport.profit_sharing && this.getProfitSharingReport.profit_sharing.length == 0" class="btn btn-secondary" value="Reset" @click="resetFilter()">
                </div>
            </div>
          </div>
          <!-- ;  ; -->
          <div class="container-fluid m-0 p-0" :style="[filteredTable && filteredTable.length > 7 ? {'overflow-y': 'scroll'} : {}, {'width':'100%'}, {'height':'40vh'}]">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Project No.</th>
                          <th scope="col">Project Name</th>
                          <th scope="col">Farm Name</th>
                          <th scope="col">Produce Name</th>
                          <th scope="col">Total Sales</th>
                          <th scope="col">Total Expenses</th>
                          <th scope="col">Owner Share</th>
                          <th scope="col">Trader Share</th>
                          <th scope="col">Net Profit</th>
                          <th scope="col">Date Completed</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr v-for="(report, index) in filteredTable" :key="index">
                        <td>{{ report.project_id }}</td>
                        <td>{{ getProjectName(report) }}</td>
                        <td>{{ getFarmNameTable(report) }}</td>
                        <td>{{ getProduceName(report) }}</td>
                        <td>{{ report.ar_totalSales }}</td>
                        <td>{{ report.ar_totalExpenses }}</td>
                        <td>{{ report.ar_ownerShare }}</td>
                        <td>{{ Math.abs(report.ar_totalSales - report.ar_ownerShare) }}</td>
                        <td>{{ report.ar_profit }}</td>
                        <td>{{ report.ar_approvedOn }}</td>
                    </tr>
                  </tbody>
              </table>
            </div>
            <div class="d-flex align-items-baseline justify-content-between mt-5">
                <h5>Total Income: {{ getIncome.toFixed(2) }}</h5>
                <h5>Total Expenses: {{ getExpenses.toFixed(2) }}</h5>
                <h5>Balance: {{ getBalance.toFixed(2) }}</h5>
            </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
import { format, sub, add } from 'date-fns';
  import { mapActions, mapGetters } from 'vuex';
  export default {
      name: "ProfitSharingReport",
      created() {
        this.fetchProfitSharingReport()
        .then(() => {
            if(this.getProfitSharingReport.profit_sharing && this.getProfitSharingReport.profit_sharing.length > 0){
                var sortedData = this.getProfitSharingReport.profit_sharing.sort((a, b) => {
                    return new Date(a.created_at) - new Date(b.created_at)
                })    
                this.filter_dateFrom = format(new Date(sortedData[0].created_at), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(sortedData[sortedData.length - 1].created_at), 'yyyy-MM-dd')                
            }
            this.readyApp()
        })          
      },
      data(){
        return {
            filter_farm: "None",
            filter_produce: "None",
            filter_dateFrom: null,
            filter_dateTo: null,
        }
      },
      watch:{
        filter_dateFrom(newVal, oldVal){
            if(!newVal){
               this.filter_dateFrom = oldVal 
            }
            else if(newVal > this.filter_dateTo){
                this.filter_dateFrom = format(sub(new Date(this.filter_dateTo), {
                    days: 1
                }), 'yyyy-MM-dd')
            }
        },
        filter_dateTo(newVal, oldVal){
            if(!newVal){
                this.filter_dateTo = oldVal 
            }
            else if(newVal < this.filter_dateFrom){
                this.filter_dateTo = format(add(new Date(this.filter_dateFrom), {
                    days: 1
                }) , 'yyyy-MM-dd')
            }
        },
      },
      methods: {
          ...mapActions(['readyApp', 'fetchProfitSharingReport']),
          resetFilter(){
            var sortedData = this.getProfitSharingReport.profit_sharing.sort((a, b) => {
                return new Date(a.created_at) - new Date(b.created_at)
            })   
            this.filter_farm = 'None' 
            this.filter_produce = 'None' 
            this.filter_dateFrom = format(new Date(sortedData[0].created_at), 'yyyy-MM-dd')
            this.filter_dateTo = format(new Date(sortedData[sortedData.length - 1].created_at), 'yyyy-MM-dd')
          },
          setFarm(e){
            this.filter_farm = e.target.value
          },
          setProduce(e){
            this.filter_type = e.target.value
          },
          getFarmName(farm){
            var farmObj = this.getProfitSharingReport.farms.filter((f) => {
                return parseInt(farm.id) === parseInt(f.id)
            })
            var ownerObj = this.getProfitSharingReport.farm_owners.filter((o) => {
                return parseInt(farmObj[0].farm_owner_id) === parseInt(o.id)
            })
            return farmObj[0].farm_name + ' - ' + ownerObj[0].owner_firstName + ' ' + ownerObj[0].owner_lastName
          },
          getProjectName(report){
            var projectObj = this.getProfitSharingReport.projects.filter((p) => {
                return parseInt(report.project_id) === parseInt(p.id)
            })
            var contractObj = this.getProfitSharingReport.contracts.filter((c) => {
                return parseInt(projectObj[0].contract_id) === parseInt(c.id)
            })
            return contractObj[0].farm_name + ' Project'
          },
          getFarmNameTable(report){
            var farmObj = this.getProfitSharingReport.farms.filter((f) => {
                return parseInt(report.farm_id) === parseInt(f.id)
            })
            var ownerObj = this.getProfitSharingReport.farm_owners.filter((o) => {
                return parseInt(farmObj[0].farm_owner_id) === parseInt(o.id)
            })
            return farmObj[0].farm_name + ' - ' + ownerObj[0].owner_firstName + ' ' + ownerObj[0].owner_lastName
          },
          getProduceName(report){
            var prodObj = this.getProfitSharingReport.produces.filter((p) => {
                return parseInt(report.produce_id) === parseInt(p.id)
            })  
            return prodObj[0].prod_name + ' ' + prodObj[0].prod_type
          }
      },
      computed: {
        ...mapGetters(['getProfitSharingReport']),
        filteredTable(){
            var table = [];
            if(this.getProfitSharingReport.profit_sharing){
                table = this.getProfitSharingReport.profit_sharing.filter((p) => {
                    return format(new Date(p.created_at), 'yyyy-MM-dd') >= format(new Date(this.filter_dateFrom), 'yyyy-MM-dd')
                    && format(new Date(p.created_at), 'yyyy-MM-dd') <= format(new Date(this.filter_dateTo), 'yyyy-MM-dd')
                })
                if(this.filter_farm != 'None' && this.filter_produce != 'None'){
                    table = table.filter((pp) => {
                        return parseInt(this.filter_farm) === parseInt(pp.farm_id) 
                        && parseInt(this.filter_produce) === parseInt(pp.produce_id)
                    })
                }
                else if(this.filter_farm == 'None' && this.filter_produce != 'None'){
                    table = table.filter((pp) => {
                        return parseInt(this.filter_produce) === parseInt(pp.produce_id)
                    })
                }
                else if(this.filter_farm != 'None' && this.filter_produce == 'None'){
                    table = table.filter((pp) => {
                        return parseInt(this.filter_farm) === parseInt(pp.farm_id)                     
                    })
                }
            }                       
            return table
        },
        getIncome(){
            var income = 0
            if(this.getProfitSharingReport.accs && this.getProfitSharingReport.accs.length > 0
            || this.getProfitSharingReport.sales && this.getProfitSharingReport.sales.length > 0){
                var accs = []
                var sales = []                
                this.getProfitSharingReport.accs.forEach((a) => {
                    accs.push(a.bid_order_acc_amount)
                })
                console.log(accs)
                this.getProfitSharingReport.sales.forEach((s) => {
                    accs.push(s.sale_total)
                })
                income = accs.reduce((a, b) => parseFloat(a) + parseFloat(b), income)
                income = sales.reduce((a, b) => parseFloat(a) + parseFloat(b), income)                 
            }
            return income
        },
        getExpenses(){
            var expense = 0
            if(this.getProfitSharingReport.profit_sharing 
            && this.getProfitSharingReport.profit_sharing.length > 0){
                var expenses = [];
                this.getProfitSharingReport.profit_sharing.forEach((s) => {
                    expenses.push(s.ar_totalExpenses)
                })
                expense = expenses.reduce((a, b) => parseFloat(a) + parseFloat(b), expense)
            }
            return expense
        },
        getBalance(){
            return this.getIncome - this.getExpenses
        }
      },      
  }
  </script>
  
  
  <style>
  
  </style>
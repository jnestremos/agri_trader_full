<template>
  <div class="profitSharingReportOwner">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
              <h3>Profit Sharing Report</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Farm</label>
                  <select class="form-select" :disabled="this.getProfitSharingReportForOwner.profit_sharings 
                  && this.getProfitSharingReportForOwner.profit_sharings.length == 0" @change="setFarm($event)">
                      <option value="None">None</option>
                      <option v-for="(farm, index) in getProfitSharingReportForOwner.farms" :key="index" :value="farm.id">{{ getFarmName(farm) }}</option>
                  </select>
              </div>              
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Produce</label>
                  <select class="form-select" :disabled="this.getProfitSharingReportForOwner.profit_sharings 
                  && this.getProfitSharingReportForOwner.profit_sharings.length == 0" @change="setProduce($event)">
                      <option selected value="None">None</option>
                      <option v-for="(produce, index) in getProfitSharingReportForOwner.produces" :key="index" :value="produce.id">{{ produce.prod_name + ' ' + produce.prod_type }}</option>
                  </select>
              </div>              
          </div>
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">From</label>
                <input type="date" :disabled="this.getProfitSharingReportForOwner.profit_sharings 
                  && this.getProfitSharingReportForOwner.profit_sharings.length == 0" v-model="filter_dateFrom" class="form-control">
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">To</label>
                <div class="d-flex align-items-baseline">
                    <input type="date" :disabled="this.getProfitSharingReportForOwner.profit_sharings 
                  && this.getProfitSharingReportForOwner.profit_sharings.length == 0" v-model="filter_dateTo" class="form-control me-3">
                    <input type="reset" :disabled="this.getProfitSharingReportForOwner.profit_sharings 
                  && this.getProfitSharingReportForOwner.profit_sharings.length == 0" class="btn btn-secondary" value="Reset" @click="resetFilter()">
                </div>
            </div>
          </div>
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Project No.</th>
                          <th scope="col">Project Name</th>
                          <th scope="col">Farm Name</th>
                          <th scope="col">Total Sales</th>
                          <th scope="col">Produce Name</th>
                          <!-- <th scope="col">Total Expenses</th> -->
                          <th scope="col">Owner Share</th>
                          <!-- <th scope="col">Trader Share</th> -->
                          <!-- <th scope="col">Net Profit</th> -->
                          <th scope="col">Date Completed</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr v-for="(report, index) in filteredTable" :key="index">
                        <td>{{ report.project_id }}</td>
                        <td>{{ getProjectName(report) }}</td>
                        <td>{{ getFarmNameTable(report) }}</td>
                        <td>{{ report.ar_totalSales }}</td>
                        <td>{{ getProduceName(report) }}</td>
                        <!-- <td>{{ report.ar_totalExpenses }}</td> -->
                        <td>{{ report.ar_ownerShare }}</td>
                        <!-- <td>{{ report.ar_totalSales - report.ar_ownerShare }}</td> -->
                        <!-- <td>{{ report.ar_profit }}</td> -->
                        <td>{{ report.ar_approvedOn }}</td>
                    </tr>
                  </tbody>
              </table>
            </div>
        </div>
      </div>
  </div>
</template>

<script>
import { add, format, sub } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "ProfitSharingReportOwner",
    created(){
        this.fetchProfitSharingReportForOwner()
        .then(() => {
            if(this.getProfitSharingReportForOwner.profit_sharings
            && this.getProfitSharingReportForOwner.profit_sharings.length > 0){
                var sortedData = this.getProfitSharingReportForOwner.profit_sharings.sort((a, b) => {
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
        ...mapActions(['readyApp', 'fetchProfitSharingReportForOwner']),
        resetFilter(){
            var sortedData = this.getProfitSharingReportForOwner.profit_sharing.sort((a, b) => {
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
            var farmObj = this.getProfitSharingReportForOwner.farms.filter((f) => {
                return parseInt(farm.id) === parseInt(f.id)
            })            
            return farmObj[0].farm_name
          },
          getProjectName(report){
            var projectObj = this.getProfitSharingReportForOwner.projects.filter((p) => {
                return parseInt(report.project_id) === parseInt(p.id)
            })
            var contractObj = this.getProfitSharingReportForOwner.contracts.filter((c) => {
                return parseInt(projectObj[0].contract_id) === parseInt(c.id)
            })
            return contractObj[0].farm_name + ' Project'
          },
          getFarmNameTable(report){
            var farmObj = this.getProfitSharingReportForOwner.farms.filter((f) => {
                return parseInt(report.farm_id) === parseInt(f.id)
            })            
            return farmObj[0].farm_name
          },
          getProduceName(report){
            var prodObj = this.getProfitSharingReportForOwner.produces.filter((p) => {
                return parseInt(report.produce_id) === parseInt(p.id)
            })  
            return prodObj[0].prod_name + ' ' + prodObj[0].prod_type
          }
    },
    computed: {
        ...mapGetters(['getProfitSharingReportForOwner']),
        filteredTable(){
            var table = []
            if(this.getProfitSharingReportForOwner.profit_sharings 
            && this.getProfitSharingReportForOwner.profit_sharings.length > 0){
                table = this.getProfitSharingReportForOwner.profit_sharings.filter((p) => {
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
        }
    }
}
</script>

<style>

</style>
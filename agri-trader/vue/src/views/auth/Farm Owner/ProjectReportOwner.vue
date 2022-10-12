<template>
  <div class="projectReportOwner">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Projects</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">            
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">Select Project</label>
                <select name="" class="form-select" id="" @change="setContract($event)">
                    <option selected value="None">None</option>
                    <option v-for="(contract, index) in getProjectReportForOwner.contracts" :key="index" :value="contract.id">{{ contract.id }}</option>
                </select>
            </div>            
          </div>
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">From</label>
                <input type="date" v-model="filter_dateFrom" class="form-control">
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">To</label>
                <div class="d-flex align-items-baseline">
                    <input type="date" v-model="filter_dateTo" class="form-control me-3">
                    <input type="reset" class="btn btn-secondary" value="Reset">
                </div>
            </div>
          </div>   
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                        <th scope="col">Project #</th>                                                                               
                        <th scope="col">Project Name</th>
                        <th scope="col">Farm Name</th>                                                                             
                        <th scope="col">Status</th>
                        <th scope="col">Date of Project Start</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr style="cursor:pointer;" v-for="(contract, index) in filteredTable" :key="index" @click="showProject(contract)">
                        <td>{{ getProjectID(contract) }}</td>                                                                            
                        <td>{{ contract.farm_name + ' Project' }}</td>
                        <td>{{ contract.farm_name }}</td>                                                               
                        <td>{{ getStatus(contract) }}</td>
                        <td>{{ getProjectStart(contract) }}</td>    
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
    name: 'ProjectReportOwner',
    created(){
        this.fetchProjectReportForOwner()
        .then(() => {
            if(this.getProjectReportForOwner.contracts && this.getProjectReportForOwner.contracts.length > 0){
                var contracts = this.getProjectReportForOwner.contracts.sort((a, b) => {
                    return new Date(a.created_at) - new Date(b.created_at)
                })
                this.filter_dateFrom = format(new Date(contracts[0].created_at), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(contracts[contracts.length - 1].created_at), 'yyyy-MM-dd')
            }
            this.readyApp()
        })
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
    data(){
        return {
            filter_dateFrom: null,
            filter_dateTo: null,
            filter_contract: "None",
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProjectReportForOwner']),
        setContract(e){
            this.filter_contract = e.target.value
        },
        getProjectID(contract){
            var projObj = this.getProjectReportForOwner.projects.filter((p) => {
                return parseInt(contract.id) === parseInt(p.contract_id)
            })
            return projObj[0].id
          },
          getStatus(contract){
            var projObj = this.getProjectReportForOwner.projects.filter((p) => {
                return parseInt(contract.id) === parseInt(p.contract_id)
            })
            if(projObj[0].project_status_id == 1){
                return 'Pending'
            }
            else if(projObj[0].project_status_id == 2){
                return 'Active'
            }
            else if(projObj[0].project_status_id == 3){
                return 'Cancelled'
            }
            else if(projObj[0].project_status_id == 4){
                return 'Completed'
            }
            else if(projObj[0].project_status_id == 5){
                return 'Failed'
            }
          },
          getProjectStart(contract){
            var projObj = this.getProjectReportForOwner.projects.filter((p) => {
                return parseInt(contract.id) === parseInt(p.contract_id)
            })
            return projObj[0].created_at.split('T')[0]
          },
          showProject(contract){
            var id = this.getProjectID(contract)
            this.$router.push({ path: `/projects/owner/${id}` })            
          },
    },
    computed: {
        ...mapGetters(['getProjectReportForOwner']),
        filteredTable(){
            var table = [];
            if(this.getProjectReportForOwner.contracts && this.getProjectReportForOwner.contracts.length > 0){
                table = this.getProjectReportForOwner.contracts.filter((s) => {
                    return format(new Date(s.created_at), 'yyyy-MM-dd') >= this.filter_dateFrom
                    && format(new Date(s.created_at), 'yyyy-MM-dd') <= this.filter_dateTo
                })
                if(this.filter_contract != "None"){
                    table = table.filter((c) => {
                        return parseInt(c.id) === parseInt(this.filter_contract)
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
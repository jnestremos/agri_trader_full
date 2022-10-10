<template>
    <div class="projectReport">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Projects</h3>
        </div>
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
          <div style="width:85%; height:65%" class="pb-5">
              <div class="form-row mt-3">
                  <div class="col-lg-3 me-3">
                      <label for="stockInHitsory_supplierList" class="form-label me-4">Select Project #</label>
                      <select class="form-select" id="supplier_name" @change="setContract($event)">
                          <option selected value="None">None</option>
                          <option v-for="(contract, index) in getProjectReport.contracts" :key="index" :value="contract.id">{{ contract.id }}</option>                        
                      </select>                    
                  </div>                  
                  <div class="col-lg-3 me-3">
                      <label for="stockInHitsory_supplierList" class="form-label me-4">Date From</label>
                      <input type="date" name="" class="form-control" id="" v-model="filter_dateFrom">                  
                  </div>                  
                  <div class="col-lg-3 me-3">
                    <label for="stockInHitsory_supplierList" class="form-label me-4">Date To</label>
                    <input type="date" name="" class="form-control" id="" v-model="filter_dateTo">                    
                  </div>                  
              </div>
              <div class="mb-2 mt-4" style="width:100%; height:90%; clear:left;">
                  <table id="supplySelect" class="table table-striped table-bordered align-middle" :style="[filteredTable && filteredTable.length > 8 ? {'overflow-y':'scroll'} : {}, {'width': '100%'}]">
                      <thead align="center">
                          <tr>                              
                              <th scope="col">Project #</th>                                                                               
                              <th scope="col">Project Name</th>
                              <th scope="col">Farm Name</th>                             
                              <th scope="col">Farm Owner</th>                             
                              <th scope="col">Status</th>
                              <th scope="col">Date of Project Start</th>
                          </tr>
                      </thead>
                      <tbody align="center">
                          <tr style="cursor:pointer;" v-for="(contract, index) in filteredTable" :key="index" @click="showProject(contract)">                   
                              <td>{{ getProjectID(contract) }}</td>                                                                            
                              <td>{{ contract.farm_name + ' Project' }}</td>
                              <td>{{ contract.farm_name }}</td>
                              <td>{{ getFarmOwner(contract) }}</td>                                            
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
import { format } from 'date-fns'
  import { mapActions, mapGetters } from 'vuex'
  export default {
      name: 'ProjectReport',
      created(){
        this.fetchProjectReport()
        .then(() => {
            if(this.getProjectReport.projects && this.getProjectReport.projects.length > 0){
                var projects = this.getProjectReport.projects.sort((a, b) => {
                    return new Date(a.created_at) - new Date(b.created_at)
                })
                this.filter_dateFrom = format(new Date(projects[0].created_at), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(projects[projects.length - 1].created_at), 'yyyy-MM-dd')
            }
            this.readyApp()
        })          
      },
      data(){
        return {
            filter_contract: 'None',
            filter_dateFrom: null,
            filter_dateTo: null,
        }
      },
      methods: {
          ...mapActions(['readyApp', 'fetchProjectReport']),
          setContract(e){
            this.filter_contract = e.target.value
          },
          getProjectID(contract){
            var projObj = this.getProjectReport.projects.filter((p) => {
                return parseInt(contract.id) === parseInt(p.contract_id)
            })
            return projObj[0].id
          },
          showProject(contract){
            var id = this.getProjectID(contract)
            this.$router.push({ path: `/projects/${id}` })            
          },
          getFarmOwner(contract){            
            var farmObj = this.getProjectReport.farms.filter((f) => {
                return parseInt(contract.farm_id) === parseInt(f.id)
            })
            var ownerObj = this.getProjectReport.farm_owners.filter((o) => {
                return parseInt(farmObj[0].farm_owner_id) === parseInt(o.id)
            })
            
            return ownerObj[0].owner_firstName + ' ' + ownerObj[0].owner_lastName
          },
          getStatus(contract){
            var projObj = this.getProjectReport.projects.filter((p) => {
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
            var projObj = this.getProjectReport.projects.filter((p) => {
                return parseInt(contract.id) === parseInt(p.contract_id)
            })
            return projObj[0].created_at.split('T')[0]
          },
      },
      computed: {
          ...mapGetters(['getProjectReport']),
          filteredTable(){
            var table = [];
            if(this.getProjectReport.contracts){
                table = this.getProjectReport.contracts.filter((c) => {
                    return format(new Date(c.created_at), 'yyyy-MM-dd') >= this.filter_dateFrom
                    && format(new Date(c.created_at), 'yyyy-MM-dd') <= this.filter_dateTo
                })
                if(this.filter_contract != 'None'){
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
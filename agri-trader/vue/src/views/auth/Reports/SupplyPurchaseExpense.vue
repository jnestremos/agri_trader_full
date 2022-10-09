<template>
    <div class="SupplyPurchaseExpenseReport">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
              <h3>Supply Purchase Expense Report</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Project</label>
                  <select class="form-select">
                      <option value="None">Select Project</option>
                      <option v-for="(id, index) in filterProjectIDS" :key="index" :value="id">{{ getProjectName(id) }}</option>                      
                  </select>
              </div>
          </div>
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Project No.</th>
                          <th scope="col">Project Name</th>
                          <th scope="col">Supplier Name</th>
                          <th scope="col">Supply Name</th>
                          <th scope="col">Price</th>
                          <th scope="col">Qty Used</th>
                          <th scope="col">Date Used</th>
                          <th scope="col">Subtotal</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr v-for="(stock, index) in filterTable" :key="index">
                        <td>{{ stock.project_id }}</td>
                        <td>{{ getProjectName(stock) }}</td>
                        <td>{{ getSupplierName(stock) }}</td>
                        <td>{{ getSupplyName(stock) }}</td>
                        <td>{{ getPrice(stock) }}</td>
                        <td>{{ stock.supply_qty }}</td>
                        <td>{{ stock.created_at.split('T')[0] }}</td>
                        <td>{{ getAmount(stock) }}</td>
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
      name: "SupplyPurchaseExpenseReport",
      created() {
        this.fetchStockOutReport()
        .then(() => {
            this.readyApp()
        })        
      },
      data(){
        return {
            filter_project: 'None'
        }
      },
      methods: {
          ...mapActions(['readyApp', 'fetchStockOutReport']),
          getProjectName(stock){
            var projectObj = null
            if(typeof(stock) == 'object'){
                projectObj = this.getStockOutReport.projects.filter((p) => {
                    return parseInt(stock.project_id) === parseInt(p.id)
                })
            }
            else{
                projectObj = this.getStockOutReport.projects.filter((p) => {
                    return parseInt(stock) === parseInt(p.id)
                })
            }
            var contractObj = this.getStockOutReport.contracts.filter((c) => {
                return parseInt(projectObj[0].contract_id) === parseInt(c.id)
            })
            return contractObj[0].farm_name + ' Project'
        },
        setProjectID(e){
            this.filter_project = e.target.value
        },
        getSupplierName(stock){
            var supplierObj = this.getStockOutReport.suppliers.filter((s) => {
                return parseInt(stock.supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        },
        getSupplyName(stock){
            var supplyObj = this.getStockOutReport.supplies.filter((s) => {
                return parseInt(stock.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_name
        },
        getPrice(stock){
            var supplyObj = this.getStockOutReport.supplies.filter((s) => {
                return parseInt(stock.supply_id) === parseInt(s.id)
            })
            return parseInt(supplyObj[0].supply_initialPrice).toFixed(2)
        },
        getAmount(stock){
            var supplyObj = this.getStockOutReport.supplies.filter((s) => {
                return parseInt(stock.supply_id) === parseInt(s.id)
            })
            return parseFloat(supplyObj[0].supply_initialPrice * stock.supply_qty).toFixed(2)
        }
      },
      computed: {
        ...mapGetters(['getStockOutReport']),
        filterProjectIDS(){
            var ids = new Set();
            var arr = []
            if(this.getStockOutReport.stockOut){
                this.getStockOutReport.stockOut.forEach((e) => {
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
            if(this.filter_project != 'None'){
                table = this.getStockOutReport.stockOut.filter((s) => {
                    return parseInt(this.filter_project) === parseInt(s.project_id)
                })
                return table
            }
            else{
                return this.getStockOutReport.stockOut
            }
        }
      }
  }
  </script>
  
  
  <style>
  
  </style>
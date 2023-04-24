<template>
    <div class="SupplyPurchaseExpenseReport">
      <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Supply Purchase Expense Review</h3>
        <!-- <div class="d-flex">
            <router-link to="/reports/dashboard"><button class="btn btn-info text-right">Return to Reports Dashboard</button></router-link>
        </div> -->
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
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
                    <tr v-for="(stock, index) in records" :key="index">
                        <td>{{ stock.project_id }}</td>
                        <td>{{ getProjectName(stock) }}</td>
                        <td>{{ getSupplierName(stock) }}</td>
                        <td>{{ getSupplyName(stock) }}</td>
                        <td>{{ getPrice(stock) }}</td>
                        <td>{{ stock.supply_qty }}</td>
                        <td>{{ stock.created_at.split('T')[0] }}</td>
                        <td class="amount">{{ getAmount(stock) }}</td>
                    </tr>
                  </tbody>
              </table>
            </div>
            <div class="col-lg-5 mt-4">
                  <label class="form-label me-4 fw-bold h5">Total Supply Expenditures: </label>
                  <label class="form-label me-4 fw-bold h5" name="" style="width:200px" id="">{{ getTotal }}</label>
            </div>
        </div>
      </div>
    </div>
  </template>

  <script>
import { format, add, sub } from 'date-fns';
  import { mapActions, mapGetters } from 'vuex';
  export default {
      name: "SupplyExpensePreview",
      created() {
        if(!this.getPrintReport){
            this.$router.push({ path: "/reports/supplyExpenditures" })
        }
        else{
            this.records = this.getPrintReport
            this.readyApp()
        }

      },
      data(){
        return {
            filter_project: 'None',
            total_arr: [],
            filter_dateFrom: null,
            filter_dateTo: null,
            records: null,
        }
      },
      watch:{
        'records'(newVal){
            this.total_arr = []
            newVal.forEach((v) => {
                var supplyObj = this.getStockOutReport.supplies.filter((s) => {
                    return parseInt(v.supply_id) === parseInt(s.id)
                })
                this.total_arr.push(supplyObj[0].supply_initialPrice * v.supply_qty)
            })
        },
        filter_dateFrom(newVal, oldVal){
            if(!newVal){
               this.filter_dateFrom = oldVal
            }
            else if(newVal >= this.filter_dateTo){
                this.filter_dateFrom = format(sub(new Date(this.filter_dateTo), {
                    days: 1
                }), 'yyyy-MM-dd')
            }
        },
        filter_dateTo(newVal, oldVal){
            if(!newVal){
                this.filter_dateTo = oldVal
            }
            else if(newVal <= this.filter_dateFrom){
                this.filter_dateTo = format(add(new Date(this.filter_dateFrom), {
                    days: 1
                }) , 'yyyy-MM-dd')
            }
        },
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
            return parseInt(supplyObj[0].supply_initialPrice).toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
        },
        getAmount(stock){
            var supplyObj = this.getStockOutReport.supplies.filter((s) => {
                return parseInt(stock.supply_id) === parseInt(s.id)
            })
            return parseFloat(supplyObj[0].supply_initialPrice * stock.supply_qty).toLocaleString("en-PH", { style: 'currency', currency: 'PHP' })
        }
      },
      computed: {
        ...mapGetters(['getStockOutReport', 'getPrintReport']),
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
            if(this.getStockOutReport.stockOut && this.getStockOutReport.stockOut.length > 0){
                table = this.getStockOutReport.stockOut.filter((s) => {
                    return format(new Date(s.created_at), 'yyyy-MM-dd') >= this.filter_dateFrom &&
                    format(new Date(s.created_at), 'yyyy-MM-dd') <= this.filter_dateTo
                })
                if(this.filter_project != 'None'){
                    table = table.filter((s) => {
                        return parseInt(this.filter_project) === parseInt(s.project_id)
                    })
                }
                return table
            }
            return table
        },
        getTotal(){
            return this.total_arr.reduce((a, b) => a + b, 0)
            ? parseFloat(this.total_arr.reduce((a, b) => a + b, 0)).toLocaleString("en-PH", { style: 'currency', currency: 'PHP' }) : null
        }
      }
  }
  </script>


  <style>

  </style>

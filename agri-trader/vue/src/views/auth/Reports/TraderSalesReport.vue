<template>
    <div class="TraderSalesReport">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Sales Report</h3>      
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Select Project</label>
                  <select class="form-select" @change="setProject($event)">
                      <option value="None">None</option>
                      <option v-for="(project, index) in getSalesReport.projects" :key="index" :value="project.id">{{ getProjectName(project) }}</option>
                  </select>
              </div>
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Select Produce</label>
                  <select class="form-select" @change="setProduce($event)">
                      <option value="None">None</option>
                      <option v-for="(produce, index) in getSalesReport.produce_traders" :key="index" :value="produce.id">{{ getProduceName(produce) }}</option>                    
                  </select>
              </div>
          </div>
          <div class="form-row mb-3 mt-2">
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">From</label>
                <input type="date" class="form-control" v-model="filter_dateFrom">
            </div>
            <div class="col-lg-3 me-3">
                <label class="form-label me-4 fw-bold">To</label>
                <input type="date" class="form-control" v-model="filter_dateTo">
            </div>
          </div>
          <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <th scope="col">Project No.</th>
                          <th scope="col">Project Name</th>
                          <th scope="col">Produce Name</th>
                          <th scope="col">Sales Type</th>
                          <th scope="col">Qty Sold</th>
                          <th scope="col">Price Sold</th>
                          <th scope="col">Total</th>
                          <th scope="col">Date Rendered</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                        <tr v-for="(sale, index) in filteredTable" :key="index">
                            <td>{{ sale.project_id }}</td>
                            <td>{{ getProjectNameSale(sale) }}</td>
                            <td>{{ getProduceNameSale(sale) }}</td>
                            <td>{{ sale.sale_type }}</td>
                            <td>{{ sale.sale_qty }}</td>
                            <td>{{ sale.sale_price }}</td>
                            <td>{{ sale.sale_total }}</td>
                            <td>{{ sale.created_at.split('T')[0] }}</td>
                        </tr>                                                                        
                  </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
</template>
  
<script>
import { add, format, sub } from 'date-fns';
  import { mapActions, mapGetters } from 'vuex';
  export default {
      name: "TraderSalesReport",
      created() {
        this.fetchSalesReport()
        .then(() => {
            if(this.getSalesReport.sales && this.getSalesReport.sales.length > 0){
                var sales = this.getSalesReport.sales.sort((a, b) => {
                    return new Date(a.created_at) - new Date(b.created_at)
                })
                this.filter_dateFrom = format(new Date(sales[0].created_at), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(sales[sales.length - 1].created_at), 'yyyy-MM-dd')
            }
            this.readyApp()
        })
      },
      data(){
        return {
            filter_project: 'None',
            filter_produce: 'None',
            filter_dateFrom: null,
            filter_dateTo: null,
        }
      },
      watch:{
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
          ...mapActions(['readyApp', 'fetchSalesReport']),
          setProject(e){
            this.filter_project = e.target.value
          },
          setProduce(e){
            this.filter_produce = e.target.value
          },
          getProduceNameSale(sale){
            var prodTraderObj = null
            var prodObj = null
            var arr = null
            if(sale.bid_order_id){
                var orderObj = this.getSalesReport.orders.filter((o) => {
                    return parseInt(sale.bid_order_id) === parseInt(o.id)
                })
                prodTraderObj = this.getSalesReport.produce_traders.filter((p) => {
                    return parseInt(orderObj[0].produce_trader_id) === parseInt(p.id)
                })
                prodObj = this.getSalesReport.produces.filter((p) => {
                    return parseInt(prodTraderObj[0].produce_id) === parseInt(p.id)
                })
                arr = prodTraderObj[0].prod_name.split('(Class')
                if(arr.indexOf('(Class') != -1){
                    arr.splice(arr.indexOf('(Class'), 0, prodObj[0].prod_type)
                    return arr.join(' ')
                }
                else{
                    return prodTraderObj[0].prod_name + ' ' + prodObj[0].prod_type
                }
            }
            else{
                var inventoryObj = this.getSalesReport.produce_inventories.filter((i) => {
                    return parseInt(sale.produce_inventory_id) === parseInt(i.id)
                })
                var yieldObj = this.getSalesReport.produce_yields.filter((y) => {
                    return parseInt(inventoryObj[0].produce_yield_id) === parseInt(y.id)
                })
                prodTraderObj = this.getSalesReport.produce_traders.filter((p) => {
                    return parseInt(yieldObj[0].produce_trader_id) === parseInt(p.id)
                })
                prodObj = this.getSalesReport.produces.filter((p) => {
                    return parseInt(prodTraderObj[0].produce_id) === parseInt(p.id)
                })
                arr = prodTraderObj[0].prod_name.split('(Class')
                if(arr.indexOf('(Class') != -1){
                    arr.splice(arr.indexOf('(Class'), 0, prodObj[0].prod_type)
                    return arr.join(' ')
                }
                else{
                    return prodTraderObj[0].prod_name + ' ' + prodObj[0].prod_type
                }
            }

          },
          getProjectNameSale(sale){
            var projObj = this.getSalesReport.projects.filter((p) => {
                return parseInt(sale.project_id) === parseInt(p.id)
            })
            var contractObj = this.getSalesReport.contracts.filter((c) => {
                return parseInt(projObj[0].contract_id) === parseInt(c.id)
            })
            return contractObj[0].farm_name + ' Project'
          },
          getProjectName(project){
            var contractObj = this.getSalesReport.contracts.filter((c) => {
                return parseInt(project.contract_id) === parseInt(c.id)
            })
            return `${contractObj[0].id} - ${contractObj[0].farm_name} Project`
          },
          getProduceName(produce){
            var prodObj = this.getSalesReport.produces.filter((p) => {
                return parseInt(produce.produce_id) === parseInt(p.id)
            })
            var arr = produce.prod_name.split('(Class')
            if(arr.indexOf('(Class') != -1){
                arr.splice(arr.indexOf('(Class'), 0, prodObj[0].prod_type)
                return arr.join(' ')
            }
            else{
                return produce.prod_name + ' ' + prodObj[0].prod_type
            }
          }
      },
      computed:{
        ...mapGetters(['getSalesReport']),
        filteredTable(){
            var table = []       
            var orderObj = null            
            var inventoryObj = null            
            var yieldObj = null            
            var container = []
            var containerr = []
            if(this.getSalesReport.sales){
                table = this.getSalesReport.sales.filter((s) => {
                    return format(new Date(s.created_at), 'yyyy-MM-dd') >= this.filter_dateFrom
                    && format(new Date(s.created_at), 'yyyy-MM-dd') <= this.filter_dateTo
                })
                if(this.filter_project != "None" && this.filter_produce != 'None'){                                       
                    orderObj = this.getSalesReport.orders.filter((o) => {
                        return parseInt(o.produce_trader_id) === parseInt(this.filter_produce)
                    })                    
                    orderObj.forEach((o) => {
                        containerr = table.filter((s) => {
                            return parseInt(o.id) === parseInt(s.bid_order_id)
                        })
                        containerr.forEach((c) => {
                            container.push(c)
                        })
                    })
                    containerr = table.filter((s) => {
                        return s.bid_order_id === null
                    })
                    console.log(containerr)
                    containerr.forEach((s) => {
                        inventoryObj = this.getSalesReport.produce_inventories.filter((i) => {
                            return parseInt(s.produce_inventory_id) === parseInt(i.id)
                        })
                        inventoryObj.forEach((i) => {
                            yieldObj = this.getSalesReport.produce_yields.filter((y) => {
                                return parseInt(i.produce_yield_id) === parseInt(y.id)
                            })
                            if(parseInt(yieldObj[0].produce_trader_id) === parseInt(this.filter_produce)){
                                container.push(s)
                            }
                        })                            
                    })   
                    container = container.filter((c) => {
                        return parseInt(this.filter_project) === parseInt(c.project_id)
                    })
                    table = container                    
                }
                else if(this.filter_project == "None" && this.filter_produce != 'None'){
                    orderObj = this.getSalesReport.orders.filter((o) => {
                        return parseInt(o.produce_trader_id) === parseInt(this.filter_produce)
                    })
                    orderObj.forEach((o) => {
                        containerr = table.filter((s) => {
                            return parseInt(o.id) === parseInt(s.bid_order_id)
                        })
                        containerr.forEach((c) => {
                            container.push(c)
                        })
                    })
                    containerr = table.filter((s) => {
                        return s.bid_order_id === null
                    })
                    console.log(containerr)
                    containerr.forEach((s) => {
                        inventoryObj = this.getSalesReport.produce_inventories.filter((i) => {
                            return parseInt(s.produce_inventory_id) === parseInt(i.id)
                        })
                        inventoryObj.forEach((i) => {
                            yieldObj = this.getSalesReport.produce_yields.filter((y) => {
                                return parseInt(i.produce_yield_id) === parseInt(y.id)
                            })
                            if(parseInt(yieldObj[0].produce_trader_id) === parseInt(this.filter_produce)){
                                container.push(s)
                            }
                        })                            
                    })
                    table = container
                }
                else if(this.filter_project != "None" && this.filter_produce == 'None'){
                    table = table.filter((s) => {
                        return parseInt(this.filter_project) === parseInt(s.project_id)
                    })
                }
                return table
            }
            return table
        },        
      }
  }
  </script>
  
  
  <style>
  
  </style>
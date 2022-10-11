<template>
  <div class="salesReportOwner">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Sales Report</h3>
    </div>
    <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">            
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
                          <th scope="col">Sales No.</th>
                          <th scope="col">Sales Type</th>
                          <th scope="col">Farm Originated</th>
                          <th scope="col">Produce Name</th>
                          <th scope="col">Qty</th>
                          <th scope="col">Price Sold</th>
                          <th scope="col">Total</th>                          
                          <th scope="col">Date Completed</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr v-for="(sale, index) in filteredTable" :key="index">
                        <td>{{ sale.id }}</td>
                        <td>{{ sale.sale_type }}</td>
                        <td>{{ getFarmName(sale) }}</td>
                        <td>{{ getProduceName(sale) }}</td>
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
import { format, add, sub } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "SalesReportOwner",
    created(){
        this.fetchSalesReportForOwner()
        .then(() => {
            if(this.getSalesReportForOwner.sales && this.getSalesReportForOwner.sales.length > 0){
                var sales = this.this.getSalesReportForOwner.sales.sort((a, b) => {
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
            filter_dateFrom: null,
            filter_dateTo: null,
        }
    },
    watch: {
        filter_dateFrom(newVal){
            if(newVal >= this.filter_dateTo){
                this.filter_dateFrom = format(sub(new Date(newVal), {
                    days: 1
                }), 'yyyy-MM-dd')
            }
        },
        filter_dateTo(newVal){
            if(newVal <= this.filter_dateFrom){
                this.filter_dateTo = format(add(new Date(newVal), {
                    days: 1
                }), 'yyyy-MM-dd')
            }
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchSalesReportForOwner']),
        getFarmName(sale){
            var projectObj = this.getSalesReportForOwner.projects.filter((p) => {
                return parseInt(sale.project_id) === parseInt(p.id)
            })
            var contractObj = this.getSalesReportForOwner.contracts.filter((c) => {
                return parseInt(projectObj[0].contract_id) === parseInt(c.id)
            })
            return contractObj[0].farm_name
        },
        getProduceName(sale){
            var orderObj = this.getSalesReportForOwner.orders.filter((o) => {
                return parseInt(sale.bid_order_id) === parseInt(o.id)
            })
            var prodTraderObj = this.getSalesReportForOwner.produce_traders.filter((p) => {
                return parseInt(orderObj[0].produce_trader_id) === parseInt(p.id)
            })
            var prodObj = this.getSalesReportForOwner.produces.filter((pp) => {
                return parseInt(prodTraderObj[0].produce_id) === parseInt(pp.id)
            })
            var arr = prodTraderObj[0].prod_name.split('(Class')
            if(arr.indexOf('(Class') != -1){
                arr.splice(arr.indexOf('(Class'), 0, prodObj[0].prod_type)
                return arr.join(' ')
            }
            else{
                return prodTraderObj[0].prod_name + ' ' + prodObj[0].prod_type
            }
        }
    },
    computed: {
        ...mapGetters(['getSalesReportForOwner']),
        filteredTable(){
            var table = [];
            if(this.getSalesReportForOwner.sales && this.getSalesReportForOwner.sales.length > 0){
                table = this.getSalesReportForOwner.sales.filter((s) => {
                    return format(new Date(s.created_at), 'yyyy-MM-dd') >= this.filter_dateFrom
                    && format(new Date(s.created_at), 'yyyy-MM-dd') <= this.filter_dateTo
                })
            }           
            return table
        }
    }
}
</script>

<style>

</style>
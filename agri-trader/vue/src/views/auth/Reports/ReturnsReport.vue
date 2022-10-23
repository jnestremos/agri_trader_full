<template>
  <div class="returnsReport">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Returns</h3>        
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:85%; height:65%" class="pb-5">
            <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Purchase Order Number</label>
                  <select class="form-select" @click="setOrder($event)">
                      <option value="None">None</option>
                      <option v-for="(number, index) in getPurchaseNumbers" :key="index" :value="number">{{ number }}</option>
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
                          <th scope="col">RR ID</th>
                          <th scope="col">Return Order Num</th>
                          <th scope="col">Purchase Order Num</th>
                          <th scope="col">Supplier Name</th>
                          <th scope="col">Supply Name</th>
                          <th scope="col">Supply Type</th>
                          <th scope="col">Supply For</th>
                          <th scope="col">Price</th>
                          <th scope="col">Qty Returned</th>
                          <th scope="col">Total</th>
                          <th scope="col">Remarks</th>
                          <th scope="col">Date Issued</th>
                      </tr>
                  </thead>
                  <tbody align="center">
                      <tr style="cursor:pointer;" v-for="(report, index) in filteredTable" :key="index">
                        <td>{{ report.report_num }}</td>
                        <td>{{ report.returnOrder_num }}</td>
                        <td>{{ report.purchaseOrder_num }}</td>
                        <td>{{ getSupplierName(report) }}</td>
                        <td>{{ getSupplyName(report) }}</td>                        
                        <td>{{ getSupplyType(report) }}</td>                        
                        <td>{{ getSupplyFor(report) }}</td>                        
                        <td>{{ getSupplyPrice(report) }}</td>
                        <td>{{ report.purchaseOrder_qtyDefect }}</td>
                        <td>{{ report.purchaseOrder_subTotal.toFixed(2) }}</td>
                        <td>{{ report.return_remark }}</td>
                        <td>{{ report.created_at.split('T')[0] }}</td>
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
    name:'ReturnsReport',
    created(){
        this.fetchPurchaseReturnsReport()
        .then(() => {
            if(this.getPurchaseReturnsReport.returns && this.getPurchaseReturnsReport.returns.length > 0){
                var returns = this.getPurchaseReturnsReport.returns.sort((a, b) => {
                    return new Date(a.created_at) - new Date(b.created_at)
                })
                this.filter_dateFrom = format(new Date(returns[0].created_at), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(returns[returns.length - 1].created_at), 'yyyy-MM-dd')
            }
            this.readyApp()
        })        
    },
    data(){
        return {
            filter_purchase: 'None',
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
    methods:{
        ...mapActions(['readyApp', 'fetchPurchaseReturnsReport']),
        setOrder(e){
            this.filter_purchase = e.target.value
        },
        // getReceivingReportID(report){
        //     var reportObj = this.getPurchaseReturnsReport.receiving_reports.filter((r) => {
        //         return parseInt(report.supply_id) === parseInt(r.supply_id)
        //         && report.returnOrder_num === r.purchaseOrder_num
        //     })
        //     return reportObj[0].id
        // },
        getSupplierName(report){
            var orderObj = this.getPurchaseReturnsReport.orders.filter((o) => {
                return report.purchaseOrder_num === o.purchaseOrder_num
            })
            var supplierObj = this.getPurchaseReturnsReport.suppliers.filter((s) => {
                return parseInt(orderObj[0].supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
        },
        getSupplyName(report){
            var orderObj = this.getPurchaseReturnsReport.orders.filter((o) => {
                return report.purchaseOrder_num === o.purchaseOrder_num
            })
            var supplyObj = this.getPurchaseReturnsReport.supplies.filter((s) => {
                return parseInt(orderObj[0].supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_name
        },
        getSupplyType(report){
            var orderObj = this.getPurchaseReturnsReport.orders.filter((o) => {
                return report.purchaseOrder_num === o.purchaseOrder_num
            })
            var supplyObj = this.getPurchaseReturnsReport.supplies.filter((s) => {
                return parseInt(orderObj[0].supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_type
        },
        getSupplyFor(report){
            var orderObj = this.getPurchaseReturnsReport.orders.filter((o) => {
                return report.purchaseOrder_num === o.purchaseOrder_num
            })
            var supplyObj = this.getPurchaseReturnsReport.supplies.filter((s) => {
                return parseInt(orderObj[0].supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_for
        },
        getSupplyPrice(report){
            var orderObj = this.getPurchaseReturnsReport.orders.filter((o) => {
                return report.purchaseOrder_num === o.purchaseOrder_num
            })
            var supplyObj = this.getPurchaseReturnsReport.supplies.filter((s) => {
                return parseInt(orderObj[0].supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_initialPrice
        },
    },
    computed:{
        ...mapGetters(['getPurchaseReturnsReport']),
        getPurchaseNumbers(){
            var numbers = new Set()
            var arr = []
            if(this.getPurchaseReturnsReport.returns && this.getPurchaseReturnsReport.returns.length > 0){
                this.getPurchaseReturnsReport.returns.forEach((r) => {
                    numbers.add(r.purchaseOrder_num)
                })
                numbers.forEach((n) => {
                    arr.push(n)
                })
            }
            return arr
        },
        filteredTable(){
            var table = [];
            if(this.getPurchaseReturnsReport.returns){
                if(this.filter_purchase != "None"){
                    table = this.getPurchaseReturnsReport.returns.filter((r) => {
                        return this.filter_purchase === r.purchaseOrder_num
                    })
                }
                else{
                    return this.getPurchaseReturnsReport.returns
                }
            }
            return table
        }
    }
}
</script>

<style>

</style>
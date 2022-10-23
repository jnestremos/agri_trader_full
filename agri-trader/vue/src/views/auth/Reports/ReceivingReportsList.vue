<template>
    <div class="ReceivingReportsList">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Receiving Reports</h3>        
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:100%; height:65%" class="pb-5">
          <div class="form-row mb-3 mt-2">
              <div class="col-lg-3 me-3">
                  <label class="form-label me-4 fw-bold">Purchase Order Number</label>
                  <select class="form-select" @click="setOrder($event)">
                      <option value="None">Select PO #</option>
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
          <!-- ; ; ; -->
          <div class="container-fluid m-0 p-0" :style="[filteredTable && filteredTable.length > 6 ? {'overflow-y': 'scroll'} : {}, {'width':'100%'}, {'height': '40vh'}]">
              <table id="supplySelect" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm;">
                  <thead align="center">
                      <tr>
                          <!-- <th scope="col">RR No.</th> -->
                          <th scope="col">Purchase Order No.</th>
                          <th scope="col">Supplier</th>
                          <th scope="col">Supply Name</th>
                          <th scope="col">Supply Type</th>
                          <th scope="col">Supply For</th>
                          <th scope="col">Unit</th>
                          <th scope="col">Price</th>
                          <th scope="col">Items Ordered</th>
                          <th scope="col">Stocked In</th>
                          <th scope="col">Defective</th>
                          <th scope="col">Remarks</th>                                            
                          <th scope="col">Date Created</th>                                            
                      </tr>
                  </thead>
                  <tbody align="center">
                    <tr v-for="(report, index) in filteredTable" :key="index">
                        <!-- <td>{{ report.id }}</td> -->
                        <td>{{ report.purchaseOrder_num }}</td>
                        <td>{{ getSupplierName(report) }}</td>
                        <td>{{ getSupplyName(report) }}</td>
                        <td>{{ getSupplyType(report) }}</td>
                        <td>{{ getSupplyFor(report) }}</td>
                        <td>{{ report.purchaseOrder_unit }}</td>
                        <td>{{ getInitialPrice(report) }}</td>
                        <td>{{ report.purchaseOrder_qtyGood + report.purchaseOrder_qtyDefect }}</td>
                        <td>{{ report.purchaseOrder_qtyGood }}</td>
                        <td>{{ report.purchaseOrder_qtyDefect }}</td>
                        <td>{{ report.report_remark }}</td>
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
import { add, format, sub } from 'date-fns';
import { mapActions, mapGetters } from 'vuex';
export default {
    name: "ReceivingReportsList",
      created() {
        this.fetchReceivingReport()
        .then(() => {
            if(this.getReceivingReport.receiving_reports && this.getReceivingReport.receiving_reports.length > 0){
                var reports = this.getReceivingReport.receiving_reports.sort((a, b) => {
                    return new Date(a.created_at) - new Date(b.created_at)
                })
                this.filter_dateFrom = format(new Date(reports[0].created_at), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(reports[reports.length - 1].created_at), 'yyyy-MM-dd')
            }
            this.readyApp()
        })          
      }, 
      data(){
        return {
            filter_order: 'None',
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
          ...mapActions(['readyApp', 'fetchReceivingReport']),
          setOrder(e){
            this.filter_order = e.target.value
          },
          getSupplierName(report){
            var supplyObj = this.getReceivingReport.supplies.filter((s) => {
                return parseInt(report.supply_id) === parseInt(s.id)
            })
            var supplierObj = this.getReceivingReport.suppliers.filter((s) => {
                return parseInt(supplyObj[0].supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
          },
          getSupplyName(report){
            var supplyObj = this.getReceivingReport.supplies.filter((s) => {
                return parseInt(report.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_name
          },
          getSupplyType(report){
            var supplyObj = this.getReceivingReport.supplies.filter((s) => {
                return parseInt(report.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_type
          },
          getSupplyFor(report){
            var supplyObj = this.getReceivingReport.supplies.filter((s) => {
                return parseInt(report.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_for
          },
          getInitialPrice(report){
            var supplyObj = this.getReceivingReport.supplies.filter((s) => {
                return parseInt(report.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_initialPrice
          },
      },
      computed: {
        ...mapGetters(['getReceivingReport']),
        filteredTable(){
            var table = []
            if(this.getReceivingReport.receiving_reports){
                table = this.getReceivingReport.receiving_reports.filter((r) => {
                    return format(new Date(r.created_at), 'yyyy-MM-dd') >= this.filter_dateFrom
                    && format(new Date(r.created_at), 'yyyy-MM-dd') <= this.filter_dateTo
                })
                if(this.filter_order != 'None'){
                    table = table.filter((r) => {
                        return this.filter_order === r.purchaseOrder_num
                    })
                }
                return table
            }
            return table
        },
        getPurchaseNumbers(){
            var numbers = new Set()
            var arr = [];
            if(this.getReceivingReport.receiving_reports){
                this.getReceivingReport.receiving_reports.forEach((r) => {
                    numbers.add(r.purchaseOrder_num)
                })
                numbers.forEach((n) => {
                    arr.push(n)
                })
            }
            return arr
        }
      }
  }
  </script>
  
  
  <style>
  
  </style>
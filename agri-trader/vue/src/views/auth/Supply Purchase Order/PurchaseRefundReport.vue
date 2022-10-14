<template>
    <div class="PurchaseRefundReport">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
          <h3>Supply Purchase Orders | Refunds</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
          <div style="width:85%; height:65%" class="pb-5">
              <div class="form-row mt-3">
                  <div class="col-lg-3 me-3">
                      <label for="stockInHitsory_supplierList" class="form-label me-4">Choose Supplier</label>
                      <select class="form-select" id="supplier_name" @change="setPurchaseNum($event)">
                          <option selected value="None">Select Purchase Order</option>
                          <option v-for="(num, index) in getOrderNumbers" :key="index" :value="num">{{ num }}</option>                        
                      </select>                    
                  </div>                  
              </div>
              <div class="mb-2 mt-4" style="width:100%; height:90%; clear:left;">
                  <table id="supplySelect" class="table table-striped table-bordered align-middle" :style="[filteredTable && filteredTable.length > 8 ? {'overflow-y':'scroll'} : {}, {'width': '100%'}]">
                      <thead align="center">
                          <tr>                              
                              <th scope="col">Purchase Order</th>                                                                               
                              <th scope="col">Qty Refunded</th>
                              <th scope="col">Total Refund Amount</th>                             
                              <th scope="col">Status</th>
                              <th scope="col">Date Issued</th>
                          </tr>
                      </thead>
                      <tbody align="center">
                          <tr v-for="(supply, index) in filteredTable" :key="index" @click="triggerModal(supply)">                   
                              <td>{{ supply.purchaseOrder_num }}</td>                                                                            
                              <td>{{ supply.purchaseOrder_qtyDefect }}</td>
                              <td>{{ supply.purchaseOrder_subTotal }}</td>                                            
                              <td>{{ supply.refundOrder_status }}</td>
                              <td>{{ supply.created_at.split('T')[0] }}</td>                          
                            <b-modal size="xl" :hide-footer="supply.refundOrder_status == 'Confirmed'" :id="`modal-${supply.purchaseOrder_num}`" :title="`Refund Details for ${supply.purchaseOrder_num}`">
                                <div class="w-100 h-100">                                    
                                    <div :style="[getItems(supply) && getItems(supply).length > 5 ? {'overflow-y':'scroll'} : {}, {'height':'30vh'}]">
                                        <table style="width:100%; background:lightgray">
                                            <thead>
                                                <tr>
                                                    <th>Supplier Name</th>
                                                    <th>Supply ID</th>
                                                    <th>Supply Name</th>
                                                    <th>Supply Type</th>
                                                    <th>Supply For</th>
                                                    <th>Qty Defect</th>
                                                    <th>Supply Unit</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(order, index) in getItems(supply)" :key="index">
                                                    <td>{{ getSupplierName(order) }}</td>
                                                    <td>{{ order.supply_id }}</td>
                                                    <td>{{ getSupplyName(order) }}</td>
                                                    <td>{{ getSupplyType(order)}}</td>
                                                    <td>{{ getSupplyFor(order) }}</td>
                                                    <td>{{ order.purchaseOrder_qtyDefect }}</td>
                                                    <td>{{ order.purchaseOrder_unit }}</td>
                                                    <td>{{ order.purchaseOrder_subTotal }}</td>
                                                </tr>
                                            </tbody>
                                        </table>                                    
                                    </div>                                    
                                    <div :style="[getSuppliers(supply) && getSuppliers(supply).length > 5 ? {'overflow-y': 'scroll'} : {}, {'height': '30vh'}]" class="mt-3">
                                        <table style="width:100%; background:lightgray">
                                            <thead>
                                                <tr>
                                                    <th>Supplier Name</th>
                                                    <th>Bank Name</th>
                                                    <th>E-Wallet Name</th>
                                                    <th>Acc Name</th>
                                                    <th>Acc Num</th>                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(supplier, index) in getSuppliers(supply)" :key="index">
                                                    <td>{{ supplier.supplier_name }}</td>
                                                    <td>{{ supplier.supplier_bankName }}</td>
                                                    <td>{{ supplier.supplier_otherName }}</td>
                                                    <td>{{ supplier.supplier_accName }}</td>
                                                    <td>{{ supplier.supplier_accNum }}</td>
                                                </tr>
                                            </tbody>
                                        </table>                                    
                                    </div>
                                    <h5 class="mt-3">{{ supply.refundOrder_status == 'Confirmed' ? `Confirmed on: ${supply.updated_at.split('T')[0]}` : '' }}</h5>
                                    <!-- <h5 class="mb-3">Supplier's Bank Name: {{ getBankName(supply) }}</h5>                                     -->
                                    <!-- <h5>Supplier's E-Wallet: {{ getBankName(supply) }}</h5> -->
                                    <!-- <h5 class="mb-3">Supplier's Account Name Holder: {{ getAccName(supply) }}</h5>
                                    <h5>Supplier's Account Number: {{ getAccNum(supply) }}</h5> -->
                                </div>
                                <template #modal-footer="{ ok, cancel }">
                                    <b-button variant="secondary" @click="cancel()">Cancel</b-button>
                                    <b-button variant="primary" @click="ok()">Confirm</b-button>
                                </template>
                            </b-modal>                                                            
                          </tr>
                      </tbody>
                  </table>
              </div>              
          </div>
      </div>
    </div>
    
  </template>
  
  <script>
  import { mapActions, mapGetters } from 'vuex'
  export default {
      name: "PurchaseRefundReport",
      created() {
          this.fetchSupplyRefunds()
          .then(() => {
              this.readyApp()
          })        
      },
      data(){
          return{
             filter_num: 'None',   
             purchaseOrder_num: null          
          }
      },  
      mounted(){
        this.$root.$on('bv::modal::hide', (bvEvent) => {
            if(bvEvent.trigger == 'ok'){
                this.updateSupplyRefund(this.purchaseOrder_num)
                .then(() => {
                    this.$toastr.s('Refund Confirmed Successfully!')
                    setTimeout(() => {
                        location.reload()
                    }, 5000)
                })
            }
            this.purchaseOrder_num = null
        })
      }, 
      methods: {
          ...mapActions(['readyApp', 'fetchSupplyRefunds', 'updateSupplyRefund']),
          triggerModal(supply){
            this.$bvModal.show(`modal-${supply.purchaseOrder_num}`)
            this.purchaseOrder_num = supply.purchaseOrder_num
          },
          setPurchaseNum(e){
            this.filter_num = e.target.value
          },
          getItems(supply){
            var refunds = this.getSupplyRefunds.purchaseOrder_refunds.filter((r) => {
                return supply.purchaseOrder_num === r.purchaseOrder_num
            })
            return refunds
          },
          getSuppliers(supply){
            var refundObj = this.getSupplyRefunds.purchaseOrder_refunds.filter((r) => {
                return supply.purchaseOrder_num === r.purchaseOrder_num
            })
            var suppliers = []
            refundObj.forEach((r) => {
                var supplyObj = this.getSupplyRefunds.supplies.filter((s) => {
                    return parseInt(r.supply_id) === parseInt(s.id)
                })
                var supplierObj = this.getSupplyRefunds.suppliers.filter((s) => {
                    return parseInt(supplyObj[0].supplier_id) === parseInt(s.id)
                })
                if(!suppliers.includes(supplierObj[0])){
                    suppliers.push(supplierObj[0])
                }
            })
            return suppliers
          },
          getSupplierName(supply){
            var supplyObj = this.getSupplyRefunds.supplies.filter((s) => {
                return parseInt(supply.supply_id) === parseInt(s.id)
            })
            var supplierObj = this.getSupplyRefunds.suppliers.filter((s) => {
                return parseInt(supplyObj[0].supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_name
          },
          getSupplyName(supply){
            var supplyObj = this.getSupplyRefunds.supplies.filter((s) => {
                return parseInt(supply.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_name
          },
          getSupplyType(supply){
            var supplyObj = this.getSupplyRefunds.supplies.filter((s) => {
                return parseInt(supply.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_type
          },
          getSupplyFor(supply){
            var supplyObj = this.getSupplyRefunds.supplies.filter((s) => {
                return parseInt(supply.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_for
          },
          getBankName(supply){
            var supplyObj = this.getSupplyRefunds.supplies.filter((s) => {
                return parseInt(supply.supply_id) === parseInt(s.id)
            })
            var supplierObj = this.getSupplyRefunds.suppliers.filter((s) => {
                return parseInt(supplyObj[0].supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_bankName              
          },
          getAccName(supply){
            var supplyObj = this.getSupplyRefunds.supplies.filter((s) => {
                return parseInt(supply.supply_id) === parseInt(s.id)
            })
            var supplierObj = this.getSupplyRefunds.suppliers.filter((s) => {
                return parseInt(supplyObj[0].supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_accName
          },
          getAccNum(supply){
            var supplyObj = this.getSupplyRefunds.supplies.filter((s) => {
                return parseInt(supply.supply_id) === parseInt(s.id)
            })
            var supplierObj = this.getSupplyRefunds.suppliers.filter((s) => {
                return parseInt(supplyObj[0].supplier_id) === parseInt(s.id)
            })
            return supplierObj[0].supplier_accNum
          },
          
      },
      computed: {
          ...mapGetters(['getSupplyRefunds']),
          filteredTable(){
              var table = []            
              if(this.filter_num != 'None'){
                  table = this.getSupplyRefunds.refunds_filtered.filter((s) => {
                      return parseInt(this.filter_num) === parseInt(s.purchaseOrder_num)
                  })                  
                  return table
              }
              else{
                  return this.getSupplyRefunds.refunds_filtered
              }
          },          
          getOrderNumbers(){
              var types = new Set()
              var arr = [];
              if(this.getSupplyRefunds.refunds_filtered){
                  this.getSupplyRefunds.refunds_filtered.forEach((s) => {
                      types.add(s.purchaseOrder_num)
                  })                
                  types.forEach((ss) => {
                      arr.push(ss)
                  })                
              }
              return arr
          }
      }
    }
  </script>
  
  <style scoped>
  table, th, td {    
      border-collapse: collapse;
      border-spacing: 0;
      
  }
  th, td{
      padding: 10px;
      text-align:center;
      border: 2px solid black;
  }
  </style>
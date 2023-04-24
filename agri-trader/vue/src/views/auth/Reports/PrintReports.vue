<template>
    <div class="PrintReport">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Project Information</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:95%; height:65%;" class="pb-5">
            <div class="form-row mt-2">
                <div class="col lg-2 mt-3">
                    <label class="h5">Project Name: Rebote Farm Project</label>
                </div>
                <div class="col lg-2 mt-3">
                    <label class="h5">Farm Owner: Mario Rebote</label>
                </div>
                <div class="col lg-2 mt-3">
                    <label class="h5">Trader: R. Rebote</label>
                </div>
            </div>
            <div class="form-row mt-1">
                <div class="col lg-2 mb-3 mt-3">
                    <label class="h5">Farm Produce: Cavendish Banana</label>
                </div>
                <div class="col lg-2 mb-3 mt-3">
                    <label class="h5">Project Completed: April 14, 2023</label>
                </div>
                <div class="col lg-2 mb-3 mt-3">
                    <label class="h5">Status: Completed Successfully</label>
                </div>
            </div>
            <hr class="first"/>
            <div class="row mt-2">
                <h4> Financial Report </h4>
                <hr class="2"/>
                <div class="col md-3 mt-2">
                    <label class="h5"> Total Sales: Php 45,000.00 </label>
                </div>
                <div class="col md-2">
                    <div class="col md-3 mt-2">
                        <label class="h5"> Bidding: Php 0.00</label>
                    </div>
                    <div class="col md-3 mt-2">
                        <label class="h5"> On-Hand Sales: Php 45,000.00 </label>
                    </div>
                </div>
                <hr>
                <div class="row mt-2">
                    <div class="col md-3 mt-2">
                        <label class="h5"> Total Expenditures: Php 35,000.00</label>
                    </div>
                    <div class="col md-3">
                        <div class="col md-3 mt-2">
                            <label class="h5"> Supplies: Php 25,000.00 </label>
                        </div>
                        <div class="col md-3 mt-2">
                            <label class="h5"> Other Expenditures: Php 10,000.00 </label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-row mt-5">
                    <div class="col md-5">
                        <label class="h5">Total Sales: Php 45,000.00</label>
                    </div>
                    <div class="col md-5">
                        <label class="h5">Total Expenditures: Php 35,000.00</label>
                    </div>
                    <div class="col md-5">
                        <label class="h5">Net Profit: Php 10,000.00</label>
                    </div>
                </div>
                <div class="form-row mt-5">
                    <div class="col">
                        <label class="h5">Farm Owner's Share: Php 5,000.00</label>
                    </div>
                    <div class="col">
                        <label class="h5">Trader's Share: Php 5,000.00</label>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <!-- <button @click="printPage">Print</button> -->
    </div>
  </template>

  <script>
  import { mapActions} from "vuex";

  export default {
    name:"PrintReports",
    created(){
        // this.fetchPrintReports()
        this.readyApp()
    },
    // computed: {
    //     ...mapGetters(['getPrintReports']),
    //     getTotalIncome() {
    //         var income = 0
    //         if(this.getPrintReports.accs && this.getPrintReports.accs.length > 0 || this.getPrintReports.sales && this.getPrintReports.sales.length > 0){
    //             var accs = []
    //             var sales = []
    //             this.getPrintReports.forEach((a) => {
    //                 accs.push(a.bid_order_acc_amount)
    //             })
    //             console.log(accs)
    //             this.getProfitSharingReport.sales.forEach((s) => {
    //                 accs.push(s.sale_total)
    //             })
    //             income = accs.reduce((a, b) => parseFloat(a) + parseFloat(b), income)
    //             income = sales.reduce((a, b) => parseFloat(a) + parseFloat(b), income)
    //         }
    //     return income
    //     },
    //     getTotalExpenses() {
    //         var expense = 0
    //         if(this.getPrintReports.profit_sharing && this.getPrintReports.profit_sharing.length > 0){
    //             var expense = []
    //             this.getPrintReports.profit_sharing.forEach((s) => {
    //                 expense.push(s.ar_totalExpense)
    //             })
    //             expense = expense.reduce((a, b) => parseFloat(a) + parseFloat(b), expense)
    //         }
    //         return expense
    //     },
    //     getTotalBalance(){
    //         return this.getIncome - this.getTotalExpenses
    //     }
    // },
    methods: {
      printPage() {
        window.print();

      },
      ...mapActions(['readyApp'])
    }
  };
  </script>

  <style>
  @media print {
    body {
      font-size: 12pt;
    }
  }
  hr.first {
    border: 1px solid black;
  }
  </style>







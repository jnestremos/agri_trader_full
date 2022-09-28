<template>
    <div class="PurchaseOrderStatus">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply Purchase Order | Status</h3>
        </div>
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
            <div style="width:85%; height:65%" class="pb-5">
                <form class="d-flex flex-column justify content between mt-2" style="height:20%">
                    <div class="form-row mb-2">
                        <div class="col-lg-2 me-2">
                            <label for="orderSummary_date" class="form-label me-4" >Date</label>
                            <input type="text" name="orderSummary_Date" id="" class="form-control" placeholder="09/05/2022">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_purchaseOrderNum" class="form-label me-4" >Purchase Order No.:</label>
                            <input type="text" name="orderSummary_purchaseOrderNum" id="" class="form-control" placeholder="PO-1234567">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_purchaseOrderStatus" class="form-label me-4" >Purchase Order Status</label>
                            <select class="form-select" @change="changeStatus($event)">
                                <option selected="Pending">Pending</option>
                                <option selected="Order Confirmed">Order Confirmed</option>
                                <option selected="For Delivery">For Delivery</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="font-weight-bold" style="font-size:110%">Supplier Name: Pacifica Agrivet</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="font-weight-bold" style="font-size:110%">Contact Person: Juan dela Cruz</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="font-weight-bold" style="font-size:110%">Contact Number: 09123456789</p>
                        </div>
                    </div>
                    <div class="mb-2" style="width:100% height:90%; clear:left;">
                        <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:100%;">
                            <thead align="center">
                                <tr>
                                    <th scope="col">Supply Type</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Price per unit</th>
                                    <th scope="col">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                <tr>
                                    <th>Fertilizer</th>
                                    <th>15</th>
                                    <th>Sack</th>
                                    <th>Php 500</th>
                                    <th>Php 7,500.00</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_totalAmount" class="form-label me-4" >Total Amount</label>
                            <input type="text" name="orderSummary_totalAmount" id="" class="form-control" placeholder="Php 7,500.00" disabled>
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="orderSummary_transactedBy" class="form-label me-4" >Transacted By</label>
                            <input type="text" name="orderSummary_transactedBy" id="" class="form-control" v-model="orderStatus.transactedBy">
                        </div>
                    </div>
                    <div class="btn-toolbar pt-4" role="toolbar">
                        <div class="btn-group me-3">
                            <router-link to="/supplyOrder/outstandingBalance"><b-button variant="success" style="width:200px; height:60px">Proceed to Payment</b-button></router-link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: "PurchaseOrderStatus",
    created() {
        this.readyApp()
    },
    data() {
        return {
            orderStatus: {
                transactedBy: '',
                changeStatus: '',
            }
        }
    },
    watch: {
        
    },
    methods: {
        changeStatus(e){
            this.orderStatus.changeStatus = e.target.value
        },
        ...mapActions(['readyApp'])
    },
}
</script>

<style>
 
</style>
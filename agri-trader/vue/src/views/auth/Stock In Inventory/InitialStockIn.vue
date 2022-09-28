<template>
  <div class="InitialStockIn">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply | Stock In from Supply Purchase Order</h3>
        </div>
        <div class="form-row mb-2">
            <div class="col-lg-2 me-2">
                <label for="orderSummary_date" class="form-label me-4" >Date</label>
                <input type="text" name="orderSummary_Date" id="" class="form-control" disabled v-model="dateToday" placeholder="09/05/2022">
            </div>
            <div class="col-lg-3 me-3">
                <label for="orderSummary_purchaseOrderNum" class="form-label me-4" >Purchase Order No.:</label>
                <input type="text" name="orderSummary_purchaseOrderNum" id="" disabled class="form-control" v-model="data.purchaseOrder_num" placeholder="PO-1234567">
            </div>
        </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
 name: "InitialStockIn",
 created() {
        this.readyApp()
    },
    methods: {
        ...mapActions(['readyApp'])
    }
}
</script>

<style>

</style>
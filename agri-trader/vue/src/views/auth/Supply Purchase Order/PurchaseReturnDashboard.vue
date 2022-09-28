<template>
    <div class="PurchaseReturnDashboard">
        <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply Purchase Order | Purchase Returns</h3>
            <router-link to="/supplyOrder/statusDashboard"><button class="btn btn-success text-right">Return to Order Status</button></router-link>
        </div>
            <div class="container-fluid w-100 d-flex" style="height:90%; position: relative;">
                <div class="w-100">
                    <div class="form-row mb-4">
                        <div class="col">
                            <div class="card mt-3 ms-3" style="width: 30%; height: 90%; background-color: #80B032; border-radius: 20px;">
                                <div class="card-body">
                                    <label class="card-title font-weight-bold">Yara Mila UNIK-13 Nitrogen-based Fertilizer</label> <!-- name of supply -->
                                    <label class="d-flex align-items-baseline">Purchase Order No. <p class="ms-3">RETURN TEST-0000001</p></label>
                                    <label class="d-flex align-items-baseline">Supplier: <p class="ms-3">Pacifica Agrivet</p></label>
                                    <label class="d-flex align-items-baseline">Supply Type: <p class="ms-3">Fertilizer</p></label>
                                    <label class="d-flex align-items-baseline">Balance: <p class="ms-3">3,750.00</p></label>
                                    <label class="d-flex align-items-baseline">Status: <p class="ms-3 font-weight-bold" style="color:red">Pending</p></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>      
            </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';
export default {
    name: "PurchaseReturnDashboard",
    created() {
        this.readyApp()
    },
    methods: {
        ...mapActions(['readyApp']),
    }
}
</script>

<style scoped>
/* .h5{
    flex: 1 1 auto;
} */
</style>
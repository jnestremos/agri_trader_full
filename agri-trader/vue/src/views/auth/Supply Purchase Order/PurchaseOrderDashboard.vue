<template>
    <div class="PurchaseOrderDashboard">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply Purchase Order | Status</h3>
        </div>
            <div class="container-fluid w-100 d-flex" style="height:90%; position: relative;">
                <div class="w-100">
                    <div class="form-row mb-4">
                        <dic class="col">
                            <div class="card mt-3 ms-3" style="width: 30%; height: 90%; background-color: #E0EDCA; border-radius: 20px;">
                                <div class="card-body">
                                    <label class="card-title font-weight-bold">Yara Mila UNIK-13 Nitrogen-based Fertilizer</label>
                                    <label class="d-flex align-items-baseline">Purchase Order No. <p class="ms-3">0000001</p></label>
                                    <label class="d-flex align-items-baseline">Supplier: <p class="ms-3">Pacifica Agrivet</p></label>
                                    <label class="d-flex align-items-baseline">Supply Type: <p class="ms-3">Fertilizer</p></label>
                                    <label class="d-flex align-items-baseline">Balance: <p class="ms-3">3,750.00</p></label>
                                    <label class="d-flex align-items-baseline">Status: <p class="ms-3 font-weight-bold" style="color:red">Pending</p></label>
                                </div>
                        </div>
                        </dic>
                    </div>
                </div>      
            </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';
export default {
    name: "PurchaseOrderDashboard",
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
<template>
    <div class="StockInHistory">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supply | Stock In History</h3>
        </div>
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
            <div style="width:85%; height:65%" class="pb-5">
                <form class="d-flex flex-column justify-content-between mt-2 ms-2" style="height:20%">
                    <div class="row">
                        <div class="col-lg-3 me-3">
                            <label for="stockIn_date" class="form-label me-4" >Date</label>
                            <input type="date" name="stockIn_Date" id="" class="form-control" placeholder="09/05/2022">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 me-3">
                            <label for="stockIn_date" class="form-label me-4" >Date from</label>
                            <input type="date" name="StockInHistory_DateFrom" id="" class="form-control" v-model="stockInHistory.datefrom">
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="stockIn_date" class="form-label me-4" >Date to</label>
                            <input type="date" name="StockInHistory_DateTo" id="" class="form-control" v-model="stockInHistory.dateto">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</template>

<script>
import { mapActions } from 'vuex';
export default {
    name: "StockInHistory",
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
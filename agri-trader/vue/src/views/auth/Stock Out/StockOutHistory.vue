<template>
    <div class="StockOutHistory">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Inventory | Stock Out History</h3>
        </div>
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
            <div style="width:85%; height:65%" class="pb-5">
                <form class="d-flex flex-column justify-content-between mt-2 ms-2" style="height:20%">
                    <div class="form-row mt-3">
                        <div class="col-lg-3 me-3">
                            <label for="stockInHitsory_supplierList" class="form-label me-4">Choose Supplier</label>
                            <select class="form-select" id="supplier_name" @change="setSupplier($event)">
                                <option selected value="None">Select Supplier</option>
                                <option v-for="(supplier, index) in getStockInHistory.suppliers" :key="index" :value="supplier.id">{{ supplier.supplier_name }}</option> 
                            </select>                    
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="stockInHistory_supplyType" class="form-label me-4" >Choose Supply Type</label>
                            <select class="form-select" :disabled="filter_supplier == 'None'" id="supply_type" @change="setSupplyType($event)">
                                <option selected value="None">Select Supply Type</option>
                                <option v-for="(type, index) in getTypes" :key="index" :value="type">{{ type }}</option>
                            </select>
                        </div>
                        <div class="col-lg-3 me-3">
                            <label for="stockInHistory_SupplyFor" class="form-label me-4">Choose Supply For</label>
                            <select class="form-select" id="supply_for" :disabled="filter_supplier == 'None'" @change="setSupplyFor($event)">
                                <option selected value="None">Select Supply For</option> 
                                <option v-for="(produce, index) in getSupplyForFiltered" :key="index" :value="produce">{{ produce }}</option>                 
                            </select>
                        </div>
                    </div>
                    <div class="mb-2 mt-3" style="width:100%; height:90%; clear:left;">
                        <table id="supplySelect" class="table table-striped table-bordered align-middle" style="width:100%;">
                            <thead align="center">
                                <tr>
                                    <th scope="col">Project No.</th>
                                    <th scope="col">Supplier Name</th>
                                    <th scope="col">Supply Name</th>
                                    <th scope="col">Supply Type</th>
                                    <th scope="col">Supply For</th>                                    
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Date Released</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                <tr v-for="(stock, index) in filteredTable" :key="index">
                                    <td>{{ stock.purchaseOrder_num }}</td>
                                    <td>{{ getSupplierName(stock) }}</td>
                                    <td>{{ getSupplyName(stock) }}</td>
                                    <td>{{ getSupplyType(stock) }}</td>
                                    <td>{{ getSupplyFor(stock) }}</td>
                                    <td>{{ stock.supply_qty }}</td>
                                    <td>{{ stock.supply_unit }}</td>
                                    <td>{{ stock.created_at.split('T')[0] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
   
}
</script>

<style>

</style>
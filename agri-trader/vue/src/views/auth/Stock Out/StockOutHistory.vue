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
                            <label for="stockInHitsory_supplierList" class="form-label me-4">Choose Project</label>
                            <select class="form-select" id="project_id" :disabled="!(getProjectIDS > 0)" @change="setProject($event)">
                                <option selected value="None">Select Project</option>
                                <option v-for="(id, index) in getProjectIDS" :key="index" :value="id">{{ 'Project # ' + id }}</option> 
                            </select>  
                        </div>
                    </div>
                    <div class="form-row mt-3">
                        <div class="col-lg-3 me-3">
                            <label for="stockInHitsory_supplierList" class="form-label me-4">Choose Supplier</label>
                            <select class="form-select" id="supplier_name" :disabled="filter_num == 'None'" @change="setSupplier($event)">
                                <option selected value="None">Select Supplier</option>
                                <option v-for="(supplier, index) in getStockOutHistory.suppliers" :key="index" :value="supplier.id">{{ supplier.supplier_name }}</option> 
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
                        <table id="supplySelect" class="table table-striped table-bordered align-middle" :style="[filteredTable && filteredTable.length > 8 ? {'overflow-y':'scroll'} : {}, {'width': '100%'}]">
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
    name: 'StockOutHistory',
    created(){
        this.fetchStockOutHistory()
        .then(() => {
            this.readyApp()
        })
        
    },
    data(){
        return {
            filter_supplier: 'None',
            filter_type: 'None',
            filter_for: 'None',
            filter_num: 'None'
        }
    },
    watch: {
        'filter_supplier'(newVal){
            if(newVal == "None"){
                this.filter_type = "None"
                this.filter_for = 'None'
                var supply_type = document.getElementById('supply_type')
                var supply_for = document.getElementById('supply_for')
                supply_type.value = 'None'
                supply_for.value = 'None' 
            }
        },
        'filter_num'(newVal){
            if(newVal == "None"){
                this.filter_supplier = "None"
                this.filter_type = "None"
                this.filter_for = 'None'
                var supply_type = document.getElementById('supply_type')
                var supply_for = document.getElementById('supply_for')
                var supplier_name = document.getElementById('supplier_name')
                supply_type.value = 'None'
                supply_for.value = 'None' 
                supplier_name.value = 'None' 
            }
        },
    },
    methods: {
        ...mapActions(['readyApp', 'fetchStockOutHistory']),
        setProject(e){
            this.filter_num = e.target.value
        },
        setSupplier(e){
            this.filter_supplier = e.target.value
        },
        setSupplyType(e){
            this.filter_type = e.target.value
        },
        setSupplyFor(e){
            this.filter_for = e.target.value
        },
        getSupplierName(stock){
            var supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                return parseInt(stock.supply_id) === parseInt(s.id)
            })
            var supplierObj = this.getStockOutHistory.suppliers.filter((ss) => {
                return parseInt(supplyObj[0].supplier_id) === parseInt(ss.id)
            })
            return supplierObj[0].supplier_name
        },
        getSupplyName(stock){
            var supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                return parseInt(stock.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_name
        },
        getSupplyType(stock){
            var supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                return parseInt(stock.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_type
        },
        getSupplyFor(stock){
            var supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                return parseInt(stock.supply_id) === parseInt(s.id)
            })
            return supplyObj[0].supply_for
        }
    },
    computed: {
        ...mapGetters(['getStockOutHistory']),
        filteredTable(){
            var table = [];
            var supplyObj = null;
            var container = null
            var containerr = null
            if(this.filter_num != 'None'){
                table = this.getStockOutHistory.stockOut_history.filter((s) => {
                    return parseInt(this.filter_num) === parseInt(s.project_id)
                })
                if(this.filter_supplier != 'None'){
                    table = table.filter((s) => {
                        return parseInt(this.filter_supplier) === parseInt(s.supplier_id)
                    })
                    if(this.filter_type != 'None' && this.filter_for != 'None'){                        
                        supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                            return (this.filter_type) === (s.supply_type) && (this.filter_for) === (s.supply_for)
                        })
                        container = []
                        supplyObj.forEach((s) => {
                            containerr = table.filter((ss) => {
                                return parseInt(s.id) === parseInt(ss.supply_id)
                            })
                            containerr.forEach((sss) => {
                                container.push(sss)
                            })
                        })
                        table = container
                    }
                    else if(this.filter_type != 'None'){
                        supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                            return (this.filter_type) === (s.supply_type)
                        })
                        container = []
                        supplyObj.forEach((s) => {
                            containerr = table.filter((ss) => {
                                return parseInt(s.id) === parseInt(ss.supply_id)
                            })
                            containerr.forEach((sss) => {
                                container.push(sss)
                            })
                        })
                        table = container
                    }
                    else if(this.filter_for != 'None'){
                        supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                            return (this.filter_for) === (s.supply_for)
                        })
                        container = []
                        supplyObj.forEach((s) => {
                            containerr = table.filter((ss) => {
                                return parseInt(s.id) === parseInt(ss.supply_id)
                            })
                            containerr.forEach((sss) => {
                                container.push(sss)
                            })
                        })
                        table = container
                    }
                }
                else{
                    return table
                }
            }
            else{
                return this.getStockOutHistory.stockOut_history
            }
            if(this.filter_supplier != 'None'){
                supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                    return parseInt(this.filter_supplier) === parseInt(s.supplier_id)
                })
                supplyObj.forEach((s) => {
                    container = this.getStockOutHistory.stockOut_history.filter((ss) => {
                        return parseInt(s.id) === parseInt(ss.supply_id)
                    })
                    container.forEach((c) => {
                        table.push(c)
                    })
                })                
                if(this.filter_type != 'None' && this.filter_for != 'None'){
                    console.log(1)
                    supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                        return (this.filter_type) === (s.supply_type)
                    })
                    supplyObj.forEach((s) => {
                        table = table.filter((ss) => {
                            return parseInt(s.id) === parseInt(ss.supply_id)
                        })                       
                    })
                    supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                        return (this.filter_for) === (s.supply_for)
                    })
                    supplyObj.forEach((s) => {
                        table = table.filter((ss) => {
                            return parseInt(s.id) === parseInt(ss.supply_id)
                        })                        
                    })                    
                }
                else if(this.filter_type != 'None'){
                    console.log(2)
                    supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                        return (this.filter_type) === (s.supply_type)
                    })
                    supplyObj.forEach((s) => {
                        table = table.filter((ss) => {
                            return parseInt(s.id) === parseInt(ss.supply_id)
                        })                        
                    })
                }
                else if(this.filter_for != 'None'){
                    console.log(3)
                    supplyObj = this.getStockOutHistory.supplies.filter((s) => {
                        return (this.filter_for) === (s.supply_for)
                    })
                    supplyObj.forEach((s) => {
                        table = table.filter((ss) => {
                            return parseInt(s.id) === parseInt(ss.supply_id)
                        })                        
                    })                    
                }  
                return table 
            }
            else{
                return this.getStockOutHistory.stockOut_history
            }
        },
        getTypes(){
            var types = new Set()
            var arr = [];
            if(this.getStockOutHistory.supplies){
                this.getStockOutHistory.supplies.forEach((s) => {
                    types.add(s.supply_type)
                })                
                types.forEach((ss) => {
                    arr.push(ss)
                })                
            }
            return arr
        },
        getProjectIDS(){
            var types = new Set()
            var arr = [];
            if(this.getStockOutHistory.stockOut_history){
                this.getStockOutHistory.stockOut_history.forEach((s) => {
                    types.add(s.project_id)
                })                
                types.forEach((ss) => {
                    arr.push(ss)
                })                
            }
            return arr
        },
        getSupplyForFiltered(){
            var types = new Set()
            var arr = [];
            if(this.getStockOutHistory.supplies){
                this.getStockOutHistory.supplies.forEach((s) => {
                    types.add(s.supply_for)
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

<style>

</style>
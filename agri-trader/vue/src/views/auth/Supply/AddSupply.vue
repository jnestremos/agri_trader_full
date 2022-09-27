<template>
    <div class="addSupply">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Supply Profile</h3>        
    </div>          
    <div class="container-fluid d-flex" style="height:90%; position:relative; z-index: 9;">
        <div style="width:60%; height:65%;" class="pb-5">
            <form class="d-flex flex-column justify-content-between mt-2" style="height:80%;" @submit.prevent="">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <label for="supplier_name" class="form-label me-4" style="font-size: large;">Supply Name</label>
                </div>
                <div class="form-row">
                    <div class="col-lg-6 mb-3">
                        <input type="text" name="supply_name" id="" class="form-control" v-model="supply.supply_name" placeholder="ex: Yara UNIK-16">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-4 mb-3 me-3">
                        <label for="supplyOrder_SupplyType" class="form-label me-3" style="font-size: large;">Supply Type</label>
                            <select class="form-select" @change="setSupplyType($event)">
                                <option selected disabled value="">Select Supply Type</option>
                                <option value="Fertilizer">Fertilizer</option>
                                <option value="Insecticide">Insecticide</option>
                                <option value="Fungicide">Fungicide</option>
                                <option value="Herbicide">Herbicide</option>
                            </select>
                    </div>
                    <div class="col-lg-4 mb-3 me-3">
                        <label for="supplyOrder_SupplyType" class="form-label me-3" style="font-size: large;">Supply For</label>
                            <select v-if="getProducesForAddSupply" class="form-select" @change="setSupplyFor($event)">
                                <option selected disabled value="">Select Supply For</option>
                                <option v-for="(type, index) in getProducesForAddSupply" :key="index" :value="type.prod_type">{{ type.prod_type }}</option>                                
                            </select>
                    </div>
                </div>
                <div class="d-flex justify-content-start align-items-center w-100">
                    <label for="supplier_contactPerson" class="form-label me-4" style="font-size: large;">Description</label>
                </div>
                <div class="form-row">
                    <div class = "col-lg-6 mb-3">
                        <input type="text" name="supply_description" id="" class="form-control" v-model="supply.supply_description" placeholder="ex: Magnesium-Based Fertilizer good for high yield of mangoes (Small Size)">
                    </div>
                </div>
                <!-- <div class="d-flex justify-content-between align-items-center w-100">
                    <label for="Date" class="form-label me-4" style="font-size: large;">Date Added</label>
                </div> -->
                <!-- <div class="form-row">
                    <div class="col-lg-4 mb-3">
                        <input type="date" name="supply_date" id="" class="form-control" v-model="supply.date">
                    </div>
                </div> -->
                <div class="form-row">
                    <div class="col-lg-3 mb-4">
                        <label for="Initial Price" class="form-label me-4" style="font-size: large;">Initial Price</label>
                        <input type="number" name="supply_initialPrice" id="" class="form-control" v-model="supply.supply_initialPrice" @change="validateAmount($event)" @blur="validateAmount($event)" style="width:100px;">
                    </div>
                    <div class="col-lg-2 mb-4 me-4">
                            <label for="supplyOrder_unitType" class="form-label me-4" >Unit Type</label>
                            <select class="form-select" @change="setUnit($event)">
                                <option selected value="Sack">Sack</option>
                                <option value="Bottle">Bottle</option>
                                <option value="Sachet">Sachet</option>
                                <option value="Pack">Pack</option>
                            </select>
                        </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-4 mb-2 me-3">
                        <select v-if="getSuppliersForAddSupply" class="form-select" @change="setSupplier($event)">
                            <option selected disabled value="">Select Supplier</option>
                            <option v-for="(supplier, index) in getSuppliersForAddSupply" :key="index" :value="supplier.id">{{ supplier.supplier_name }}</option>                            
                    </select>
                    </div>
                </div>
                <div class="btn-toolbar mt-3" role="toolbar">
                    <div class="btn-group me-3">
                        <button class="btn btn-success" style="width:100px" @click="sendSupply()">Add</button>
                    </div>                   
                </div>          
            </form>
        </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import { format } from 'date-fns'
export default {
    name: "AddSupplier",
    created() {
        this.formForAddSupply()
        .then(() => {
            this.readyApp()
        })
        .catch((err) => {
            console.error(err)
            this.$router.push({ name: 'AddSupplier' })
        })
        
    },
    data() {
        return {
            supply: {
                supply_name: null,
                supply_type: '',
                supply_for: null,
                supply_description: null,
                date: format(new Date(), 'yyyy-MM-dd'),
                supply_initialPrice: 0.00,
                supply_unit: 'Sack',
                supplier_id: null
            }
        }
    },
    methods: {
        ...mapActions(['readyApp', 'formForAddSupply', 'addSupply']),
        setSupplyType(e){
            this.supply.supply_type = e.target.value
        },
        setSupplyFor(e){
            this.supply.supply_for = e.target.value
        },
        setUnit(e){
            this.supply.supply_unit = e.target.value
        },
        setSupplier(e){
            this.supply.supplier_id = e.target.value
        },
        validateAmount(e){
            if(parseInt(e.target.value) < 0){
                this.supply.supply_initialPrice = Math.abs(e.target.value)
            }
        },
        sendSupply(){
            this.addSupply(this.supply)
            .then(() => {
                this.$toastr.s('Supply successfully added!')
                setTimeout(() => {
                    // this.$router.push({ name: 'SupplyList' })
                }, 5000)
            })
            .catch((err) => {
                console.log(err)
                var errors = err.response.data.errors
                for(var error in errors){
                    this.$toastr.e(errors[error][0])
                }                
            })
        }
    },
    computed: {
        ...mapGetters(['getSuppliersForAddSupply', 'getProducesForAddSupply'])
    }
    
}
</script>
<style>

</style>
<template>
    <div class="addSupply">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Supply Profile</h3>        
    </div>          
    <div class="container-fluid d-flex" style="height:90%; position:relative; z-index: 9;">
        <div style="width:55%; height:65%;" class="pb-5">
            <form class="d-flex flex-column justify-content-between mt-2" style="height:80%;" @submit.prevent="">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <label for="supplier_name" class="form-label me-4" style="font-size: large;">Supply Name</label>
                </div>
                <div class="form-row">
                    <div class="col-lg-6 mb-3">
                        <input type="text" name="supply_name" id="" class="form-control" v-model="supply.supply_Name">
                    </div>
                </div>
                <div class="d-flex justify-content-start align-items-center w-100">
                    <label for="supplier_contactPerson" class="form-label me-4" style="font-size: large;">Description</label>
                </div>
                <div class="form-row">
                    <div class = "col-lg-6 mb-3">
                        <input type="text" name="supply_description" id="" class="form-control" v-model="supply.supply_description">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center w-100">
                    <label for="Date" class="form-label me-4" style="font-size: large;">Date Added</label>
                </div>
                <div class="form-row">
                    <div class="col-lg-4 mb-3">
                        <input type="date" name="supply_date" id="" class="form-control" v-model="supply.date">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-3 mb-4">
                        <label for="Reorder Level" class="form-label me-4" style="font-size: large;">Reorder Level</label>
                        <input type="text" name="supply_reorderLevel" id="" class="form-control" v-model="supply.supply_reorderLevel">
                    </div>
                    <div class="col-lg-3 mb-4">
                        <label for="Initial Price" class="form-label me-4" style="font-size: large;">Initial Price</label>
                        <input type="text" name="supply_initialPrice" id="" class="form-control" v-model="supply.supply_initialPrice">
                    </div>
                </div>
                <div class="form-row mb-4 me-4">
                    <select class="form-select">
                        <option selected>Select Supplier</option>
                        <option value="1">Pacifica Agrivet</option>
                        <option value="2">McJenski Agro</option>
                        <option value="3">Inson Agro Farm Supply</option>
                        <option value="4">Jeems Agrivet Supply</option>
                    </select>
                </div>
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group me-3">
                        <button class="btn btn-success" style="width:100px">Add</button>
                    </div>
                    <div class="btn-group me-3">
                        <button class="btn btn-secondary" style="width:100px">Edit</button>
                    </div>
                </div>          
            </form>
        </div>
    </div>
  </div>
</template>

<script>
export default {
    name: "AddSupplier",
    data() {
        return {
            supply: {
                supply_Name: '',
                supply_description: '',
                date:'',
                supply_reorderLevel:'',
                supply_initialPrice:'',
            }
        }
    }
    
}
</script>
<style>

</style>
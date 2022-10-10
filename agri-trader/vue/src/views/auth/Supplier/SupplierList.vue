<template>
  <div class="SupplierList">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supplier's List</h3>
        </div>
            <div class="container-fluid" style="height:90%;">
                <div style="width:90%; height:85%;" class="pb-5">
                  <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
                    <table id="supplierList" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm">
                      <thead align="center">
                        <tr>
                          <th scope="col">Name</th>
                          <th scope="col">Address</th>
                          <th scope="col">Contact Person</th>
                          <th scope="col">Contact Number</th>
                          <th scope="col">Supplies linked to Supplier</th>
                        </tr>
                      </thead>
                      <tbody align="center">
                        <tr class="supplier" v-for="(supplier, index) in getSuppliers" :key="index" @click="goToEditPage(supplier.id)">
                          <th>{{ supplier.supplier_name }}</th>
                          <th>{{ getAddress(supplier) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>  
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group me-3">
                        <router-link to="/supplier/add"> <button class="btn btn-success" style="width:120px; height: 50px;">Add</button> </router-link>
                    </div>
                    <div class="btn-group me-3">
                      <router-link to="/supply/add"> <button class="btn btn-success" style="width:200px; height: 50px;"> Add Supply to Supplier </button> </router-link>
                    </div> 
                </div>      
            </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
export default {
  name: "SupplierList",
  created() {
    this.fetchSuppliers()
    .then(() => {
      this.readyApp()
    })  
  },  
  methods: {
    ...mapActions(['readyApp', 'fetchSuppliers']),
    goToEditPage(id){      
      this.$router.push({ path: `/supplier/${id}` });
    },
    getAddress(supplier){
      var addressObj = this.getSupplierAddresses.filter((a) => {
        return parseInt(supplier.id) === parseInt(a.supplier_id)
      })
      return `${addressObj[0].address_street}, ${addressObj[0].address_town}, ${addressObj[0].address_province}`
    }
    
  },
  computed:{
    ...mapGetters(['getSuppliers', 'getSupplierAddresses'])
  }
}
</script>

<style scoped>

.supplier{
  cursor: pointer;
}

</style>
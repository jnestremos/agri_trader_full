<template>
  <div class="SupplierList">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Supplier's List</h3>
        </div>
            <div class="container-fluid" style="height:90%;">
                <div style="width:90%; height:85%;" class="pb-5">
                  <div class="form-row">
                    <div class="col-lg-3 mb-3 mt-2">
                      <label class="fw-bold" style="font-size: large;">Search Supplier</label>
                      <input type="text" class="form-control" v-model="filter_supplier">
                    </div>                    
                  </div>
                  <div class="container-fluid m-0 p-0" style="width:100%; height: 40vh;">
                    <table id="supplierList" class="table table-striped table-bordered align-middle" width="100%" style="margin: 0; border-collapse: collapse; border-spacing: 0cm">
                      <thead align="center">
                        <tr>
                          <th scope="col">Name</th>
                          <th scope="col">Address</th>
                          <th scope="col">Contact Person</th>
                          <th scope="col">Contact Number</th>
                          <th scope="col">Number of Supplies</th>
                        </tr>
                      </thead>
                      <tbody align="center">
                        <tr class="supplier" v-for="(supplier, index) in filteredTable" :key="index" @click="goToEditPage(supplier.id)">
                          <th>{{ supplier.supplier_name }}</th>
                          <th>{{ getAddress(supplier) }}</th>
                          <th>{{ getContactPerson(supplier) }}</th>
                          <th>{{ getContactNumber(supplier) }}</th>
                          <th>{{ getNumSupplies(supplier) }}</th>
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
  data(){
    return {
      filter_supplier: ''
    }
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
    },
    getContactPerson(supplier){
      var personObj = this.getSupplierContactPeople.filter((p) => {
        return parseInt(supplier.id) === parseInt(p.supplier_id)
      })
      return personObj[0].contact_firstName + ' ' + personObj[0].contact_lastName
    },
    getContactNumber(supplier){
      var contactObj = this.getSupplierContacts.filter((c) => {
        return parseInt(supplier.id) === parseInt(c.supplier_id)
      })
      if(contactObj[0].supplier_telNumber){
        return contactObj[0].supplier_phoneNumber + ' / ' + contactObj[0].supplier_telNumber
      }
      else{
        return contactObj[0].supplier_phoneNumber
      }
    },
    getNumSupplies(supplier){
      var supplies = this.getSupplies.filter((s) => {
        return parseInt(supplier.id) === parseInt(s.supplier_id)
      })
      return supplies.length
    },
    
  },
  watch: {
    filter_supplier(newVal){
      console.log(newVal)
    }
  },
  computed:{
    ...mapGetters([
      'getSuppliers', 
      'getSupplierAddresses',
      'getSupplierContacts',
      'getSupplierContactPeople',
      'getSupplies',
    ]),
    filteredTable(){
      var table = []
      if(this.filter_supplier.trim() != ''){   
        const reg = new RegExp(`^${this.filter_supplier.trim()}`, 'gm')  
        table = this.getSuppliers.filter((s) => {          
          return reg.test(s.supplier_name.toLowerCase())
        })
      }
      else{
        return this.getSuppliers
      }
      return table
    }
  }
}
</script>

<style scoped>

.supplier{
  cursor: pointer;
}

</style>
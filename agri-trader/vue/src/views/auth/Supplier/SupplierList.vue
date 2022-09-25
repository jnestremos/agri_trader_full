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
                        </tr>
                      </thead>
                      <tbody align="center">
                        <tr class="supplier" v-for="(supply, index) in suppliers" :key="index" @click="goToEditPage(supply.id)">
                          <th>{{ supply.name }}</th>
                          <th>{{ supply.address }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>  
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group me-3">
                        <router-link to="/supplier/add"> <button class="btn btn-success" style="width:150px; height: 50px;">Add</button> </router-link>
                    </div>
                  </div>      
            </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
export default {
  name: "SupplierList",
  created() {
        this.readyApp()
    },
  data() {
    return {
      suppliers: [
        {
          id: 1,
          name: 'Pacifica Agrivet',
          address: 'Sto. Tomas, Davao del Norte; Davao City',
        },
        {
          id: 2,
          name: 'McJenski Agri Supply',
          address: 'Sto. Tomas, Davao del Norte',
        },
        {
          id: 3,
          name: 'Kaumahan sa Sto. Tomas',
          address: 'Sto. Tomas, Davao del Norte',
        },
      ]
    }
  },
  methods: {
    goToEditPage(id){      
      this.$router.push({ path: `/supplier/${id}` });
    },
    ...mapActions(['readyApp'])
  }
}
</script>

<style scoped>

.supplier{
  cursor: pointer;
}

</style>
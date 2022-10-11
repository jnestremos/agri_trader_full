<template>
  <div class="addSupplier">
    <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
        <h3>Supplier Details</h3>        
    </div>          
    <div class="container-fluid d-flex" style="height:90%; position:relative; z-index: 9;">
        <div style="width:90%; height:65%;" class="pb-5">
            <form class="d-flex flex-column justify-content-between mt-2" style="height:80%;" @submit.prevent="">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <label for="supplier_name" class="form-label me-4 fw-bold" style="font-size: large;">Company Name</label>
                </div>
                <div class="form-row">
                    <div class="col-lg-7 mb-3">
                        <input type="text" name="company_name" id="" class="form-control" v-model="supplier.supplier_name">
                    </div>
                </div>
                <div class="d-flex justify-content-start align-items-center w-100" style="height:50%">
                    <label for="supplier_contactPerson" class="form-label me-4 fw-bold" style="font-size: large;">Contact Person</label>
                </div>
                <div class="form-row">
                    <div class="col-lg-2 mb-3">
                        <label for="contact_firstName" class="form-label me-4" style="font-size:small">First Name</label>
                        <input type="text" name="supplier_contactFirstName" id="" class="form-control form-control-sm" v-model="supplier.contact_firstName">
                    </div>
                    <div class="col-lg-2 mb-3">
                        <label for="contact_middleName" class="form-label me-4" style="font-size:small">Middle Name</label>
                        <input type="text" name="supplier_contactMiddleName" id="" class="form-control form-control-sm" v-model="supplier.contact_middleName">
                    </div>
                    <div class="col-lg-2 mb-3">
                        <label for="contact_lastName" class="form-label me-4" style="font-size:small">Last Name</label>
                        <input type="text" name="supplier_contactLastName" id="" class="form-control form-control-sm" v-model="supplier.contact_lastName">
                    </div>
                    <div class="col-lg-1 mb-3">
                        <label for="contact_lastName" class="form-label me-4" style="font-size:small">Suffix</label>
                        <select name="" class="form-select form-select-sm" id="" style="width:100px;" @change="setSuffix($event)">
                            <option selected value="">None</option>
                            <option value="Jr.">Jr.</option>
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                        </select>
                    </div>
                    <div class="col-lg-2 mb-3">
                        <label for="contact_lastName" class="form-label me-4" style="font-size:small">Position</label>
                        <input type="text" name="supplier_contactSuffix" id="" class="form-control form-control-sm" v-model="supplier.contact_position">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center w-100">
                    <label for="contact_information" class="form-label me-4 fw-bold" style="font-size: large;">Address</label>
                </div>
                <div class="form-row">
                    <div class="col-lg-3 mb-3">
                        <input type="number" name="address_zipCode" id="" class="form-control" placeholder="Zip Code" v-model="supplier.address_zipcode">
                    </div>
                    <div class="col-lg-3 mb-3">
                        <input type="text" name="address_street" id="" class="form-control" placeholder="Street" v-model="supplier.address_street">
                    </div>
                    <div class="col-lg-3 mb-3">
                        <input type="text" name="address_town" id="" class="form-control" placeholder="Municipality / City" v-model="supplier.address_town">
                    </div>
                    <div class="col-lg-3 mb-3">
                        <input type="text" name="address_province" id="" class="form-control" placeholder="Province" v-model="supplier.address_province">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center w-100">
                    <label for="contact_information" class="form-label me-4 fw-bold" style="font-size: large;">Contact Information</label>
                </div>
                <div class="form-row">
                    <div class="col-lg-3 mb-3">
                        <label for="Phone Number" class="form-label me-4" style="font-size: smaller;">Phone Number</label>
                        <input type="tel" name="supplier_contactNumber" id="" class="form-control" v-model="supplier.supplier_phoneNumber">
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="Telephone Number" class="form-label me-4" style="font-size: smaller;">Telephone Number</label>
                        <input type="tel" name="supplier_contactTelephone" id="" class="form-control" v-model="supplier.supplier_telNumber">
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="Email Address" class="form-label me-4" style="font-size: smaller;">Email Address</label>
                        <input type="email" name="supplier_email" id="" class="form-control" v-model="supplier.supplier_email">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center w-100">
                    <label for="contact_information" class="form-label me-4 fw-bold" style="font-size: large;">Payment Details</label>
                </div>
                <div class="form-row">
                    <div class="col-lg-3 mb-3">
                        <label for="Phone Number" class="form-label me-4" style="font-size: smaller;">Bank Name</label>
                        <input type="text" name="supplier_contactNumber" id="" class="form-control" v-model="supplier.supplier_bankName">
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="Telephone Number" class="form-label me-4" style="font-size: smaller;">E-Wallet Used</label>
                        <input type="text" name="supplier_contactTelephone" id="" class="form-control" v-model="supplier.supplier_otherName">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-3 mb-3">
                        <label for="Email Address" class="form-label me-4" style="font-size: smaller;">Account Name</label>
                        <input type="text" name="supplier_email" id="" class="form-control" v-model="supplier.supplier_accName">
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="Email Address" class="form-label me-4" style="font-size: smaller;">Account Number</label>
                        <input type="text" name="supplier_email" id="" class="form-control" v-model="supplier.supplier_accNum">
                    </div>
                </div>                 
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group me-3">
                        <button class="btn btn-success" style="width:100px" @click="editSupplier($event)">Edit</button>
                    </div>                    
                </div>      
            </form>
        </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'SupplierDetails',
    created(){
      this.fetchSupplier(this.$route.params.id)
      .then(() => {
        this.supplier.supplier_name = this.getSupplier.supplier.supplier_name
        this.supplier.supplier_bankName = this.getSupplier.supplier.supplier_bankName
        this.supplier.supplier_accName = this.getSupplier.supplier.supplier_accName
        this.supplier.supplier_accNum = this.getSupplier.supplier.supplier_accNum
        this.supplier.supplier_otherName = this.getSupplier.supplier.supplier_otherName
        this.supplier.contact_firstName = this.getSupplier.supplier_contact_person.contact_firstName
        this.supplier.contact_middleName = this.getSupplier.supplier_contact_person.contact_middleName
        this.supplier.contact_lastName = this.getSupplier.supplier_contact_person.contact_lastName
        this.supplier.contact_suffix = this.getSupplier.supplier_contact_person.contact_suffix
        this.supplier.contact_position = this.getSupplier.supplier_contact_person.contact_position
        this.supplier.address_zipcode = this.getSupplier.supplier_address.address_zipcode
        this.supplier.address_street = this.getSupplier.supplier_address.address_street
        this.supplier.address_town = this.getSupplier.supplier_address.address_town
        this.supplier.address_province = this.getSupplier.supplier_address.address_province
        this.supplier.supplier_phoneNumber = this.getSupplier.supplier_contact.supplier_phoneNumber
        this.supplier.supplier_telNumber = this.getSupplier.supplier_contact.supplier_telNumber
        this.supplier.supplier_email = this.getSupplier.supplier_contact.supplier_email
        this.readyApp()
      })
      .catch((err) => {
        console.error(err)
        this.$router.push({ name: 'SupplierList' })
      })
    },
    data() {
        return {
            supplier: {
                id: this.$route.params.id,
                supplier_name: '',
                supplier_bankName: '',
                supplier_accName: '',
                supplier_accNum: '',
                supplier_otherName: '',
                contact_firstName: '',
                contact_middleName: '',
                contact_lastName: '',
                contact_suffix: 'None',
                contact_position: '',
                address_zipcode: '',
                address_street: '',
                address_town: '',
                address_province: '',
                supplier_phoneNumber: '',
                supplier_telNumber: '',
                supplier_email: '',
            }
        }
    },
    methods: {
      ...mapActions(['readyApp', 'fetchSupplier', 'updateSupplier']),
      setSuffix(e){
        this.supplier.contact_suffix = e.target.value
      },
      editSupplier(e){
        this.updateSupplier(this.supplier)
        .then(() => {
            e.target.disabled = true
          this.$toastr.s('Supplier Updated Successfully!')
          setTimeout(() => {
            this.$router.push({ name: 'SupplierList' })
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
      ...mapGetters(['getSupplier'])
    }
}
</script>

<style>

</style>
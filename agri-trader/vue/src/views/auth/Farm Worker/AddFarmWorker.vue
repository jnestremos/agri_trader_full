<template>
    <div class="AddFarmWorker">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Farm Worker Profile</h3>        
        </div> 
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index: 9;">
            <div style="width:90%; height:80%" class="pb-5">
                <form class="d-flex flex-column justify-content-between mt-2" style="height:20%;" @submit.prevent="">
                    <div class="d-flex justify-content-start align-items-center w-100">
                        <label for="farmWorker_farmWorkerName" class="form-label me-4" style="font-size: large;">Name</label>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_firstName" class="form-label me-4 font-weight-bold" style="font-size:medium">First Name</label>
                            <input type="text" name="farmWorker_farmWorkerFirstName" id="" class="form-control form-control-sm" v-model="farmWorker.worker_firstName">
                        </div>
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_middleName" class="form-label me-4 font-weight-bold" style="font-size:medium">Middle Name</label>
                            <input type="text" name="farmWorker_farmWorkerMiddleName" id="" class="form-control form-control-sm" v-model="farmWorker.worker_middleName">
                        </div>
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_lastName" class="form-label me-4 font-weight-bold" style="font-size:medium">Last Name</label>
                            <input type="text" name="farmWorker_farmWorkerLastName" id="" class="form-control form-control-sm" v-model="farmWorker.worker_lastName">
                        </div>
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_lastName" class="form-label me-4 font-weight-bold" style="font-size:medium">Suffix</label>
                            <input type="text" name="farmWorker_farmWorkerSuffix" id="" class="form-control form-control-sm" v-model="farmWorker.worker_suffix">
                        </div>
                    </div>
                    <div class="d-flex justify-content-start align-items-center w-100">
                        <label for="farmWorker_farmWorkerName" class="form-label me-4" style="font-size: large;">Address</label>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-lg-2 mb-1">
                            <label for="address_zipCode" class="form-label me-4 font-weight-bold" style="font-size:medium">Zip Code</label>
                            <input type="text" name="address_zipCode" id="" class="form-control form-control-sm" v-model="farmWorker.workerAdd_zipCode">
                        </div>
                        <div class="col-lg-2 mb-1">
                            <label for="address_street" class="form-label me-4 font-weight-bold" style="font-size:medium">Street</label>
                            <input type="text" name="address_street" id="" class="form-control form-control-sm" v-model="farmWorker.workerAdd_street">
                        </div>
                        <div class="col-lg-2 mb-1">
                            <label for="address_barangay" class="form-label me-4 font-weight-bold" style="font-size:medium">Barangay</label>
                            <input type="text" name="address_barangay" id="" class="form-control form-control-sm" v-model="farmWorker.workerAdd_barangay">
                        </div>
                        <div class="col-lg-2 mb-1">
                            <label for="address_town" class="form-label me-4 font-weight-bold" style="font-size:medium">Town</label>
                            <input type="text" name="address_town" id="" class="form-control form-control-sm" v-model="farmWorker.workerAdd_town">
                        </div>
                        <div class="col-lg-2 mb-1">
                            <label for="address_province" class="form-label me-4 font-weight-bold" style="font-size:medium">Province</label>
                            <input type="text" name="address_province" id="" class="form-control form-control-sm" v-model="farmWorker.workerAdd_province">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <label for="contact_information" class="form-label me-4" style="font-size: large;">Contact Number</label>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-3 mb-1">
                            <input type="text" name="worker_contactNum" id="" class="form-control form-control-sm" v-model="farmWorker.worker_contactNum">
                        </div>
                    </div>
                    <div class="form-row mt-2">
                        <div class="col-lg-2 mb-1">
                            <label for="farmWorker_gender" class="form-label me-4 font-weight-bold">Gender</label>
                            <select class="form-select form-control-sm" @change="setGender($event)">
                                <option value="None">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-1">
                            <label for="farmWorker_maritalStatus" class="form-label me-4 font-weight-bold">Marital Status</label>
                            <select class="form-select form-control-sm" @change="setMaritalStatus($event)">
                                <option value="None">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-1">
                            <label for="farmWorker_birthday" class="form-label me-4 font-weight-bold">Birthday</label>
                            <input type="date" name="worker_birthday" id="" class="form-control form-control-sm" v-model="farmWorker.worker_birthdate">
                        </div>
                        <div class="col-sm-2">
                            <label for="farmWorker_age" class="form-label me-4 font-weight-bold">Age</label>
                            <input type="number" name="worker_birthday" id="" class="form-control form-control-sm" style="width:90px" v-model="farmWorker.worker_age">
                        </div>
                    </div>
                    <div class="row mt-2">
                            <label for="contact_number" class="form-label me-4" style="font-size: large;">Default Salary Payment Option</label>
                    </div>
                    <div class="form-row mb-3 me-3">
                        <div class="col-md-2">
                            <input class="form-check" type="checkbox" ref="cash" value="Cash" :checked="farmWorker.salaryPaymentMethod_cash = 'Cash'" @change="setSalaryPaymentMethod($event)"/>Cash
                        </div>
                        <div class="col-md-2">
                            <input class="form-check" type="checkbox" ref="bank" value="Bank" :checked="farmWorker.salaryPaymentMethod_bank = 'Card'" @change="setSalaryPaymentMethod($event)"/>Card/Wire Transfer
                        </div>
                    </div>
                    <div class="form-row" v-if="farmWorker.salaryPaymentMethod == 'Bank'">
                        <div class="col-lg-2 mb-1">
                            <label for="orderSummary_transactedBy" class="form-label me-4 mt-3" >Banking Institution</label>
                            <select class="form-select" @change="setBank($event)">
                                <option selected value="None">Select Bank</option>
                                <option value="BDO">BDO Unibank</option>
                                <option value="BPI">BPI</option>
                                <option value="LBP">LandBank</option>
                            </select>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label for="orderSummary_transactedBy" class="form-label me-4 mt-3" >Account Name</label>
                            <input type="text" name="orderSummary_transactedBy" id="" class="form-control" v-model="farmWorker.salaryPaymentMethod_accName">
                        </div>
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_accNum" class="form-label me-4 mt-3" >Account Number</label>
                            <input type="text" name="farmWorker_accNum" id="" class="form-control" v-model="farmWorker.salaryPaymentMethod_accNum">
                        </div>
                    </div>
                    <div class="btn-toolbar mt-2" role="toolbar">
                        <div class="btn-group me-3">
                            <button class="btn btn-success" style="width:100px">Add</button>
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
    name: "AddFarmWorker",
    created() {
        this.readyApp()
    },
    data() {
        return {
            farmWorker: {

                worker_firstName: '',
                worker_middleName: '',
                worker_lastName: '',
                worker_suffix: '',
                worker_age: '',
                worker_contactNum: '',
                worker_gender: '',
                worker_maritalStatus: '',
                workerAdd_zipCode: '',
                workerAdd_street: '',
                workerAdd_barangay: '',
                workerAdd_town: '',
                workerAdd_province: '',
                salaryPaymentMethod: null,
                salaryPaymentMethod_bankName: '',
                salaryPaymentMethod_accNum: '',
                salaryPaymentMethod_accName: '',
                
            }

        }
        
    },
    methods: {
        ...mapActions(['readyApp']),
        setSalaryPaymentMethod(e){
            if(e.target.checked){
                this.farmWorker.salaryPaymentMethod = e.target.value
                    
            }   
            else{
                this.farmWorker.salaryPaymentMethod = null
            }      
        },
        setBank(e){
            this.farmWorker.salaryPaymentMethod_bankName = e.target.value
        },
        setMaritalStatus(e){
            this.farmWorker.worker_maritalStatus = e.target.value
        },
        setGender(e){
            this.farmWorker.worker_gender = e.target.value
        }
    },
    watch: {
       'farmWorker.salaryPaymentMethod'(newVal){
        if(newVal == 'Cash'){
            this.$refs.bank.checked = false
            this.farmWorker.salaryPaymentMethod_accNum = null
            this.farmWorker.salaryPaymentMethod_accName = null
            this.farmWorker.salaryPaymentMethod_bankName = null
        }
        else if(newVal == 'Bank'){
            this.$refs.cash.checked = false
        }
       }
    }
}
</script>

<style>

</style>
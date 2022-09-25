<template>
    <div class="AddFarmWorker">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Farm Worker Profile</h3>        
        </div> 
        <div class="container-fluid d-flex" style="height:90%; position: relative; z-index: 9;">
            <div style="width:85%; height:65%" class="pb-5">
                <form class="d-flex flex-column justify-content-between mt-2" style="height:20%;" @submit.prevent="">
                    <div class="d-flex justify-content-start align-items-center w-100">
                        <label for="farmWorker_farmWorkerName" class="form-label me-4" style="font-size: large;">Name</label>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_firstName" class="form-label me-4" style="font-size:small">First Name</label>
                            <input type="text" name="farmWorker_farmWorkerFirstName" id="" class="form-control form-control-sm" v-model="farmWorker.farmWorker_firstName">
                        </div>
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_middleName" class="form-label me-4" style="font-size:small">Middle Name</label>
                            <input type="text" name="farmWorker_farmWorkerMiddleName" id="" class="form-control form-control-sm" v-model="farmWorker.farmWorker_middleName">
                        </div>
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_lastName" class="form-label me-4" style="font-size:small">Last Name</label>
                            <input type="text" name="farmWorker_farmWorkerLastName" id="" class="form-control form-control-sm" v-model="farmWorker.farmWorker_lastName">
                        </div>
                        <div class="col-lg-2 mb-2">
                            <label for="farmWorker_lastName" class="form-label me-4" style="font-size:small">Suffix</label>
                            <input type="text" name="farmWorker_farmWorkerSuffix" id="" class="form-control form-control-sm" v-model="farmWorker.farmWorker_suffix">
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
                companyName: '',
                farmWorker_firstName: '',
                farmWorker_middleName: '',
                farmWorker_lastName: '',
                farmWorker_suffix: '',
            }

        }
        
    },
    methods: {
        ...mapActions(['readyApp'])
    }
}
</script>

<style>

</style>
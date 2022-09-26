<template>
    <div class="incomeSummary">
        <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
            <h3>Income Summary</h3>
        </div>       
    </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: 'IncomeSummary',
    created(){
        this.readyApp()
    },
    methods :{
        ...mapActions(['readyApp'])
    }
}
</script>

<style>

</style>
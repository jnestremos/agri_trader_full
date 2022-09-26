<template>
    <div class="incomeSummary">
        this is the income Summary page
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
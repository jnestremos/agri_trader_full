<template>
  <div class="bidOrderHistory">
    this is the bid order history page
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: 'BidOrderHistory',
    created(){
        this.readyApp()
    },
    methods:{
        ...mapActions(['readyApp'])
    }
}
</script>

<style>

</style>
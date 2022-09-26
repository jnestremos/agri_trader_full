<template>
  <div class="distributorListOfBids">
    this is the distributor list of bids
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'DistributorListOfBids',
  created(){
    this.readyApp()
  },
  methods: {
    ...mapActions(['readyApp'])
  }
}
</script>

<style>

</style>
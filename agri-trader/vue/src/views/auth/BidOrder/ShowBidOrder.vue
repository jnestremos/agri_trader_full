<template>
  <div class="showBidOrder">
    this is the show bid order # {{ $route.params.id }}
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: 'ShowBidOrder',
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
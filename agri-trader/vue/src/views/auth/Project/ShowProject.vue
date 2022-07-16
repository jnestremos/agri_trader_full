<template>
  <div class="showProject">
    this is the show project page
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: "ShowProject",
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
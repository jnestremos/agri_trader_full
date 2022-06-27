<template>
  <div class="showProduce">
    this is the show produce page # {{ this.$route.params.id }}
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "ShowProduce",
    created(){
        this.readyApp()
        .then(() => {
            this.fetchProduce(this.$route.params.id)               
         })         
    },
    computed: {
        ...mapGetters(['getProduceDetails'])
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProduce'])
    }
}
</script>

<style>

</style>
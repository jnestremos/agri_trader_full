<template>
  <div class="addProduce">
    this is the add produce page
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: 'AddProduce',
    created(){
        this.readyApp()
    },
    data(){
        return {
            error: null
        }
    },
    methods: {
        ...mapActions(['readyApp', 'addProduce']),
        addProduceToTrader(){
            this.addProduce()
            .then(() => {
                this.$router.push({name: 'AllProduces'})
            })
            .catch((err) => {
                console.log(err.response.data.errors)
                this.error = err.response.data.errors
            })
        }
    }
}
</script>

<style>

</style>
<template>
  <div class="distributorLayout">
    <div class="container-fluid pt-2 d-flex justify-content-end" style="height: 5vh;">
        <div class="d-flex justify-content-between align-items-baseline">
            <h5>Help |</h5>
            <h5>&nbsp; Join Us |</h5>
            <form action="" @submit.prevent="logoutUser()">
                <button type="submit" style="background:transparent; border:0">
                    <h5>Logout</h5>
                </button>
            </form>            
        </div>
    </div>
    <div class="container-fluid d-flex justify-content-end" style="background:lightgreen; height: 6vh;">
        <div class="d-flex pe-5" style="width: 60%;">
            <ul class="d-flex justify-content-between align-items-center w-100 h-100" style="list-style:none">
                <li>Home</li>
                <li>About</li>
                <li>Produces</li>
                <li>Traders</li>
                <li>Contact</li>
                <li><router-link to="/bid_order/history">Orders</router-link></li>
            </ul>
        </div>
    </div>
    <div class="container-fluid p-0 m-0" style="width:100vw; height:87vh;">
        <router-view></router-view>
    </div>    
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name: "DistributorLayout",
    methods: {
        ...mapActions(['logout']),
        logoutUser(){            
            this.logout()
            .then(() => {
                this.$router.push({name: 'LoginTrader'})
            })
            .catch((err) => {
                console.log(err)
            })
        }        
    }
}
</script>

<style>

</style>
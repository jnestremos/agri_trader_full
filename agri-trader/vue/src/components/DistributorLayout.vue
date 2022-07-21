<template>
  <div class="distributorLayout">
    <div class="container-fluid pt-2 d-flex justify-content-end" style="height: 5vh;">
        <div class="d-flex justify-content-between">
            <h5>Help |</h5>
            <h5>&nbsp; Join Us |</h5>
            <h5>&nbsp;Sign-in</h5>
        </div>
    </div>
    <div class="container-fluid d-flex justify-content-end" style="background:lightgreen; height: 8vh;">
        <div class="d-flex pe-5" style="width: 60%;">
            <ul class="d-flex justify-content-between align-items-center w-100 h-100" style="list-style:none">
                <li>Home</li>
                <li>About</li>
                <li>Produces</li>
                <li>Traders</li>
                <li>Contact</li>
            </ul>
        </div>
    </div>
    <div class="container-fluid p-4" style="width:100vw; height:87vh;">
        <router-view></router-view>
    </div>    
  </div>
</template>

<script>
export default {
    name: "DistributorLayout"
}
</script>

<style>

</style>
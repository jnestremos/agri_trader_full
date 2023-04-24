<template>
    <div class="PrintPreview">
      <div class="container-fluid d-flex justify-content-end" style="background:#E0EDCA; height: 6vh;">
      </div>
      <div class="container-fluid p-0 m-0" style="width:100vw; height:87vh;">
          <router-view></router-view>
      </div>
    </div>
  </template>

  <script>
  import { mapActions } from 'vuex'
  import auth from '../store/modules/Auth/auth'
  export default {
      name: "PrintPreview",
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
          },
          getID(){
              return auth.state.user.id
          }
      },
  }
  </script>

  <style>

  </style>

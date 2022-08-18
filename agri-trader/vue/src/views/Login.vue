<template>
  <div class="login"> 
    <div class="container-fluid">
      <div class="row">
        <div class="col-6 text-black vh-100" style="background-color:green">
          Image Logo
        </div>
        <div class="col-6 text-black vh-100">
          <div class="d-flex justify-content-end align-items-center w-100" style="height:5%">
            <router-link to="/login/trader" v-if="$route.name == 'LoginDistributor'">as trader</router-link>
            <router-link to="/login/distributor" v-else-if="$route.name == 'LoginTrader'">as distributor</router-link>         
          </div>
          <div class="d-flex flex-column justify-content-center align-items-center w-100" style="height:95%">
            <div class="d-flex flex-column justify-content-center align-items-center w-50">
              <h1><i>Great to See You Again!</i></h1>              
            </div>
            <div class="d-flex pt-5 justify-content-center h-50 w-50">
              <form action="" class="d-flex flex-column align-items-center" @submit.prevent="loginUser()">
                <input type="email" class="form-control" style="width:400px; background-color:#DBDCD8;" placeholder="Email" v-model="data.email">
                <br>   
                <input type="password" class="form-control mb-2" placeholder="Password" style="background-color:#DBDCD8; width:400px;" v-model="data.password">
                <p>Don't have an account? 
                  <router-link to="/register/trader" v-if="$route.name == 'LoginTrader'">Sign-up as Trader</router-link>
                  <router-link to="/register/distributor" v-if="$route.name == 'LoginDistributor'">Sign-up as Distributor</router-link>
                </p>
                <br>
                <input type="submit" value="Login" style="background-color:#598216; width:300px; color:white; 
                height:50px; border-color:transparent;">
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>        
  </div>
</template>

<script>
// @ is an alias to /src
import { mapActions } from 'vuex'
import auth from '../store/modules/Auth/auth'
export default {
  name: 'Login',
  data(){
    return {
      data:{
        email: '',
        password: '',     
      },
      errors: null                     
    }
  },
  methods:{
    ...mapActions(['login', 'logout']),
    loginUser(){
      this.login(this.data)        
      .then(() => {
        if(auth.state.user.role == 'trader' && this.$route.name == 'LoginTrader'){
          this.$router.push({name: 'Dashboard'})
        }
        else if(auth.state.user.role == 'distributor' && this.$route.name == 'LoginDistributor'){
          this.$router.push({name: 'Catalog'})
        } 
        else{
          this.logout()
          this.$toastr.e('Unauthorized access!')
        }       
      })       
      .catch((err) => {
        this.errors = err.response.data.errors
        for(var error in this.errors){
            this.$toastr.e(this.errors[error][0])
        } 
      })  
    }
  }
  
}
</script>

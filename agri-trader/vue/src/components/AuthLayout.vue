<template>
  <div id="authLayout" class="d-flex">   
    <div>
        <b-nav vertical class="vh-100 justify-content-between align-items-center py-5" style="background-color:green; width:8vw">
        <b-nav-item class="d-flex justify-content-center">
            <font-awesome-icon icon="fa-regular fa-user" style="font-size:40px; color:white;"/>
          </b-nav-item>
        <div style="width:100%; color:black;">          
          <b-nav-item @mouseenter="moduleLeave()"><router-link style="color:black" to="/dashboard">Home</router-link></b-nav-item>
          <b-nav-item @mouseenter="showModules()" @mouseleave="hideModules()" class="w-100"><p style="color:black; margin:0">Modules</p></b-nav-item>
          <b-nav-item @mouseenter="moduleLeave()"><p style="color:black; margin:0">Settings</p></b-nav-item>
        </div>
        <b-nav-text class="d-flex justify-content-center">
          <form action="" @submit.prevent="logoutUser()">
            <button type="submit" style="border:0; background-color:transparent">
              <font-awesome-icon icon="fa-solid fa-arrow-right-from-bracket" style="font-size:40px; transform:rotate(-180deg); color:white;"/>
            </button>
          </form>          
        </b-nav-text>                    
      </b-nav>
    </div>
    <div style="width:300px; height:100vh; position: fixed; left:8%; z-index:-1;" class="d-flex align-items-center" id="moduless">
      <div style="background-color:green; width:100%; height:40%; opacity:0; display:none; transition:0.5s" id="modules" @mouseenter="moduleEnter()" @mouseleave="moduleLeave()">
        <ul class="d-flex flex-column justify-content-around h-100 text-left" style="list-style:none">
          <li><router-link style="color:black" to="/projects">Projects</router-link></li>
          <li><router-link style="color:black" to="/produces">Produces</router-link></li>
          <li><router-link style="color:black" to="/farms">Farms and Owners</router-link></li>
          <li><router-link style="color:black" to="/bid/orders">Bid Orders</router-link></li>
          <li><router-link style="color:black" :to="`/messages/${getID}/trader`">Chat</router-link></li>
          <li>Delivery</li>
          <li>Sales Income Report</li>
          <li>Profit Sharing Report</li>
        </ul>
      </div>
    </div>
      <router-view style="width:92vw; height:100vh;"></router-view>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {    
    name: 'AuthLayout',  
    data(){
      return{
        show:false
      }      
    },  
    computed: {
      ...mapGetters(['getID'])
    }, 
    methods:{
      ...mapActions(['logout']),
      showModules(){     
        var modules = document.getElementById('modules')               
        var moduless = document.getElementById('moduless')  
        moduless.style.zIndex = '9999999999999'               
        modules.style.opacity = '1'  
        modules.style.display = 'block'      
        this.show = true               
      },
      hideModules(){           
        var modules = document.getElementById('modules')
        var moduless = document.getElementById('moduless')  
        if(!this.show){
          modules.style.opacity = '0'       
          modules.style.display = 'none'                 
          moduless.style.zIndex = '-1'
        }                  
      },  
      moduleEnter(){
        var modules = document.getElementById('modules')
        var moduless = document.getElementById('moduless')  
        this.show = true
        if(modules.style.opacity == '1'){
          modules.style.opacity = '1' 
          modules.style.display = 'block' 
          moduless.style.zIndex = '99'
        }                        
      },
      moduleLeave(){
        var modules = document.getElementById('modules')
        var moduless = document.getElementById('moduless')
        this.show = false        
        modules.style.opacity = '0'           
        modules.style.display = 'none'  
        moduless.style.zIndex = '-1'        
      },
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

<style scoped>
b-nav-item{
  color:black !important;
}
</style>
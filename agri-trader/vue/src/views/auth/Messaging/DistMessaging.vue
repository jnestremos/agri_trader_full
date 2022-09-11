<template>
  <div class="distMessaging h-100 w-100">
    <div class="container-fluid w-100 d-flex pe-5 pt-3 justify-content-between align-items-center" style="height:10%;">
        <h3>Chats</h3>
    </div>           
    <div class="container-fluid w-100 p-3 m-0" style="height:90%; background:#D8E9BE">
      <div class="w-100 h-100 d-flex" style="border:1px solid black">
        <div class="h-100" style="border-right:1px solid black; width:25%; overflow-y: scroll;">
          <h4 :class="selected_id != id ? 'ps-3 d-flex align-items-center trader' : 'ps-3 d-flex align-items-center trader selected'" style="height:7%;" @click="setSelectedId(id)" v-for="(id, index) in getFilteredNameList" :key="index">{{ getName(id) }}</h4>
        </div>
        <div class="h-100" style="width:75%;">
          <div id="chat" class="" style="width:100%; height:90%; overflow-y: scroll;">
            <div style="width:100%; clear: both;" :class="index != 0 ? 'mt-2 p-3' : 'mt-5 p-3'" v-for="(message, index) in getSelectedMsgs" :key="index">
              <div :ref="index == getSelectedMsgs.length - 1 ? `last${selected_id}` : ''" :class="message.message_sentBy == 'distributor' ? 'p-3 text-center float-right sender' : 'p-3 text-center float-left recipient'" v-b-tooltip.hover :title="getDate(message)">{{ message.message_body }}</div>
            </div>                        
          </div>
          <div class="w-100 px-2 d-flex justify-content-center align-items-center" style="height:10%;">
            <input type="text" name="" id="" class="form-control me-2" v-model="data.message_body">
            <button class="btn btn-success" @click="sendMsg()">Send</button>
          </div>
        </div>
      </div>
    </div>   
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "DistMessaging",
    created(){
      console.log(new Date().toString())
      this.readMessages(this.$route.params.id)
      .then(() => {        
        if(!this.selected_id){
          this.selected_id = this.getFilteredNameList[0]                                          
          this.data.trader_id = this.selected_id                             
        }                  
        this.readyApp()
        .then(() => {          
          var chat = document.getElementById('chat')
          chat.scrollTo(0, chat.scrollHeight)
        })                                
      })
      .catch((err) => {
        console.error(err)
        this.$router.push({ name: 'Catalog' })
      })                   
    },
    // mounted(){
    //   this.myInterval = setInterval(() => {
    //     this.readMessages(this.$route.params.id)
    //   }, 3000)
    // },
    beforeDestroy(){
      clearInterval(this.myInterval)
    },   
    data(){
      return {
        myInterval: null,
        errors: null,
        selected_id: null,
        data: {
          distributor_id: this.$route.params.id,
          trader_id: null,
          message_sentBy: 'distributor',
          message_body: null,
        }
      }
    },
    methods: {
        ...mapActions(['readyApp', 'readMessages', 'sendMessage']),
        sendMsg(){
          this.sendMessage(this.data)
          .then(() => {
            this.$toastr.s('Message Send Successfully!')
            this.data.message_body = null            
          })
          .catch((err) => {
            console.log(err)
            this.errors = err.response.data.errors
            for(var error in this.errors){
              this.$toastr.e(this.errors[error][0])
            }            
          })
        },
        getName(id){
          var traderObj = this.getTraders.filter((t) => {
            return parseInt(id) === parseInt(t.id)
          })
          return traderObj[0].trader_firstName + ' ' + traderObj[0].trader_lastName
        },
        getDate(message){      
          var dateArr = message.created_at.split('T')         
          var date = dateArr[0].split('-')
          var year = date[0]
          var month = date[1]
          var day = date[2]
          var time = dateArr[1].split('.')[0]
          var hr = time.split(':')[0]
          var min = time.split(':')[1]
          var sec = time.split(':')[2]         
          var meridiem = null;
          if(parseInt(hr) > 12){
            meridiem = 'PM'
            hr = parseInt(hr) - 12
          }
          else if(parseInt(hr) <= 12){
            meridiem = 'AM'
          }
          else if(parseInt(hr) == 0){
            hr = '01'
          }          
          var wordDay = new Date(year, month, day).toString().split(" ")[0];
          var wordMonth = new Date(year, month, day).toString().split(" ")[1];
          return wordDay + ' ' + wordMonth + ' ' + day + ' ' + year + ' ' + hr + ':' + min + ':' + sec + ' ' + meridiem;

        },
        setSelectedId(id){
          var [last] = this.$refs[`last${this.selected_id}`];
          console.log(last)
          if(last){
            last.scrollIntoView()
          }                   
          this.selected_id = id         
          this.data.trader_id = id   
          this.data.message_body = null      
        }
    },
    computed: {
      ...mapGetters(['getMessages', 'getTraders']),
      getFilteredNameList(){
        var idss = new Set();
        if(this.getMessages){
          for(var msg of this.getMessages){
            idss.add(msg.trader_id)
          } 
          var ids = [];
          for (var id of idss){
            ids.push(id)
          }          
        }       
        return ids
      },
      getSelectedMsgs(){
        console.log(1)
        if(this.getMessages){          
          var messages = this.getMessages.filter((msg) => {
            return parseInt(this.selected_id) === parseInt(msg.trader_id)
          }) 
        }          
        return messages
      }
    }
}
</script>

<style scoped>
.trader{
  background:transparent
}
.trader:hover{
  background:grey;
  transition: 0.5s;
  cursor: pointer;
}
.selected{
  background:grey;
}
.sender{
  background:white; 
  width:15%; 
  border-radius: 15px;
}
.recipient{
  background:#8FA768; 
  color:white; 
  width:15%; 
  border-radius: 15px;
}
</style>
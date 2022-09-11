import axiosClient from '../../../axios'
//import router from 'vue-router'


const state = {
    user : {
        api_token : sessionStorage.getItem('api_token'),
        role: sessionStorage.getItem('user_role'),
        name: sessionStorage.getItem('name'),
        email: sessionStorage.getItem('email'),
        id: sessionStorage.getItem("id")
    },    
};

const getters = {
    getName(){
        return state.user.name;
    },
    getID(){
        return state.user.id
    }
};

const actions = {  
    register({ commit }, user){
        var contactNumArr = [user.contactNum, user.contactNum2, user.contactNum3];
        contactNumArr = contactNumArr.filter((num) => {
            return num !== ''
        })
        delete user['contactNum2']
        delete user['contactNum3']
        user.contactNum = contactNumArr
        return axiosClient.post('/register',user)
        .then((res)=>{
            commit('setToken', res.data)
            console.log(res.data);                                     
        })            
    },
    logout({ commit }){
        return axiosClient.post('/logout')
        .then((res)=>{            
            console.log(res.data);               
            commit('logoutUser');                                  
        })
        .catch((err)=>{           
            console.log(err)                          
        })
    },
    login({ commit }, data){
        return axiosClient.post('/login', data)
        .then((res)=>{            
            console.log(res.data);               
            commit('setToken', res.data);            
        })        
    },    
};

const mutations = {
    setToken: (state, data) => {
        state.user.api_token = data.token        
        state.user.role = data.role
        state.user.name = data.name   
        state.user.email = data.user.email   
        state.user.id = data.id  
        sessionStorage.setItem('api_token', data.token)                       
        sessionStorage.setItem('user_role', data.role)                       
        sessionStorage.setItem('name', data.name)          
        sessionStorage.setItem('email', data.user.email)
        sessionStorage.setItem('id', data.id)
        
    },
    logoutUser: () => {
        state.user.api_token = null        
        state.user.role = null        
        state.user.name = null        
        state.user.email = null    
        state.user.id = null    
        sessionStorage.clear()
    },      
};


export default {
    state,
    getters,
    actions,
    mutations
}

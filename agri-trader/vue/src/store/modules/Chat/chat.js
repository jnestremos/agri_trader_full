import axiosClient from "../../../axios";
import auth from "../Auth/auth";
const state = {
    traders: null,
    distributors: null,
    messages: null
}

const getters = {
    getMessages(){
        return state.messages
    },
    getTraders(){
        return state.traders
    },
    getDistributors(){
        return state.distributors
    },
}

const actions = {
    readMessages({ commit }, id){       
        return axiosClient.get(`messages/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setMessages', res.data)
        })                    
    },
    sendMessage({ commit }, data){
        if(auth.state.user.role === 'distributor'){
            return axiosClient.post(`messages/${data.distributor_id}/add`, data)
            .then((res) => {
                console.log(res.data)
                commit('asd')
            }) 
        }
        else if(auth.state.user.role === 'trader'){
            return axiosClient.post(`messages/${data.trader_id}/add`, data)
            .then((res) => {
                console.log(res.data)
                commit('asd')
            })             
        }    
    }
}

const mutations = {
    setMessages: (state, data) => {
        state.messages = data.messages
        state.traders = data.traders
        state.distributors = data.distributors
    },
    asd: () => {
        console.log(1)
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
import axiosClient from "../../../axios";

const state = {
    project_status_id: null,
    profit_sharing: {
        project: null,        
        contract: null,        
        sales: null,        
        stockOut: null,        
        expenditures: null,        
        contract_share: null,        
        supplies: null,        
        farm_owner: null,        
        produce: null,    
        profit_sharing: null    
    },   
}

const getters = {
    getStatusForProfit(){
        return state.project_status_id        
    },
    getProfitSharing(){
        return state.profit_sharing
    }
}

const actions = {
    initStatusForProfit({ commit }, id){
        commit('setStatusForProfit', id)
    },
    fetchInitProfitSharing({ commit }, id){
        return axiosClient.get(`project/profit/sharing/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setProfitSharing', res.data)
        })
    },
    sendProfitSharing({ commit }, data){
        return axiosClient.post(`/project/profit/sharing/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setStatusForProfit: (state, data) => {
        state.project_status_id = data
    },
    setProfitSharing: (state, data) => {
        state.profit_sharing.project = data.project
        state.profit_sharing.contract = data.contract
        state.profit_sharing.sales = data.sales
        state.profit_sharing.stockOut = data.stockOut
        state.profit_sharing.expenditures = data.expenditures
        state.profit_sharing.contract_share = data.contract_share
        state.profit_sharing.supplies = data.supplies
        state.profit_sharing.farm_owner = data.farm_owner
        state.profit_sharing.produce = data.produce
        state.profit_sharing.profit_sharing = data.profit_sharing
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
import axiosClient from '../../../axios'

const state = {
    init_RR: {
        orders: null,
        suppliers: null,
        supplies: null,
        // supplier_contact: null,
        // supplier_contact_person: null,
    }
}

const getters = {
    getPOForRR(){
        return state.init_RR
    }
}

const actions = {
    fetchPOForRR({ commit }, id){
        return axiosClient.get(`/receiving/report/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setPOForRR', res.data)
        })
    },
    fetchPOforUpdateRR({ commit }, id){
        return axiosClient.get(`/receiving/report/${id}/update`)
        .then((res) => {
            console.log(res.data)
            commit('setPOForRR', res.data)
        })
    },
    sendRR({ commit }, data){
        return axiosClient.post(`/receiving/report/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    issueReturn({ commit }, data){
        return axiosClient.post(`/supplyReturn/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    issueRefund({ commit }, data){
        return axiosClient.post(`/supplyRefund/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setPOForRR: (state, data) => {
        state.init_RR.orders = data.orders
        state.init_RR.suppliers = data.suppliers
        state.init_RR.supplies = data.supplies
        // state.init_RR.supplier_contact = data.supplier_contact
        // state.init_RR.supplier_contact_person = data.supplier_contact_person
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
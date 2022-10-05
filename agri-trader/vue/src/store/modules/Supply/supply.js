import axiosClient from '../../../axios'


const state = {
    suppliers: null,
    produces: null,
    supply_list: {
        supplies: null,
        suppliers: null
    }
}

const getters = {
    getSuppliersForAddSupply(){
        return state.suppliers
    },
    getProducesForAddSupply(){
        return state.produces
    },
    getSupplyList(){
        return state.supply_list
    }
}

const actions = {
    formForAddSupply({ commit }){
        return axiosClient.get(`/supply`)
        .then((res) => {
            console.log(res.data)
            commit('setFormForAddSupply', res.data)
        })
    },
    addSupply({ commit }, data){
        return axiosClient.post(`/supply/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchSupplyList({ commit }){
        return axiosClient.get(`/supply/list`)
        .then((res) => {
            console.log(res.data)
            commit('setSupplyList', res.data)
        })
    },
    updateSupply({ commit }, data){
        return axiosClient.patch(`/supply/${data.id}`)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setFormForAddSupply: (state, data) => {
        state.suppliers = data.suppliers
        state.produces = data.produces
    },
    asd: () => {
        console.log(1)
    },
    setSupplyList: (state, data) => {
        state.supply_list.supplies = data.supplies
        state.supply_list.suppliers = data.suppliers
    }

}

export default {
    state,
    getters,
    actions,
    mutations
}
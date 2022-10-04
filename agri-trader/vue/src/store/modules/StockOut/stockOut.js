import axiosClient from "../../../axios"

const state = {
    stockOut_history: {
        stockOut_history: null,
        suppliers: null,
        supplies: null,
        produces: null,
    },
    stockOut: {
        inventory: null,
        supplies: null,
        suppliers: null,
        stockOut: null,
        produces: null,
    }
}

const getters = {
    getStockOutHistory(){
        return state.stockOut_history
    },
    getStockOut(){
        return state.stockOut
    },
}

const actions = {
    fetchStockOutHistory({ commit }){
        return axiosClient.get(`/stockOut/history`)
        .then((res) => {
            console.log(res.data)
            commit('setStockOutHistory', res.data)
        })
    },
    fetchStockOut({ commit }, id){
        return axiosClient.get(`/project/stockOut/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setStockOut', res.data)
        })
    },
    addStockOut({ commit }, data){
        return axiosClient.post(`/project/stockOut/${data.id}`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setStockOutHistory: (state, data) => {
        state.stockOut.stockOut = data.stockOut
        state.stockOut.suppliers = data.suppliers
        state.stockOut.supplies = data.supplies
        state.stockOut.produces = data.produces
    },
    setStockOut: (state, data) => {
        state.stockOut.stockOut = data.stockOut
        state.stockOut.inventory = data.inventory
        state.stockOut.suppliers = data.suppliers
        state.stockOut.supplies = data.supplies
        state.stockOut.produces = data.produces
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
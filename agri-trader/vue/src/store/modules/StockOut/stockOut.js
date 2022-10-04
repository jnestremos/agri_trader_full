import axiosClient from "../../../axios"

const state = {
    stockOut: {
        stockOut_history: null,
        suppliers: null,
        supplies: null,
        produces: null,
    }
}

const getters = {
    getStockOutHistory(){
        return state.stockOut
    }
}

const actions = {
    fetchStockOutHistory({ commit }){
        return axiosClient.get(`/stockOut/history`)
        .then((res) => {
            console.log(res.data)
            commit('setStockOutHistory', res.data)
        })
    }
}

const mutations = {
    setStockOutHistory: (state, data) => {
        state.stockOut.stockOut_history = data.stockOut_history
        state.stockOut.suppliers = data.suppliers
        state.stockOut.supplies = data.supplies
        state.stockOut.produces = data.produces
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
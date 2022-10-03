import axiosClient from "../../../../axios"

const state = {
    stockIn: {
        stockIn_history: null,
        suppliers: null,
        supplies: null,
        produces: null,
    }
}

const getters = {
    getStockInHistory(){
        return state.stockIn
    }
}

const actions = {
    fetchStockInHistory({ commit }){
        return axiosClient.get(`/stockIn/history`)
        .then((res) => {
            console.log(res.data)
            commit('setStockInHistory', res.data)
        })
    }
}

const mutations = {
    setStockInHistory: (state, data) => {
        state.stockIn.stockIn_history = data.stockIn_history
        state.stockIn.suppliers = data.suppliers
        state.stockIn.supplies = data.supplies
        state.stockIn.produces = data.produces
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
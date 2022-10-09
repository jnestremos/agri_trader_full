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
    },
    stockOut_report: {
        stockOut: null,
        suppliers: null,
        supplies: null,
        contracts: null,
        projects: null,
    }
}

const getters = {
    getStockOutHistory(){
        return state.stockOut_history
    },
    getStockOut(){
        return state.stockOut
    },
    getStockOutReport(){
        return state.stockOut_report
    }
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
    },
    fetchStockOutReport({ commit }){
        return axiosClient.get(`/reports/supplyExpenditures`)
        .then((res) => {
            console.log(res.data)
            commit('setStockOutReport', res.data)
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
    },
    setStockOutReport: (state, data) => {
        state.stockOut_report.stockOut = data.stockOut
        state.stockOut_report.supplies = data.supplies
        state.stockOut_report.suppliers = data.suppliers
        state.stockOut_report.projects = data.projects
        state.stockOut_report.contracts = data.contracts
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
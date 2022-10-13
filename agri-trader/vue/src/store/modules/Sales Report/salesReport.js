import axiosClient from "../../../axios";

const state = {
    sales_report: {
        sales: null,
        contracts: null,
        projects: null,
        produce_traders: null,
        produces: null,
        orders: null,
    }
}

const getters = {
    getSalesReport(){
        return state.sales_report
    }
}

const actions = {
    fetchSalesReport({ commit }){
        return axiosClient.get(`/reports/salesReport`)
        .then((res) => {
            console.log(res.data)
            commit('setSalesReport', res.data)
        })
    }
}

const mutations = {
    setSalesReport: (state, data) => {
        state.sales_report.sales = data.sales
        state.sales_report.contracts = data.contracts
        state.sales_report.projects = data.projects
        state.sales_report.produce_traders = data.produce_traders
        state.sales_report.produces = data.produces
        state.sales_report.orders = data.orders
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
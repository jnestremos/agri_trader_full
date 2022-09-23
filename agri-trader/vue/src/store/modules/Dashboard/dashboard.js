import axiosClient from '../../../axios'
const state = {
    totalSales: null
}

const getters = {
    getTotalSales(){
        return state.totalSales
    }
}

const actions = {
    fetchDashboardData({ commit }){
        return axiosClient.get(`/dashboard`)
        .then((res) => {
            console.log(res.data)
            commit('setDashboardData', res.data)
        })
    }
}

const mutations = {
    setDashboardData: (state, data) => {
        state.totalSales = data.totalSales
    }
}

export default {
    state,
    getters,
    mutations,
    actions
}
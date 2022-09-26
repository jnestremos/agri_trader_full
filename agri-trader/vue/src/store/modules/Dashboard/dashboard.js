import axiosClient from '../../../axios'
const state = {
    totalSales: null,
    incomeSumm: null
}

const getters = {
    getTotalSales(){
        return state.totalSales
    },
    getIncomeSumm(){
        return state.incomeSumm
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
        state.incomeSumm = data.incomeSumm
    }
}

export default {
    state,
    getters,
    mutations,
    actions
}
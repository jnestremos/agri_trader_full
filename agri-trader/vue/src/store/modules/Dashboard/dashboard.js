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
        var dateToday = new Date().toISOString()
        var year = dateToday.split('T')[0].split('-')[0]
        var month = dateToday.split('T')[0].split('-')[1]
        // var day = new Date().toLocaleString().split('/')[1]
        var day = dateToday.split('T')[0].split('-')[2]
        var incomeSummObj = data.incomeSumm.filter((i) => {
            console.log(`${year}-${month}-${day}`)
            console.log(i['created_at'].split('T')[0])
            console.log(`${year}-${month}-${day}` === i['created_at'].split('T')[0])
            return `${year}-${month}-${day}` === i['created_at'].split('T')[0]
        })
        state.incomeSumm = incomeSummObj
    }
}

export default {
    state,
    getters,
    mutations,
    actions
}
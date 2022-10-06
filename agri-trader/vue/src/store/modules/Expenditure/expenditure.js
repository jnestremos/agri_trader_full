import axiosClient from "../../../axios";


const state = {
    expenditures: null,
    project_commenceDate: null
}

const getters = {
    getExpenditures(){
        return state.expenditures
    },
    getProjectCommenceDate(){
        return state.project_commenceDate
    }
}

const actions = {
    fetchProjectExpenditures({ commit }, id){
        return axiosClient.get(`/project/expenditures/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setExpenditure', res.data)
        })
    },
    addProjectExpenditure({ commit }, data){
        return axiosClient.post(`/project/expenditures/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setExpenditure: (state, data) => {
        state.expenditures = data.expenditures
        state.project_commenceDate = data.project_commenceDate
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
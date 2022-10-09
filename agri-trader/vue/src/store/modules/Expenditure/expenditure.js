import axiosClient from "../../../axios";


const state = {
    expenditures: null,
    project_commenceDate: null,
    expenditure_report: {
        expenditures: null,
        contracts: null,
        projects: null,
    }
}

const getters = {
    getExpenditures(){
        return state.expenditures
    },
    getProjectCommenceDate(){
        return state.project_commenceDate
    },
    getExpenditureReport(){
        return state.expenditure_report
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
    },
    fetchExpenditureReport({ commit }){
        return axiosClient.get(`/reports/otherExpenditures`)
        .then((res) => {
            console.log(res.data)
            commit('setExpenditureReport', res.data)
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
    },
    setExpenditureReport: (state, data) => {
        state.expenditure_report.expenditures = data.expenditures
        state.expenditure_report.contracts = data.contracts
        state.expenditure_report.projects = data.projects
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
const state = {
    printReport: null
}

const getters ={
    getPrintReport(){
        return state.printReport
    }
}

const actions ={
    fetchPrintReport({ commit }, records) {
        console.log(records)
        commit('setPrintReport', records)
    }
}

const mutations = {
    setPrintReport: (state, data) => {
        state.printReport = data
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}

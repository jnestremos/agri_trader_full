import axiosClient from '../../../axios'


const state = {
    suppliers: null,
    produces: null
}

const getters = {
    getSuppliersForAddSupply(){
        return state.suppliers
    },
    getProducesForAddSupply(){
        return state.produces
    }

}

const actions = {
    formForAddSupply({ commit }){
        return axiosClient.get(`/supply`)
        .then((res) => {
            console.log(res.data)
            commit('setFormForAddSupply', res.data)
        })
    },
    addSupply({ commit }, data){
        return axiosClient.post(`/supply/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setFormForAddSupply: (state, data) => {
        state.suppliers = data.suppliers
        state.produces = data.produces
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
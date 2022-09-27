import axiosClient from '../../../axios'

const state = {
    form_PO: {
        suppliers: null,
        supplies: null,
        produces: null,
    }
}

const getters = {
    getFormPO(){
        return state.form_PO
    }
}

const actions = {
    formForAddPO({ commit }){
        return axiosClient.get(`/supplyOrder`)
        .then((res) => {
            console.log(res.data)
            commit('setFormPO', res.data)
        })
    },
    addPO({ commit }, data){
        return axiosClient.post(`/supplyOrder/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setFormPO: (state, data) => {
        state.form_PO.suppliers = data.suppliers
        state.form_PO.supplies = data.supplies
        state.form_PO.produces = data.produces
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
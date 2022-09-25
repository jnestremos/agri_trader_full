import axiosClient from '../../../axios'

const state = {
    suppliers: null,
    supplier_addresses: null
}

const getters = {
    getSuppliers(){
        return state.suppliers
    },
    getSupplierAddresses(){
        return state.supplier_addresses
    }
}

const actions = {
    addSupplier({ commit }, data){
        console.log(data)
        return axiosClient.post(`/supplier/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchSuppliers({ commit }){
        return axiosClient.get(`/supplier`)
        .then((res) => {
            console.log(res.data)
            commit('setSuppliers', res.data)
        })
    }

}

const mutations = {
    asd: () => {
        console.log(1)
    },
    setSuppliers: (state, data) => {
        state.suppliers = data.suppliers
        state.supplier_addresses = data.supplier_addresses
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
import axiosClient from '../../../axios'

const state = {
    suppliers: null,
    supplier_addresses: null,
    supplier: {
        supplier: null,
        supplier_address: null,
        supplier_contact: null,
        supplier_contact_person: null
    }   
}

const getters = {
    getSuppliers(){
        return state.suppliers
    },
    getSupplierAddresses(){
        return state.supplier_addresses
    },
    getSupplier(){
        return state.supplier
    }
}

const actions = {
    addSupplier({ commit }, data){        
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
    },
    fetchSupplier({ commit }, id){
       return axiosClient.get(`/supplier/${id}`) 
       .then((res) => {
            console.log(res.data)
            commit('setSupplier', res.data)
       })
    },
    updateSupplier({ commit }, data){        
        return axiosClient.patch(`/supplier/${data.id}`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },

}

const mutations = {
    asd: () => {
        console.log(1)
    },
    setSuppliers: (state, data) => {
        state.suppliers = data.suppliers
        state.supplier_addresses = data.supplier_addresses
    },
    setSupplier: (state, data) => {
        state.supplier.supplier = data.supplier
        state.supplier.supplier_address = data.supplier_address
        state.supplier.supplier_contact = data.supplier_contact
        state.supplier.supplier_contact_person = data.supplier_contact_person
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
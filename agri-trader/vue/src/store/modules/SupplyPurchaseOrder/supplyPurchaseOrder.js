import axiosClient from '../../../axios'

const state = {
    form_PO: {
        suppliers: null,
        supplies: null,
        produces: null,
    },
    purchaseOrder: {
        supplier_id: null,
        supply_id: null,
        purchaseOrder_num: null,
        purchaseOrder_status: null,
        purchaseOrder_qty: null,
        purchaseOrder_unit: null,
        purchaseOrder_subTotal: null,             
        purchaseOrder_paymentType: null,
        purchaseOrder_paymentMethod: null,
        purchaseOrder_bankName: null,
        purchaseOrder_accNum: null,
        purchaseOrder_accName: null,
        purchaseOrder_totalBalance: null,
        purchaseOrder_dpAmount: null,
        purchaseOrder_percentage: null,
        purchaseOrder_balance: null,
    },    
}   

const getters = {
    getFormPO(){
        return state.form_PO
    },
    getPO(){
        return state.purchaseOrder
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
    },
    initPO({ commit }, data){
        commit('setPO', data)
    },
}

const mutations = {
    setFormPO: (state, data) => {
        state.form_PO.suppliers = data.suppliers
        state.form_PO.supplies = data.supplies
        state.form_PO.produces = data.produces
    },
    setPO: (state, data) => {
        state.purchaseOrder.supplier_id = data.supplier_id
        state.purchaseOrder.supply_id = data.supply_id
        state.purchaseOrder.purchaseOrder_num = data.purchaseOrder_num
        state.purchaseOrder.purchaseOrder_status = data.purchaseOrder_status
        state.purchaseOrder.purchaseOrder_qty = data.purchaseOrder_qty
        state.purchaseOrder.purchaseOrder_unit = data.purchaseOrder_unit
        state.purchaseOrder.purchaseOrder_subTotal = data.purchaseOrder_subTotal 
        state.purchaseOrder.purchaseOrder_paymentType = data.purchaseOrder_paymentType 
        state.purchaseOrder.purchaseOrder_paymentMethod = data.purchaseOrder_paymentMethod 
        state.purchaseOrder.purchaseOrder_bankName = data.purchaseOrder_bankName 
        state.purchaseOrder.purchaseOrder_accNum = data.purchaseOrder_accNum 
        state.purchaseOrder.purchaseOrder_accName = data.purchaseOrder_accName 
        state.purchaseOrder.purchaseOrder_totalBalance = data.purchaseOrder_totalBalance 
        state.purchaseOrder.purchaseOrder_dpAmount = data.purchaseOrder_dpAmount 
        state.purchaseOrder.purchaseOrder_percentage = data.purchaseOrder_percentage 
        state.purchaseOrder.purchaseOrder_balance = data.purchaseOrder_balance                        
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
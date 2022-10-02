import axiosClient from '../../../axios'

const state = {
    form_PO: {
        uuid: null,
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
    purchaseOrder_dashboard: {
        purchaseOrders_filtered: null,
        current_page: null,
        first_page_url: null,
        last_page: null,
        last_page_url: null,
        next_page_url: null,
        per_page: null,
        prev_page_url: null,
        total: null,
        links: null,
        purchaseOrders: null,
        purchaseOrder_accs: null,        
        suppliers: null,        
        supplies: null,        
    },    
    returns_dashboard: {
        returns_filtered: null,
        current_page: null,
        first_page_url: null,
        last_page: null,
        last_page_url: null,
        next_page_url: null,
        per_page: null,
        prev_page_url: null,
        total: null,
        links: null,
        returns: null,       
        suppliers: null,        
        supplies: null,        
    }    
}   

const getters = {
    getFormPO(){
        return state.form_PO
    },
    getPO(){
        return state.purchaseOrder
    },
    getPODashboard(){
        return state.purchaseOrder_dashboard
    },
    getReturnDashboard(){
        return state.returns_dashboard
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
    fetchPODashboard({ commit }, query = null){
        if(query){
            return axiosClient.get(`/supplyOrder/dashboard?${query}`)
            .then((res) => {
                console.log(res.data)
                commit('setPODashboard', res.data)
            })
        }
        else{
            return axiosClient.get(`/supplyOrder/dashboard`)
            .then((res) => {
                console.log(res.data)
                commit('setPODashboard', res.data)
            })
        }
    },
    updatePOStatus({ commit }, id){
        return axiosClient.patch(`/supplyOrder/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    updatePOPayment({ commit }, id){
        return axiosClient.patch(`/supplyOrder/${id}/payment`)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchReturnDashboard({ commit }, query = null){
        if(query){
            return axiosClient.get(`/supplyReturn/dashboard?${query}`)
            .then((res) => {
                console.log(res.data)
                commit('setReturnDashboard', res.data)
            })
        }
        else{
            return axiosClient.get(`/supplyReturn/dashboard`)
            .then((res) => {
                console.log(res.data)
                commit('setReturnDashboard', res.data)
            })
        }
    },
}

const mutations = {
    setFormPO: (state, data) => {
        state.form_PO.uuid = data.uuid
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
    setPODashboard: (state, data) => {
        if(data.purchaseOrders_filtered.data){
            state.purchaseOrder_dashboard.purchaseOrders_filtered = data.purchaseOrders_filtered.data
            state.purchaseOrder_dashboard.current_page = data.purchaseOrders_filtered.current_page
            state.purchaseOrder_dashboard.first_page_url = data.purchaseOrders_filtered.first_page_url
            state.purchaseOrder_dashboard.last_page = data.purchaseOrders_filtered.last_page
            state.purchaseOrder_dashboard.last_page_url = data.purchaseOrders_filtered.last_page_url
            state.purchaseOrder_dashboard.next_page_url = data.purchaseOrders_filtered.next_page_url
            state.purchaseOrder_dashboard.per_page = data.purchaseOrders_filtered.per_page
            state.purchaseOrder_dashboard.prev_page_url = data.purchaseOrders_filtered.prev_page_url
            state.purchaseOrder_dashboard.total = data.purchaseOrders_filtered.total                        
            state.purchaseOrder_dashboard.links = data.purchaseOrders_filtered.links
            state.purchaseOrder_dashboard.links.splice(0, 1)
            state.purchaseOrder_dashboard.links.splice(state.purchaseOrder_dashboard.links.length - 1, 1)            
        }
        else{
            state.purchaseOrder_dashboard.purchaseOrders_filtered = data.purchaseOrders_filtered
        }
        state.purchaseOrder_dashboard.purchaseOrders = data.purchaseOrders
        state.purchaseOrder_dashboard.purchaseOrder_accs = data.purchaseOrder_accs        
        state.purchaseOrder_dashboard.suppliers = data.suppliers        
        state.purchaseOrder_dashboard.supplies = data.supplies        
    },
    setReturnDashboard: (state, data) => {
        if(data.returns_filtered.data){
            state.returns_dashboard.returns_filtered = data.returns_filtered.data
            state.returns_dashboard.current_page = data.returns_filtered.current_page
            state.returns_dashboard.first_page_url = data.returns_filtered.first_page_url
            state.returns_dashboard.last_page = data.returns_filtered.last_page
            state.returns_dashboard.last_page_url = data.returns_filtered.last_page_url
            state.returns_dashboard.next_page_url = data.returns_filtered.next_page_url
            state.returns_dashboard.per_page = data.returns_filtered.per_page
            state.returns_dashboard.prev_page_url = data.returns_filtered.prev_page_url
            state.returns_dashboard.total = data.returns_filtered.total                        
            state.returns_dashboard.links = data.returns_filtered.links
            state.returns_dashboard.links.splice(0, 1)
            state.returns_dashboard.links.splice(state.returns_dashboard.links.length - 1, 1)            
        }
        else{
            state.returns_dashboard.returns_filtered = data.returns_filtered
        }
        state.returns_dashboard.returns = data.returns        
        state.returns_dashboard.suppliers = data.suppliers        
        state.returns_dashboard.supplies = data.supplies        
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
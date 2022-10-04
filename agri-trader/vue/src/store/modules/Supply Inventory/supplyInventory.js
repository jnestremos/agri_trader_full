import axiosClient from "../../../axios";

const state = {
    supply_inventory: {
        inventory: null,
        suppliers: null,
        produces: null,
        supplies: null,
    }
}

const getters = {
    getSupplyInventory(){
        return state.supply_inventory
    }
}

const actions = {
    fetchSupplyInventory({ commit }){
        return axiosClient.get(`/supply/inventory`)
        .then((res) => {
            console.log(res.data)
            commit('setSupplyInventory', res.data)
        })
    }
}

const mutations = {
    setSupplyInventory: (state, data) => {
        state.supply_inventory.inventory = data.inventory
        state.supply_inventory.suppliers = data.suppliers
        state.supply_inventory.produces = data.produces
        state.supply_inventory.supplies = data.supplies
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
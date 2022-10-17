import axiosClient from "../../../axios"

const state = {
    produce_inventory: {
        project: null,
        contract: null,
        produce: null, 
        farm: null, 
        farm_owner: null,
        produce_yields: null,
        produce_inventories: null, 
        sales: null,
        bid_orders: null
    }
}

const getters = {
    getProduceInventory(){
        return state.produce_inventory
    }
}

const actions = {
    addUnsold({ commit }, data){
        return axiosClient.post(`/harvest/${data.id}/unsold`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchProduceInventory({ commit }, id){
        return axiosClient.get(`/harvest/${id}/inventory`)
        .then((res) => {
            console.log(res.data)
            commit('setProduceInventory', res.data)
        })
    }
}

const mutations = {
    setProduceInventory: (state, data) => {
        state.produce_inventory.project = data.project
        state.produce_inventory.contract = data.contract
        state.produce_inventory.produce = data.produce
        state.produce_inventory.farm = data.farm
        state.produce_inventory.farm_owner = data.farm_owner
        state.produce_inventory.produce_yields = data.produce_yields
        state.produce_inventory.produce_inventories = data.produce_inventories
        state.produce_inventory.sales = data.sales
        state.produce_inventory.bid_orders = data.bid_orders
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
import axiosClient from "../../../axios"

const state = {
    harvest_details: {
        yield: null,
        bid_orders: null,
        project_harvestableEnd: null,
        produce: null,
        produce_trader: null,
        farm: null,
        farm_owner: null,
        dist_contactNums: null,
        on_hand_bids: null,
        project_bids: null,
        distributors: null,
        project: null
    }
}

const getters = {
    getHarvestDetails(){
        return state.harvest_details
    }
}

const actions = {
    fetchHarvestDetails({ commit }, id){
        return axiosClient.get(`/harvest/${id}/details`)
        .then((res) => {
            console.log(res.data)
            commit('setHarvestDetails', res.data)
        })
    },
    sendHarvestDetails({ commit }, data){
        if(!(state.harvest_details.produce_trader.produce_numOfGrades > 1)){
            data.produce_yield_qtyHarvested = data.produce_yield_qtyHarvested[0]            
            data.produce_yield_price = data.produce_yield_price[0]
        }        
        return axiosClient.post(`yield/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setHarvestDetails: (state, data) => {
        state.harvest_details.yield = data.yield
        state.harvest_details.project_harvestableEnd = data.project_harvestableEnd
        state.harvest_details.bid_orders = data.bid_orders
        state.harvest_details.produce = data.produce
        state.harvest_details.produce_trader = data.produce_trader
        state.harvest_details.farm = data.farm
        state.harvest_details.farm_owner = data.farm_owner
        state.harvest_details.dist_contactNums = data.dist_contactNums
        state.harvest_details.on_hand_bids = data.on_hand_bids
        state.harvest_details.project_bids = data.project_bids
        state.harvest_details.distributors = data.distributors
        state.harvest_details.projects = data.projects
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
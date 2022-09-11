import axiosClient from "../../../axios"

const state = {
    delivery_form:{
        bid_order: null,
        project_bid: null,
        on_hand_bid: null,
        distributor: null,
        dist_contactNum: null,
        produce_trader: null,
        contract: null,
        yield: null,
        farm: null,
        dist_address: null,
        produce: null,
        bid_order_acc: null
    }
}

const getters = {
    getDeliveryForm(){
        return state.delivery_form
    }
}

const actions = {
    fetchDeliveryFormDetails({ commit }, data){
        return axiosClient.get(`/delivery/form/${data.id}/produce/${data.produce_id}`)
        .then((res) => {
            console.log(res.data)
            commit('setDeliveryFormDetails', res.data)
        })
    },
    sendDeliveryFormDetails({ commit }, data){        
        if(getters.getDeliveryForm().project_bid){
            return axiosClient.put(`bid/project/${data.id}/deliver`, data)
            .then((res) => {
                console.log(res.data)
                commit('asd')
            })
        }
        else{
            return axiosClient.put(`bid/onhand/${data.id}/deliver`, data)
            .then((res) => {
                console.log(res.data)
                commit('asd')
            })
        }

    }
}

const mutations = {
    setDeliveryFormDetails: (state, data) => {
        state.delivery_form.bid_order = data.bid_order
        state.delivery_form.project_bid = data.project_bid
        state.delivery_form.on_hand_bid = data.on_hand_bid
        state.delivery_form.distributor = data.distributor
        state.delivery_form.dist_contactNum = data.dist_contactNum
        state.delivery_form.produce_trader = data.produce_trader
        state.delivery_form.contract = data.contract
        state.delivery_form.yield = data.yield
        state.delivery_form.farm = data.farm
        state.delivery_form.dist_address = data.dist_address
        state.delivery_form.produce = data.produce
        state.delivery_form.bid_order_acc = data.bid_order_acc
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
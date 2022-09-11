import axiosClient from "../../../axios";

const state = {
    produce_data: {
        produces: [],
        current_page: null,
        first_page_url: null,
        last_page: null,
        last_page_url: null,
        next_page_url: null,
        per_page: null,
        prev_page_url: null,
        total: null,
        links: null        
    },
    contracts: null,
    projects: null,
    produce_trader: null,
    farm_produces: null,
    produce_yields: null,
    produce_inventories: null,
    traders: null
}

const getters = {
    getProduceDataa(){
        return state.produce_data
    },
    getProduceTrader(){
        return state.produce_trader
    },
    getProjectss(){
        return state.projects
    },
    getContractss(){
        return state.contracts
    },    
    getFarmProducess(){
        return state.farm_produces
    },    
    getProduceYields(){
        return state.produce_yields
    },    
    getProduceInventories(){
        return state.produce_inventories
    },    
    getTraderss(){
        return state.traders
    }
}


const actions = {
    fetchAllCatalogProduces({ commit }, query = null){
        if(query){
            return axiosClient.get(`/catalog/?${query}`)
            .then((res) => {
                console.log(res.data)
                commit('setAllProducess', res.data)
            })
        }
        else{
            return axiosClient.get('/catalog')
            .then((res) => {
                console.log(res.data)
                commit('setAllProducess', res.data)
            })
        }
    },
}


const mutations = {
    setAllProducess: (state, data) => {
        if(data.produces.data){            
            state.produce_data.produces = data.produces.data
            state.produce_data.current_page = data.produces.current_page
            state.produce_data.first_page_url = data.produces.first_page_url
            state.produce_data.last_page = data.produces.last_page
            state.produce_data.last_page_url = data.produces.last_page_url
            state.produce_data.next_page_url = data.produces.next_page_url
            state.produce_data.per_page = data.produces.per_page
            state.produce_data.prev_page_url = data.produces.prev_page_url
            state.produce_data.total = data.produces.total
            state.produce_data.links = data.produces.links
            state.produce_data.links.splice(0, 1)
            state.produce_data.links.splice(state.produce_data.links.length - 1, 1)  
        }
        else{
            state.produce_data.produces = data.produces
        }
        state.contracts = data.contracts
        state.projects = data.projects       
        state.produce_trader = data.produce_trader 
        state.farm_produces = data.farm_produce
        state.produce_yields = data.produce_yields
        state.produce_inventories = data.produce_inventories
        state.traders = data.traders
    },
}



export default {
    state,
    getters,
    actions,
    mutations
}
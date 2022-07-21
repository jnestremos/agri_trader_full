import axiosClient from "../../../axios";

const state = {
    contract_data: {
        contracts: [],
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
    produces: null
}

const getters = {
    getContractData(){
        return state.contract_data
    },
    getProducess(){
        return state.produces
    },
    getProjectss(){
        return state.projects
    },
    getContractss(){
        return state.contracts
    },    
    
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
        if(data.filteredContracts.data){            
            state.contract_data.contracts = data.filteredContracts.data
            state.contract_data.current_page = data.filteredContracts.current_page
            state.contract_data.first_page_url = data.filteredContracts.first_page_url
            state.contract_data.last_page = data.filteredContracts.last_page
            state.contract_data.last_page_url = data.filteredContracts.last_page_url
            state.contract_data.next_page_url = data.filteredContracts.next_page_url
            state.contract_data.per_page = data.filteredContracts.per_page
            state.contract_data.prev_page_url = data.filteredContracts.prev_page_url
            state.contract_data.total = data.filteredContracts.total
            state.contract_data.links = data.filteredContracts.links
            state.contract_data.links.splice(0, 1)
            state.contract_data.links.splice(state.produce_data.links.length - 1, 1)  
        }
        else{
            state.contract_data.contracts = data.filteredContracts
        }
        state.contracts = data.contracts
        state.projects = data.projects
        state.produces = data.produces
    },
}



export default {
    state,
    getters,
    actions,
    mutations
}
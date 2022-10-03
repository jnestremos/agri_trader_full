import axiosClient from '../../../axios'

const state = {
    project_data: {
        projects: [],
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
    farm_list: null,
    trader_list: null,
    produce_list: null,
    share_list: null,
    date_list: null,

    farm_data: {
        farms: [],
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
    contract_list: null,
    project_list: null,
    farmProduce_list: null,

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
        links: null,        
    },

    farm_info: {
        farm: null,
        farm_produce: null,
        farm_address: null,
        owner: null,
        projects: null,
    },

    produce_info: {
        produce_history: null,
        produce: null,
        produce_trader: null,
        farms: null,
        projects: null,
        contracts: null,
    },

    project: null,
    contract: null,
    farm: null,
    share: null,
    farm_owner: null,
    produce: null,
    history: null

}


const getters = {
    getProjectOwner(){
        return state.project
    },
    getFarmForOwner(){
        return state.farm
    },
    getFarmOwner(){
        return state.owner
    },
    getContractOwner(){
        return state.contract
    },
    getProduceOwner(){
        return state.produce
    },
    getShareOwner(){
        return state.share
    },
    getHistoryOwner(){
        return state.history
    },
    getProjectDataForOwner(){
        return state.project_data
    },
    getFarmDataForOwner(){
        return state.farm_data
    },
    getProduceDataForOwner(){
        return state.produce_data
    },
    getFarmListForOwner(){
        return state.farm_list
    },
    getTraderListForOwner(){
        return state.trader_list
    },
    getProduceListForOwner(){
        return state.produce_list
    },
    getShareListForOwner(){
        return state.share_list
    },
    getDateListForOwner(){
        return state.date_list
    },
    getContractListForOwner(){
        return state.contract_list
    },
    getProjectListForOwner(){
        return state.project_list
    },
    getFarmProduceListForOwner(){
        return state.farmProduce_list
    },
    getFarmInfoForOwner(){
        return state.farm_info
    },
    getProduceInfoForOwner(){
        return state.produce_info
    },    
}

const actions = {
    fetchAllProjectsForOwner({ commit }, query = null){
        if(query){
            return axiosClient.get('/projects/owner/all?'+query)
            .then((res) => {                
                console.log(res.data);            
                commit('setAllProjects', res.data)                                  
            })
            .catch((err) => {
                console.log(err)
            })
        }
        else{            
            return axiosClient.get('/projects/owner/all')
            .then((res) => {               
                console.log(res.data);            
                commit('setAllProjects', res.data)                                               
            })
            .catch((err) => {
                console.log(err)
            })
        }        
    },
    fetchProjectOwner({ commit }, id){
        return axiosClient.get(`/projects/owner/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setProjectOwner', res.data)
        })
    },
    updateProjectOwner({ commit }, data){       
        return axiosClient.patch(`/projects/owner/${data.id}`, data.data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchAllFarmsForOwner({ commit }, query = null){
        if(query){
            return axiosClient.get('/farms/owner/all?'+query)
            .then((res) => {                
                console.log(res.data);            
                commit('setFarms', res.data)                                  
            })
            .catch((err) => {
                console.log(err)
            })
        }
        else{            
            return axiosClient.get('/farms/owner/all')
            .then((res) => {               
                console.log(res.data);            
                commit('setFarms', res.data)                                               
            })
            .catch((err) => {
                console.log(err)
            })
        }        
    },
    fetchAllProducesForOwner({ commit }, query = null){
        if(query){
            return axiosClient.get(`/produces/owner/all?${query}`)
            .then((res) => {
                console.log(res.data)
                commit('setAllProduces', res.data)
            })
        }
        else{
            return axiosClient.get(`/produces/owner/all`)
            .then((res) => {
                console.log(res.data)
                commit('setAllProduces', res.data)
            })
        }
    },
    fetchFarmForOwner({ commit }, id){
        return axiosClient.get(`/farm/owner/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setFarmForOwner', res.data)
        })
    },
    fetchProduceForOwner({ commit }, id){
        return axiosClient.get(`/produce/owner/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setProduceForOwner', res.data)
        })
    }
}

const mutations = {
    setProjectOwner(state, data){
        state.project = data.project
        state.contract = data.contract
        state.farm = data.farm
        state.share = data.share
        state.farm_owner = data.farm_owner
        state.produce = data.produce
        state.history = data.history
    },
    setAllProjects(state, data){
        if(data.projects.data){
            state.project_data.projects = data.projects.data
            state.project_data.current_page = data.projects.current_page
            state.project_data.first_page_url = data.projects.first_page_url
            state.project_data.last_page = data.projects.last_page
            state.project_data.last_page_url = data.projects.last_page_url
            state.project_data.next_page_url = data.projects.next_page_url
            state.project_data.per_page = data.projects.per_page
            state.project_data.prev_page_url = data.projects.prev_page_url
            state.project_data.total = data.projects.total                        
            state.project_data.links = data.projects.links
            state.project_data.links.splice(0, 1)
            state.project_data.links.splice(state.project_data.links.length - 1, 1)            
        }
        else{
            state.project_data.projects = data.projects
        }
        state.farm_list = data.farms
        state.trader_list = data.trader
        state.produce_list = data.produces
        state.share_list = data.shares
        state.date_list = data.start_dates        
    },
    setFarms: (state, data) => {
        if(data.farms.data){
            state.farm_data.farms = data.farms.data
            state.farm_data.current_page = data.farms.current_page
            state.farm_data.first_page_url = data.farms.first_page_url
            state.farm_data.last_page = data.farms.last_page
            state.farm_data.last_page_url = data.farms.last_page_url
            state.farm_data.next_page_url = data.farms.next_page_url
            state.farm_data.per_page = data.farms.per_page
            state.farm_data.prev_page_url = data.farms.prev_page_url
            state.farm_data.total = data.farms.total                        
            state.farm_data.links = data.farms.links
            state.farm_data.links.splice(0, 1)
            state.farm_data.links.splice(state.farm_data.links.length - 1, 1)            
        }
        else{
            state.farm_data.farms = data.farms
        }        
        state.contract_list = data.contracts          
        state.project_list = data.projects 
        state.farmProduce_list = data.farm_produces         
        state.trader_list = data.traders
    },
    setAllProduces: (state, data) => {
        if(data.produces.data){
            state.produce_data.farms = data.produces.data
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
        state.produce_list = data.produce_list
    },
    setFarmForOwner: (state, data) => {
        state.farm_info.farm = data.farm
        state.farm_info.farm_produces = data.farm_produces
        state.farm_info.farm_address = data.farm_address
        state.farm_info.owner = data.owner
        state.farm_info.projects = data.projects
    },
    setProduceForOwner: (state, data) => {
        state.produce_info.produce_history = data.produce_history
        state.produce_info.produce = data.produce
        state.produce_info.produce_trader = data.produce_trader
        state.produce_info.contracts = data.contracts
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
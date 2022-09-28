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
    date_list: null
}


const getters = {
    getProjectDataForOwner(){
        return state.project_data
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
}

const actions = {
    fetchAllProjectsForOwner({ commit }, query = null){
        if(query){
            return axiosClient.get('/projects/owner?'+query)
            .then((res) => {                
                console.log(res.data);            
                commit('setAllProjects', res.data)                                  
            })
            .catch((err) => {
                console.log(err)
            })
        }
        else{            
            return axiosClient.get('/projects/owner')
            .then((res) => {               
                console.log(res.data);            
                commit('setAllProjects', res.data)                                               
            })
            .catch((err) => {
                console.log(err)
            })
        }        
    },
}

const mutations = {
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
}

export default {
    state,
    getters,
    actions,
    mutations
}
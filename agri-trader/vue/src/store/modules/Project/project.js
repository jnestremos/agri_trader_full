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
    farms: null,
    produces: null,
    owners: null
}

const getters = {
    getProjects(){
        return state.project_data.projects
    },
    getProjectData(){
        return state.project_data
    },
    getFarmsForProject(){
        return state.farms
    },
    getProducesForProject(){
        return state.produces
    },
    getOwnersForProject(){
        return state.owners
    }
}

const actions = {
    fetchAllProjects({ commit }, query = null){
        if(query){
            return axiosClient.get('/projects?'+query)
            .then((res) => {                
                console.log(res.data);            
                commit('setAllProjects', res.data)                                  
            })
            .catch((err) => {
                console.log(err)
            })
        }
        else{            
            return axiosClient.get('/projects')
            .then((res) => {               
                console.log(res.data);            
                commit('setAllProjects', res.data)                                               
            })
            .catch((err) => {
                console.log(err)
            })
        }        
    },
    fetchAllFarmsForProject({ commit }){
        return axiosClient.get('/farm/all')
        .then((res) => {            
            console.log(res.data)
            commit('setAllFarmsForProject', res.data)
        })
    },
    fetchAllProducesForProject({ commit }, farm_id){
        return axiosClient.get(`/farm/produces/all/${farm_id}`)
        .then((res) => {
            console.log(res.data)
            commit('setAllProducesForProject', res.data)
        })
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
    },
    setAllFarmsForProject(state, data){
        state.farms = data.farms
        state.owners = data.owners        
    },
    setAllProducesForProject(state, data){
        state.produces = data.produces
    }
}


export default {
    state,
    getters,
    actions,
    mutations
}
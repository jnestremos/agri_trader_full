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
        links: null,
        projectss: null    
    },
    farm: null,
    history: null,
    expenditures: null,
    profit_sharing: null,
    refunds: null,
    produce_yields: null,
    produce_inventory: null,
    stockOut: null,
    supplies: null,
    share: null,
    farm_owner: null,
    produce: null,
    contract: null,
    project: null,
    farms: null,
    produces: null,
    farm_produces: null,
    owners: null,
    farm_list: null,
    owner_list: null,
    produce_list: null,
    share_list: null,
    date_list: null,
    project_report: {
        projects: null,
        contracts: null,
        produces: null,
        farms: null,
        contract_shares: null,
        farm_owners: null,
    },
    project_images: {
        images: null,        
        project: null        
    }
}

const getters = {
    getProjects(){
        return state.project_data.projects
    },
    getProjectReport(){
        return state.project_report
    },
    getExpenditureForProject(){
        return state.expenditures
    },
    getStockOutForProject(){
        return state.stockOut
    },
    getProfitSharingForProject(){
        return state.profit_sharing
    },
    getRefundForProject(){
        return state.refunds
    },
    getInventoryForProject(){
        return state.produce_inventory
    },
    getYieldForProject(){
        return state.produce_yields
    },
    getSuppliesForProject(){
        return state.supplies
    },
    getProjectData(){
        return state.project_data
    },
    getFarmsForProject(){
        return state.farms
    },
    getProducesForProject(){
        return state.farm_produces
    },
    getTimeOfHarvest(){
        return state.produces
    },
    getOwnersForProject(){
        return state.owners
    },
    getFarmList(){
        return state.farm_list
    },
    getOwnerList(){
        return state.owner_list
    },
    getProduceList(){
        return state.produce_list
    },
    getShareList(){
        return state.share_list
    },
    getDateList(){
        return state.date_list
    },
    getProject(){
        return state.project
    },
    getContract(){
        return state.contract
    },
    getFarm(){
        return state.farm
    },
    getProduce(){
        return state.produce
    },
    getShare(){
        return state.share
    },
    getOwner(){
        return state.owner
    },
    getHistory(){
        return state.history
    },
    getProjectImages(){
        return state.project_images
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
    addProject({ commit }, data){
        return axiosClient.post('/project/add', data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchProject({ commit }, id){
        return axiosClient.get(`/projects/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setProject', res.data)
        })
    },
    updateProject({ commit }, data){       
        return axiosClient.patch(`/projects/${data.id}`, data.data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    refundAllOrders({ commit }, data){
        return axiosClient.post(`/refund/all/orders/${data.id}`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchProjectReport({ commit }){
        return axiosClient.get(`/reports/projects`)
        .then((res) => {
            console.log(res.data)
            commit('setProjectReport', res.data)
        })
    },
    fetchProjectImages({ commit }, id){
        return axiosClient.get(`project/${id}/images`)
        .then((res) => {
            console.log(res.data)
            commit('setProjectImages', res.data)
        })
    },
    addProjectImage({ commit }, data){
        return axiosClient.post(`/project/add/pictures`, data, {
            headers: {
                'Content-type' : 'multipart/form-data'
            }
        })
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }   
}

const mutations = {
    setProjectImages: (state, data) => {
        state.project_images.images = data.images
        state.project_images.project = data.project
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
        state.project_data.projectss = data.projectss
        state.farm_list = data.farms
        state.owner_list = data.farm_owners
        state.produce_list = data.produces
        state.share_list = data.shares
        state.date_list = data.start_dates        
    },
    setAllFarmsForProject(state, data){
        state.farms = data.farms
        state.owners = data.owners        
    },
    setAllProducesForProject(state, data){
        state.produces = data.produces
        state.farm_produces = data.farm_produces
    },
    asd(){
        console.log(1)
    },
    setProject(state, data){
        state.project = data.project
        state.contract = data.contract
        state.farm = data.farm
        state.share = data.share
        state.farm_owner = data.farm_owner
        state.produce = data.produce
        state.history = data.history
        state.expenditures = data.expenditures
        state.stockOut = data.stockOut
        state.supplies = data.supplies
        state.profit_sharing = data.profit_sharing
        state.refunds = data.refunds
        state.produce_inventory = data.produce_inventory
        state.produce_yields = data.produce_yields
        state.project_images.images = data.images
    },
    setProjectReport: (state, data) => {
        state.project_report.contracts = data.contracts
        state.project_report.projects = data.projects
        state.project_report.farms = data.farms
        state.project_report.produces = data.produces
        state.project_report.contract_shares = data.contract_shares
        state.project_report.farm_owners = data.farm_owners
    }
}


export default {
    state,
    getters,
    actions,
    mutations
}
// import store from '../..';
import axiosClient from '../../../axios'


const state = {
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
    farm_produces: [],
    owners: [],       
    farm_details: {
        farm: {
            farm_hectares: null,
            farm_imageUrl: null,
            farm_name: null,
            farm_titleNum: null,
        },        
        owner: {
            owner_firstName: null,
            owner_lastName: null
        },
        produces: null,
        address: {
            farm_address: null,
            farm_city: null,
            farm_province: null,
            farm_zipcode: null,
        },
        farm_partners: null
    },
    farm_report: {
        farms: null,
        farm_owners: null,
        farm_produces: null,
        projects: null
    },
    owner_report: {
        farms: null,
        farm_owners: null,
        farm_owner_addresses: null,
        farm_owner_contact_numbers: null,
    }
};


const getters = {
    getFarms(){
        return state.farm_data.farms
    },
    getOwners(){
        return state.owners
    },
    getFarmData(){
        return state.farm_data
    },
    getFarmDetails(){
        return state.farm_details
    },
    getFarmProduces(){
        return state.farm_produces
    },
    getFarmReport(){
        return state.farm_report
    },
    getFarmOwnerReport(){
        return state.owner_report
    }
};


const actions = {
    fetchAllFarms({ commit }, query = null){
        if(query){
            return axiosClient.get('/farm?'+query)
            .then((res) => {                
                console.log(res.data);            
                commit('setFarms', res.data)                                  
            })
            .catch((err) => {
                console.log(err)
            })
        }
        else{            
            return axiosClient.get('/farm')
            .then((res) => {               
                console.log(res.data);            
                commit('setFarms', res.data)                                               
            })
            .catch((err) => {
                console.log(err)
            })
        }
        
    },
    registerOwner({ commit }, owner){        
        var contactNumArr = [owner.contactNum, owner.contactNum2, owner.contactNum3];
        contactNumArr = contactNumArr.filter((num) => {
            return num !== ''
        })
        delete owner['contactNum2']
        delete owner['contactNum3']
        owner.contactNum = contactNumArr
        return axiosClient.post('/farm/owner/add', owner)
        .then((res) => {            
            console.log(res.data);    
            commit('asd')        
        })        
    },
    fetchAllOwners({ commit }){
        return axiosClient.get('/farm/owners')
        .then((res) => {           
            console.log(res.data);
            commit('setOwners', res.data)                   
        })
        .catch((err) => {
            console.log(err)
        })
    },
    addFarm({ commit }, farm){                     
        return axiosClient.post('/farm/add', farm, {
            headers: {
                'Content-type' : 'multipart/form-data'
            }
        })
        .then((res) => {
            console.log(res.data);
            commit('asd')
        })
    },
    fetchFarm({ commit }, id){
        return axiosClient.get(`/farm/details/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setFarm', res.data)
        })
    },
    addProduceToFarm({ commit }, data){
        return axiosClient.post('farm/add/produce', data)
        .then((res) => {
            commit('setFarmProduce', res.data)
        })
    },
    fetchFarmProduces({ commit }, farm_id){
        return axiosClient.get(`/produces/${farm_id}`)
        .then((res) => {
            console.log(res.data)
            commit('setFarmProduces', res.data)
        })
    },
    fetchFarmReport({ commit }){
        return axiosClient.get(`/reports/farms`)
        .then((res) => {
            console.log(res.data)
            commit('setFarmReport', res.data)
        })
    },
    fetchFarmOwnerReport({ commit }){
        return axiosClient.get(`/reports/farms/owners`)
        .then((res) => {
            console.log(res.data)
            commit('setFarmOwnerReport', res.data)
        })
    }
};


const mutations = {
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
        state.farm_produces = data.farm_produces          
        
    },
    setOwners: (state, data) => {
        state.owners = data.owners
    },
    asd: () => {
        console.log(1)
    },
    setFarm: (state, data) => {
        state.farm_details.farm.farm_hectares = data.farm.farm_hectares
        state.farm_details.farm.farm_imageUrl = data.farm.farm_imageUrl
        state.farm_details.farm.farm_name = data.farm.farm_name
        state.farm_details.farm.farm_titleNum = data.farm.farm_titleNum
        state.farm_details.owner.owner_firstName = data.owner.owner_firstName
        state.farm_details.owner.owner_lastName = data.owner.owner_lastName
        if(data.produces.length > 0){
            state.farm_details.produces = data.produces
        }
        state.farm_details.address.farm_address = data.farm_address.farm_address
        state.farm_details.address.farm_zipcode = data.farm_address.farm_zipcode
        state.farm_details.address.farm_city = data.farm_address.farm_city
        state.farm_details.address.farm_province = data.farm_address.farm_province
        if(data.farm_partners.length > 0){
            state.farm_details.farm_partners = data.farm_partners
        }
        
    }, 
    setFarmProduce(state, data){
        state.farm_details.produces = data.produces
    },    
    setFarmProduces(state, data){
        state.farm_produces = data.produces
    },    
    setFarmReport: (state, data) => {
        state.farm_report.farms = data.farms
        state.farm_report.farm_owners = data.farm_owners
        state.farm_report.farm_produces = data.farm_produces
        state.farm_report.projects = data.projects
    },
    setFarmOwnerReport: (state, data) => {
        state.owner_report.farms = data.farms
        state.owner_report.farm_owners = data.farm_owners
        state.owner_report.farm_owner_addresses = data.farm_owner_addresses
        state.owner_report.farm_owner_contact_numbers = data.farm_owner_contact_numbers
    }
};





export default {
    state,
    getters,
    actions,
    mutations
}
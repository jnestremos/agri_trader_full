import axiosClient from '../../../axios'


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
    produce_details: {
        prod_name: null,
        prod_type: null,
        prod_timeOfHarvest: null,
        produce_numOfGrades: null
    },
    produces: null
}

const getters = {
    getProduceData(){
        return state.produce_data
    },
    getProduces(){
        return state.produce_data.produces
    },
    getProduceDetails(){
        return state.produce_details
    },
    getAllProduceOptions(){
        return state.produces
    }
}

const actions = {
    fetchAllProduces({ commit }, query = null){
        if(query){
            return axiosClient.get(`/produces/?${query}`)
            .then((res) => {
                console.log(res.data)
                commit('setAllProduces', res.data)
            })
        }
        else{
            return axiosClient.get('/produces')
            .then((res) => {
                console.log(res.data)
                commit('setAllProduces', res.data)
            })
        }
    },
    addProduce({ commit }){
        return axiosClient.post('/produce/add')
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchProduce({ commit }, id){
        return axiosClient.get(`/produce/details/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setProduce', res.data)
        })
    },   
    getAllProduces({ commit }){
        return axiosClient.get('produces/all')
        .then((res) => {
            console.log(res.data)
            commit('setProduceOptions', res.data)
        })
        
    }   
}

const mutations = {
    setAllProduces: (state, data) => {
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
    },
    asd: () => {
        console.log(1)
    },
    setProduce: (state, data) => {   
        state.produce_details.prod_name = data.produce.prod_name
        state.produce_details.prod_type = data.produce.prod_type
        state.produce_details.prod_timeOfHarvest = data.produce.prod_timeOfHarvest
        state.produce_details.produce_numOfGrades = data.grades.produce_numOfGrades            
    },
    setProduceOptions: (state, data) => {
        state.produces = data.produces
    }
}



export default {
    state,
    getters,
    actions,
    mutations
}
import axiosClient from "../../../axios"



const state = {
    progress_data: {
        trader_id: null,
        trader_name: null,
        prod_name: null,
        prod_class: null,
        trader_contactNum: null,
        project_completionDate: null,
        project_commenceDate: null,
        trader_email: null,
        project_floweringStart: null,
        project_floweringEnd: null,
        project_fruitBuddingStart: null,
        project_fruitBuddingEnd: null,
        project_devFruitStart: null,
        project_devFruitEnd: null,
        project_harvestableStart: null,
        project_harvestableEnd: null,
        contract_estimatedPrice: null,
        contract_estimatedHarvest: null,
        project_images: null,
        farm_name: null
    },
    order_data: {
        orders: [],
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
    order:{
        produce: null,
        bidOrder: null,
        distributor: null,
        project: null,
        contract: null,
        project_bid: null,
        on_hand_bid: null,
        distributor_contactNum: null
    },
    distributors: null,
    contracts: null,
    projects: null,
    produces: null,
    project_bids: null,
    on_hand_bids: null
}

const getters = {
    getProgressData(){
        return state.progress_data
    },
    getOrderData(){
        return state.order_data
    },
    getOrderContracts(){
        return state.contracts
    },
    getOrderProjects(){
        return state.projects
    },
    getOrderProduces(){
        return state.produces
    },
    getOrderProjectBids(){
        return state.project_bids
    },
    getOrderOnHandBids(){
        return state.on_hand_bids
    },
    getOrderDistributors(){
        return state.distributors
    },
    getOrder(){
        return state.order
    }
}

const actions = {
    fetchProjectProgress({ commit }, id){
        return axiosClient.get(`/bid/project/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setProjectt', res.data)
        })
    },
    sendBidOrder({ commit }, data){
        return axiosClient.post(`/bid/project/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchAllOrders({ commit }, query = null){
        if(query){
            return axiosClient.get(`/bid/orders?${query}`)
            .then((res) => {
                console.log(res.data)
                commit('setOrders', res.data)
            })
        }
        else{
            return axiosClient.get(`bid/orders`)
            .then((res) => {
                console.log(res.data)
                commit('setOrders', res.data)
            })
        }
    },
    fetchOrder({ commit }, id){
        //bid/project/{id}/approve
        return axiosClient.get(`/bid/orders/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setOrder', res.data)
        })
    },
    approveProjectBid({ commit }, data){
        return axiosClient.put(`/bid/project/${data.id}/approve`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }
}

const mutations = {
    setProjectt: (state, data) => {
        state.progress_data.trader_id = data.trader_id
        state.progress_data.trader_name = data.trader_name,
        state.progress_data.trader_contactNum = data.trader_contactNum,
        state.progress_data.project_completionDate = data.project_completionDate,
        state.progress_data.project_commenceDate = data.project_commenceDate,
        state.progress_data.trader_email = data.trader_email,
        state.progress_data.project_floweringStart = data.project_floweringStart,
        state.progress_data.project_floweringEnd = data.project_floweringEnd,
        state.progress_data.project_fruitBuddingStart = data.project_fruitBuddingStart,
        state.progress_data.project_fruitBuddingEnd = data.project_fruitBuddingEnd,
        state.progress_data.project_devFruitStart = data.project_devFruitStart,
        state.progress_data.project_devFruitEnd = data.project_devFruitEnd,
        state.progress_data.project_harvestableStart = data.project_harvestableStart,
        state.progress_data.project_harvestableEnd = data.project_harvestableEnd,
        state.progress_data.contract_estimatedPrice = data.contract_estimatedPrice,
        state.progress_data.contract_estimatedHarvest = data.contract_estimatedHarvest,
        state.progress_data.project_images = data.project_images
        state.progress_data.farm_name = data.farm_name
        var arr = data.prod_name.split(' ')
        var container = null            
        if(arr.indexOf('(Class')){            
            for(var i = 0; i < arr.length; i++) {
                if(arr[i] == '(Class'){                    
                    container = arr[i].substring(1, arr[i].length) + ' ' + arr[i + 1].substring(0, 1)                                    
                    arr.pop()
                    arr.pop()
                    state.progress_data.prod_name = data.prod_name.split(container)[0].split(' ')[0]
                    state.progress_data.prod_class = container 
                    break
                }                                                          
            }
        }
        else{            
            state.progress_data.prod_name = data.prod_name 
            state.progress_data.prod_class = null                    
        }
    },
    asd: () => {
        console.log(1)
    },
    setOrders: (state, data) => {
        if(data.orders.data){
            state.order_data.orders = data.orders.data
            state.order_data.current_page = data.orders.current_page
            state.order_data.first_page_url = data.orders.first_page_url
            state.order_data.last_page = data.orders.last_page
            state.order_data.last_page_url = data.orders.last_page_url
            state.order_data.next_page_url = data.orders.next_page_url
            state.order_data.per_page = data.orders.per_page
            state.order_data.prev_page_url = data.orders.prev_page_url
            state.order_data.total = data.orders.total                        
            state.order_data.links = data.orders.links
            state.order_data.links.splice(0, 1)
            state.order_data.links.splice(state.order_data.links.length - 1, 1)            
        }
        else{
            state.order_data.orders = data.orders
        }
        var distArr = []        
        for(var i = 0; i < state.order_data.orders.length; i++){
            var distObj = data.distributors.filter((d) => {
                return parseInt(d.id) === parseInt(state.order_data.orders[i].distributor_id)
            })
            distArr.push(distObj[0])
        }
        state.distributors = distArr
        state.projects = data.projects
        state.contracts = data.contracts        
        state.produces = data.produces
        state.project_bids = data.project_bids
        state.on_hand_bids = data.on_hand_bids    
    }, 
    setOrder: (state, data) => {
        state.order.bidOrder = data.bidOrder
        state.order.project = data.project
        state.order.distributor = data.distributor
        state.order.contract = data.contract
        state.order.project_bid = data.project_bid
        state.order.on_hand_bid = data.on_hand_bid
        state.order.distributor_contactNum = data.distributor_contactNum
        var arr = data.produce.prod_name.split(' ')
        var container = null            
        if(arr.indexOf('(Class')){            
            for(var i = 0; i < arr.length; i++) {
                if(arr[i] == '(Class'){                    
                    container = arr[i].substring(1, arr[i].length) + ' ' + arr[i + 1].substring(0, 1)                                    
                    arr.pop()
                    arr.pop()
                    data.produce.prod_name = data.produce.prod_name.split(container)[0].split(' ')[0]                    
                    break
                }                                                          
            }
        }
        state.order.produce = data.produce    
    } 
}

export default {
    state,
    getters,
    actions,
    mutations
}
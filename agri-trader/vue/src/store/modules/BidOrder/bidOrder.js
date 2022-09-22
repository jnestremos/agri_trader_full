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
        farm_name: null,
        produce_trader: null,
        prod_type: null,
        chart_data: null
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
    on_hand_data: {
        selectedProduce: null,
        farm_produce: null,  
        contract: null,  
        trader: null,  
        produce_yields: null,  
        trader_contactNum: null,  
        produce: null,
        produce_inventories: null,
        chart_data: null
    },
    order:{
        produce: null,
        bidOrder: null,
        distributor: null,
        project: null,
        contract: null,
        project_bid: null,
        on_hand_bid: null,
        distributor_contactNum: null,
        bid_order_acc: null,
        produce_yield: null,
        refund: null,
        dist_address: null,
        delivery: null
    },
    order_history: {
        orders: null,
        project_bids: null,
        on_hand_bids: null,
        produce_trader: null,
        contracts: null,
        projects: null,
        traders: null,
        trader_contactNums: null,
        bid_order_accs: null,
        produces: null,
        deliveries: null,
        farm_produce: null,
        produce_yields: null,
        refunds: null
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
    getOnHandData(){
        return state.on_hand_data
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
    },
    getOrderHistory(){
        return state.order_history
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
    fetchOnHandDetails({ commit }, data){
        return axiosClient.get(`/bid/onhand/${data.farm_id}/${data.produce_trader_id}`)
        .then((res) => {
            console.log(res.data)
            commit('setOnHandData', res.data)
        })
    },
    sendBidOrderProject({ commit }, data){
        return axiosClient.post(`/bid/project/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    sendBidOrderOnHand({ commit }, data){
        return axiosClient.post(`/bid/onhand/add`, data)
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
    },
    approveOnHandBid({ commit }, data){
        return axiosClient.put(`/bid/onhand/${data.id}/approve`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    fetchBidHistory({ commit }, email){
        return axiosClient.get(`/bid/history/${email}`)
        .then((res) => {
            console.log(res.data)
            commit('setOrderHistory', res.data)
        })
    },
    updateProjectStatus({ commit }, id){        
        return axiosClient.put(`bid/project/${id}/approve`)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    updateOnHandStatus({ commit }, id){        
        return axiosClient.put(`bid/onhand/${id}/approve`)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    firstPayment({ commit }, data){
        var id = data.id
        delete data['id']
        if(data.bid_type == 'Project'){
            return axiosClient.post(`bid/project/${id}/payment`, data)
            .then((res) => {
                console.log(res.data)  
                commit('asd')
            })
        }
        else if(data.bid_type == 'OnHand'){
            return axiosClient.post(`bid/onhand/${id}/payment`, data)
            .then((res) => {
                console.log(res.data)  
                commit('asd')
            })
        }
    },
    finalPayment({ commit }, data){
        var id = data.id
        delete data['id']
        if(data.bid_type == 'Project'){
            return axiosClient.put(`bid/project/${id}/deliver`, data)
            .then((res) => {
              console.log(res.data)  
              commit('asd')
            })
        }
        else if(data.bid_type == 'OnHand'){
            return axiosClient.put(`bid/onhand/${id}/deliver`, data)
            .then((res) => {
              console.log(res.data)  
              commit('asd')
            })
        }
    },
    approveFirstPayment({ commit }, id){
        if(getters.getOrder().project_bid){
            return axiosClient.post(`bid/project/${id}/payment`)
            .then((res) => {
                console.log(res.data)
                commit('asd')
            })
        }
        else{
            return axiosClient.post(`bid/onhand/${id}/payment`)
            .then((res) => {
                console.log(res.data)
                commit('asd')
            })
        }

    },
    approveFinalPayment({ commit }, id){
        if(getters.getOrder().project_bid){
            return axiosClient.put(`bid/project/${id}/deliver`)
            .then((res) => {
                console.log(res.data)
                commit('asd')
            })
        }
        else{
            return axiosClient.put(`bid/onhand/${id}/deliver`)
            .then((res) => {
                console.log(res.data)
                commit('asd')
            })
        }
    },
    requestRefund({ commit }, data){        
        return axiosClient.post(`bid/project/refund/request/${data.id}`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    approveRefund({ commit }, data){
        return axiosClient.put(`project/refund/approve/${data.id}`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    },
    confirmRefund({ commit }, data){
        return axiosClient.put(`bid/project/refund/confirm/${data.id}`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    } 
}

const mutations = {
    setProjectt: (state, data) => {
        state.progress_data.trader_id = data.trader_id
        state.progress_data.chart_data = data.chart_data
        state.progress_data.trader_name = data.trader_name,
        state.progress_data.trader_contactNum = data.trader_contactNum,
        state.progress_data.project_completionDate = data.project_completionDate,
        state.progress_data.project_commenceDate = data.project_commenceDate,
        state.progress_data.trader_email = data.trader_email,
        state.progress_data.prod_type = data.prod_type
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
        state.progress_data.produce_trader = data.produce_trader
        state.progress_data.prod_name = data.prod_name 
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
            // state.progress_data.prod_name = data.prod_name 
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
        state.order.bid_order_acc = data.bid_order_acc
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
        state.order.produce_yield = data.produce_yield
        state.order.refund = data.refund
        state.order.dist_address = data.dist_address
        state.order.delivery = data.delivery
    },
    setOrderHistory: (state, data) => {
        state.order_history.orders = data.orders
        state.order_history.projects = data.projects
        state.order_history.contracts = data.contracts
        state.order_history.project_bids = data.project_bids
        state.order_history.on_hand_bids = data.on_hand_bids
        state.order_history.produces = data.produces
        state.order_history.trader_contactNums = data.trader_contactNums
        state.order_history.traders = data.traders
        state.order_history.bid_order_accs = data.bid_order_accs
        state.order_history.produce_trader = data.produce_trader
        state.order_history.deliveries = data.deliveries
        state.order_history.farm_produce = data.farm_produce
        state.order_history.produce_yields = data.produce_yields
        state.order_history.refunds = data.refunds
    },
    setOnHandData: (state, data) => {      
        state.on_hand_data.selectedProduce = data.selectedProduce
        state.on_hand_data.farm_produce = data.farm_produce
        state.on_hand_data.contract = data.contract
        state.on_hand_data.trader = data.trader
        state.on_hand_data.produce_yields = data.produce_yields
        state.on_hand_data.trader_contactNum = data.trader_contactNum
        state.on_hand_data.produce = data.produce
        state.on_hand_data.produce_inventories = data.produce_inventories
        state.on_hand_data.chart_data = data.chart_data
    } 
}

export default {
    state,
    getters,
    actions,
    mutations
}
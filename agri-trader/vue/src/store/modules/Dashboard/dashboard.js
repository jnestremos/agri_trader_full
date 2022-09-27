import axiosClient from '../../../axios'
import { format } from 'date-fns'
const state = {
    totalSales: null,
    incomeSumm: null,
    income_summary:{
        incomeSumm: null,
        bidOrders: null,
        bid_order_accounts: null,
        produce_trader: null,
        distributors: null,
        produces: null,
        contracts: null,
        projects: null,
        project_bids: null,
        on_hand_bids: null,
    }

}

const getters = {
    getTotalSales(){
        return state.totalSales
    },
    getIncomeSumm(){
        return state.incomeSumm
    },
    getIncomeSummary(){
        return state.income_summary
    }
}

const actions = {
    fetchDashboardData({ commit }){
        return axiosClient.get(`/dashboard`)
        .then((res) => {
            console.log(res.data)
            commit('setDashboardData', res.data)
        })
    },
    fetchIncomeSummary({ commit }){
        return axiosClient.get(`/income/summary`)
        .then((res) => {
            console.log(res.data)
            commit('setIncomeSummary', res.data)
        })
    }
}

const mutations = {
    setDashboardData: (state, data) => {
        state.totalSales = data.totalSales
        var dateToday = new Date().toDateString()
        var year = dateToday.split(' ')[3]
        var month = new Date().getMonth() + 1
        if(month.toString().length == 1){
            month = '0' + month.toString()
        }        
        var day = dateToday.split(' ')[2]
        var incomeSummObj = data.incomeSumm.sort((a, b) => {
            return new Date(a['created_at']) - new Date(b['created_at'])
        })
        incomeSummObj = data.incomeSumm.filter((i) => {
            console.log(`${year}-${month}-${day}`)
            console.log(i['created_at'].split('T')[0])
            console.log(`${year}-${month}-${day}` === i['created_at'].split('T')[0])
            return `${year}-${month}-${day}` === i['created_at'].split('T')[0]
        })
        state.incomeSumm = incomeSummObj
    },
    setIncomeSummary: (state, data) => {
        var incomeSumm = data.incomeSumm.sort((a, b) => {
            return new Date(a['created_at']) - new Date(b['created_at'])
        })
        console.log(incomeSumm)
        var dateSet = new Set()
        incomeSumm.forEach((i) => {
            dateSet.add(format(new Date(i.created_at), 'yyyy-MM-dd'))
        })
        var dates = [];
        var totalValues = [];
        var container = [];
        var ids = [];
        var bid_order_ids = [];
        dateSet.forEach((d) => {
            dates.push(d)
            var initValue = 0;
            var selectedRecords = incomeSumm.filter((i) => {
                return format(new Date(i.created_at), 'yyyy-MM-dd') === d
            })
            var c = [];
            selectedRecords.forEach((r) => {
                c.push(r['sum(bid_order_acc_amount)'])
            })            
            var totalValue = c.reduce(
                (prevVal, currVal) => prevVal + currVal, 
                initValue)

            totalValues.push(totalValue)
            ids.push(selectedRecords[0].id)
            bid_order_ids.push(selectedRecords[0].bid_order_id)
        })        
        totalValues.forEach((v, i) => {
            var data = 
            {
                id: ids[i],
                bid_order_id: bid_order_ids[i],
                'sum(bid_order_acc_amount)': v,
                created_at: dates[i]
            }
            container.push(data)
        })
        incomeSumm = container       
        state.income_summary.incomeSumm = incomeSumm
        state.income_summary.bidOrders = data.bidOrders
        state.income_summary.bid_order_accounts = data.bid_order_accounts
        state.income_summary.produce_trader = data.produce_trader
        state.income_summary.distributors = data.distributors
        state.income_summary.produces = data.produces
        state.income_summary.contracts = data.contracts
        state.income_summary.projects = data.projects
        state.income_summary.project_bids = data.project_bids
        state.income_summary.on_hand_bids = data.on_hand_bids
    }
}

export default {
    state,
    getters,
    mutations,
    actions
}
<template>
    <div class="incomeSummary">
        <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
            <h3>Income Summary</h3>
        </div>
        <div class="d-flex justify-content-between align-items-baseline px-3" style="width:100%; background; height:5%;">
            <div class="d-flex justify-content-between align-items-baseline h-100 w-50">
                <h5>Filters:</h5>
                <select name="type" id="type" class="form-select" style="width:150px;" @change="setFilterType($event)">
                    <option selected value='None'>None</option>
                    <option value="Project">Projects</option>
                    <option value="On Hand">On Hand</option>
                </select>
                <select name="" id="produce" class="form-select" style="width:250px;" @change="setFilterProduce($event)">
                    <option selected value="None">None</option>
                    <option v-for="(produce, index) in getIncomeSummary.produce_trader" :key="index" :value="produce.id">{{ getFilterProdName(produce) }}</option>                
                </select>
                <input type="date" name="" id="" class="form-control" style="width:150px;" v-model="filter_dateFrom">
                <input type="date" name="" id="" class="form-control" style="width:150px;" v-model="filter_dateTo">
                <button type="reset" @click="resetFilter()" class="btn btn-secondary">Reset</button>
            </div> 
            <div class="d-flex justify-content-end align-items-baseline h-100 w-50">
                <h5 v-if="filter_dateFrom && filter_dateTo" class="me-3">Computed Amount: {{ getTotal.toFixed(2) }}</h5>                
            </div>           
        </div>       
        <div class="d-flex p-3" style="width:100%; height:85%">
            <div class="w-50 h-100 me-3" style="background:lightgreen">
                <IncomeSummaryGraph style="height:100%; width:100%;" v-if="incomeSumm" :chartData="incomeSumm" label="Income" :text="filter_dateFrom + ' to ' + filter_dateTo"/>
            </div>
            <div id="table" class="w-50 h-100" :style="[filterTable.length > 10 ? {'overflow-y': 'scroll'} : {}, {'background':'lightgreen'}]">
                <table style="width:100%;" v-if="filter_dateFrom && filter_dateTo">
                    <thead>
                        <tr>
                            <th>Transaction No.</th>
                            <th>Order No.</th>
                            <th>Project Name</th>
                            <th>Produce Name</th>
                            <th>Distributor Name</th>                 
                            <th>Order Type</th>
                            <th>Amount</th>
                            <th>Date Placed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(order, index) in incomeSumm" :key="index">
                            <td>{{ order.id }}</td>
                            <td>{{ order.bid_order_id }}</td>
                            <td>{{ getProjectName(order) }}</td>
                            <td>{{ getProduceName(order) }}</td>
                            <td>{{ getDistName(order) }}</td>                            
                            <td>{{ getOrderType(order) }}</td>
                            <td>{{ order['sum(bid_order_acc_amount)'] }}</td>
                            <td>{{ order.created_at }}</td>
                        </tr>                                                                     
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { sub, format, isAfter, isEqual, isBefore } from 'date-fns'
import IncomeSummaryGraph from '../../../components/IncomeSummaryGraph.vue';

export default {
    name: 'IncomeSummary', 
    components: { IncomeSummaryGraph },   
    created(){
        this.fetchIncomeSummary()
        .then(() => {            
            this.incomeSumm = this.getIncomeSummary.incomeSumm
            this.bid_order_accounts = this.getIncomeSummary.bid_order_accounts
            var dates = [];            
            this.bid_order_accounts.forEach((a) => {                
                dates.push(new Date(a.created_at))
            })   
            dates = dates.sort((a, b) => {
                return new Date(a) - new Date(b)
            })
            this.filter_dateFrom = format(new Date(dates[0]), 'yyyy-MM-dd')
            this.filter_dateTo = format(new Date(dates[dates.length - 1]), 'yyyy-MM-dd')            
            this.readyApp()
        })        
    },
    methods :{
        ...mapActions(['readyApp', 'fetchIncomeSummary']),
        resetFilter(){
            var dates = [];            
            this.bid_order_accounts.forEach((a) => {                
                dates.push(new Date(a.created_at))
            })   
            dates = dates.sort((a, b) => {
                return new Date(a) - new Date(b)
            })
            this.filter_dateFrom = format(new Date(dates[0]), 'yyyy-MM-dd')
            this.filter_dateTo = format(new Date(dates[dates.length - 1]), 'yyyy-MM-dd')
            this.filter_type = 'None'
            this.filter_produce = 'None'
            var produce = document.getElementById('produce')
            var type = document.getElementById('type')
            type.value = 'None'
            produce.value = 'None'
            this.incomeSumm = this.getIncomeSummary.incomeSumm       
        },
        getProjectName(order){
            var bidOrderObj = this.getIncomeSummary.bidOrders.filter((o) => {
                return parseInt(order.bid_order_id) === parseInt(o.id)
            })
            var projectObj = this.getIncomeSummary.projects.filter((p) => {
                return parseInt(bidOrderObj[0].project_id) === parseInt(p.id)
            })
            var contractObj = this.getIncomeSummary.contracts.filter((c) => {
                return parseInt(projectObj[0].contract_id) === parseInt(c.id)
            })
            var projBidObj = this.getIncomeSummary.project_bids.filter((pp) => {
                return parseInt(bidOrderObj[0].id) === parseInt(pp.bid_order_id)
            })
            var onHandBidObj = this.getIncomeSummary.on_hand_bids.filter((o) => {
                return parseInt(bidOrderObj[0].id) === parseInt(o.bid_order_id)
            })
            if(projBidObj[0]){
                return contractObj[0].farm_name + ' Project'
            }
            else if(onHandBidObj[0]){
                return contractObj[0].farm_name + ' On-Hand'
            }
        },
        getProduceName(order){
            var bidOrderObj = this.getIncomeSummary.bidOrders.filter((o) => {
                return parseInt(order.bid_order_id) === parseInt(o.id)
            })
            var prodTraderObj = this.getIncomeSummary.produce_trader.filter((p) => {
                return parseInt(bidOrderObj[0].produce_trader_id) === parseInt(p.id)
            })
            var prodObj = this.getIncomeSummary.produces.filter((pp) => {
                return parseInt(prodTraderObj[0].produce_id) === parseInt(pp.id)
            })
            var arr = prodTraderObj[0].prod_name.split(' ')            
            if(arr.indexOf('(Class') != -1){
                console.log(1)
                return prodObj[0].prod_name + ' ' + prodObj[0].prod_type + ' ' + arr[arr.length - 2] + ' ' + arr[arr.length - 1]
            }
            return prodTraderObj[0].prod_name + prodObj[0].prod_type
        },
        getOrderType(order){
            var bidOrderObj = this.getIncomeSummary.bidOrders.filter((o) => {
                return parseInt(order.bid_order_id) === parseInt(o.id)
            })
            var projBidObj = this.getIncomeSummary.project_bids.filter((p) => {
                return parseInt(bidOrderObj[0].id) === parseInt(p.bid_order_id)
            })
            var onHandBidObj = this.getIncomeSummary.on_hand_bids.filter((o) => {
                return parseInt(bidOrderObj[0].id) === parseInt(o.bid_order_id)
            })
            if(projBidObj[0]){
                return 'Project Bid'
            }
            else if(onHandBidObj[0]){
                return 'On-Hand Bid'
            }
        },
        getDistName(order){
            var bidOrderObj = this.getIncomeSummary.bidOrders.filter((o) => {
                return parseInt(order.bid_order_id) === parseInt(o.id)
            })
            var distObj = this.getIncomeSummary.distributors.filter((d) => {
                return parseInt(bidOrderObj[0].distributor_id) === parseInt(d.id)
            })
            return distObj[0].distributor_firstName + ' ' + distObj[0].distributor_lastName
        },       
        getFilterProdName(produce){
            var prodObj = this.getIncomeSummary.produces.filter((pp) => {
                return parseInt(produce.produce_id) === parseInt(pp.id)
            })
            var arr = produce.prod_name.split(' ')            
            if(arr.indexOf('(Class') != -1){
                console.log(1)
                return prodObj[0].prod_name + ' ' + prodObj[0].prod_type + ' ' + arr[arr.length - 2] + ' ' + arr[arr.length - 1]
            }
            return produce.prod_name + prodObj[0].prod_type
        },
        setFilterType(e){
            this.filter_type = e.target.value
        },
        setFilterProduce(e){
            this.filter_produce = e.target.value      
        },
        filterTable(){            
            var ids = [];
            var accounts = [];
            var container = null;
            var orders = null;            
            var yearFrom = this.filter_dateFrom.split('-')[0]
            var monthFrom = this.filter_dateFrom.split('-')[1]
            var dayFrom = this.filter_dateFrom.split('-')[2]
            var yearTo = this.filter_dateTo.split('-')[0]
            var monthTo = this.filter_dateTo.split('-')[1]
            var dayTo = this.filter_dateTo.split('-')[2]
            var dateFrom = new Date(yearFrom, parseInt(monthFrom)-1, parseInt(dayFrom), 8,0,0,0)           
            var dateTo = new Date(yearTo, parseInt(monthTo)-1, parseInt(dayTo), 8,0,0,0)            
            if(this.filter_type != 'None' && this.filter_produce != 'None'){
                if(this.filter_type == "Project"){                
                    this.getIncomeSummary.project_bids.forEach((p) => {
                        ids.push(p.bid_order_id)
                    })                                
                }
                else if(this.filter_type == "On Hand"){
                    this.getIncomeSummary.on_hand_bids.forEach((p) => {
                        ids.push(p.bid_order_id)
                    })                                
                }                
                ids.forEach((id) => {                    
                    container = this.bid_order_accounts.filter((o) => {
                        return parseInt(id) === parseInt(o.bid_order_id)
                    })                    
                    container.forEach((a) => {
                        console.log(a)
                        accounts.push(a)
                    })
                })
                orders = this.getIncomeSummary.bidOrders.filter((o) => {
                    return parseInt(this.filter_produce) === parseInt(o.produce_trader_id)
                })
                var containerr = [];                
                orders.forEach((order) => {
                    container = accounts.filter((o) => {
                        return parseInt(order.id) === parseInt(o.bid_order_id)
                    })
                    container.forEach((a) => {
                        containerr.push(a)
                    })
                })
                accounts = containerr                
                accounts = accounts.filter((a) => {
                    return new Date(a.created_at).setHours(8,0,0,0) >= new Date(dateFrom).getTime() 
                    && new Date(a.created_at).setHours(8,0,0,0) <= new Date(dateTo).getTime()
                })                 
            }
            else if(this.filter_type != 'None' || this.filter_produce != 'None'){
                if(this.filter_type != 'None'){
                    if(this.filter_type == "Project"){   
                        console.log(1)             
                        this.getIncomeSummary.project_bids.forEach((p) => {
                            ids.push(p.bid_order_id)
                        })                                
                    }
                    else if(this.filter_type == "On Hand"){
                        this.getIncomeSummary.on_hand_bids.forEach((p) => {
                            ids.push(p.bid_order_id)
                        })                                
                    }
                    ids.forEach((id) => {                    
                        container = this.bid_order_accounts.filter((o) => {
                            return parseInt(id) === parseInt(o.bid_order_id)
                        })
                        container.forEach((a) => {
                            accounts.push(a)
                        })
                    })
                }
                else{
                    orders = this.getIncomeSummary.bidOrders.filter((o) => {
                        return parseInt(this.filter_produce) === parseInt(o.produce_trader_id)
                    })
                    orders.forEach((order) => {
                        container = this.bid_order_accounts.filter((o) => {
                            return parseInt(order.id) === parseInt(o.bid_order_id)
                        })
                    })
                    container.forEach((a) => {
                        accounts.push(a)
                    })                  
                }
                accounts = accounts.filter((a) => {                    
                    return new Date(a.created_at).setHours(8,0,0,0) >= new Date(dateFrom).getTime() 
                    && new Date(a.created_at).setHours(8,0,0,0) <= new Date(dateTo).getTime()
                })  
            }           
            else{
                if(this.bid_order_accounts){                    
                    accounts = this.bid_order_accounts.filter((a) => {                          
                        console.log(new Date(a.created_at).setHours(8,0,0,0) >= new Date(dateFrom).getTime())                                           
                        return new Date(a.created_at).setHours(8,0,0,0) >= new Date(dateFrom).getTime() 
                        && new Date(a.created_at).setHours(8,0,0,0) <= new Date(dateTo).getTime()
                    })
                }               
            }
            console.log(accounts)                 
            return accounts            
        },
        setGraph(){
            var yearFrom = this.filter_dateFrom.split('-')[0]
            console.log(yearFrom)
            var monthFrom = this.filter_dateFrom.split('-')[1]
            var dayFrom = this.filter_dateFrom.split('-')[2]
            var yearTo = this.filter_dateTo.split('-')[0]
            var monthTo = this.filter_dateTo.split('-')[1]
            var dayTo = this.filter_dateTo.split('-')[2]
            var incomeSumm = this.incomeSumm.filter((i) => {
                var year = i.created_at.split('-')[0]
                var month = i.created_at.split('-')[1]
                var day = i.created_at.split('-')[2]
                var date = new Date(year, parseInt(month)-1, parseInt(day),8,0,0,0)                
                var dateFrom = new Date(yearFrom, parseInt(monthFrom)-1, parseInt(dayFrom), 8,0,0,0)           
                var dateTo = new Date(yearTo, parseInt(monthTo)-1, parseInt(dayTo), 8,0,0,0)  
                return new Date(date).getTime() >= new Date(dateFrom).getTime()
                && new Date(date).getTime() <= new Date(dateTo).getTime()
            })
            this.incomeSumm = incomeSumm
        },

    },
    data(){
        return {
            filter_dateTo: null,
            filter_dateFrom: null,
            filter_type: 'None',
            filter_produce: 'None',
            incomeSumm: null,
            bid_order_accounts: null
        }
    },
    computed: {
        ...mapGetters(['getIncomeSummary']), 
        getTotal(){
            var data = this.filterTable()
            var initValue = 0;
            data.forEach((d) => {
                initValue += parseFloat(d.bid_order_acc_amount)
            })
            return initValue
        }       
    },
    watch: {
        'filter_dateFrom'(newVal){
            var year = newVal.split('-')[0]
            var month = newVal.split('-')[1]
            var day = newVal.split('-')[2]
            var dateSet = new Date(year, parseInt(month), parseInt(day), 8, 0, 0, 0)
            year = this.filter_dateTo.split('-')[0]
            month = this.filter_dateTo.split('-')[1]
            day = this.filter_dateTo.split('-')[2]
            var dateTo = new Date(year, parseInt(month), parseInt(day), 8, 0, 0, 0)
            var isDateAfter = isAfter(dateSet, dateTo)
            var isDateEqual = isEqual(dateSet, dateTo)
            if(isDateAfter || isDateEqual){
                this.filter_dateFrom = format(sub(new Date(), {
                    days: 1
                }), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(), 'yyyy-MM-dd')
            }
            this.setGraph();                   
        },
        'filter_dateTo'(newVal){
            var year = newVal.split('-')[0]
            var month = newVal.split('-')[1]
            var day = newVal.split('-')[2]
            var dateSet = new Date(year, parseInt(month), parseInt(day), 8, 0, 0, 0)
            year = this.filter_dateFrom.split('-')[0]
            month = this.filter_dateFrom.split('-')[1]
            day = this.filter_dateFrom.split('-')[2]
            var dateFrom = new Date(year, parseInt(month), parseInt(day), 8, 0, 0, 0)
            var isDateBefore = isBefore(dateSet, dateFrom)
            var isDateEqual = isEqual(dateSet, dateFrom)
            if(isDateBefore || isDateEqual){
                this.filter_dateFrom = format(sub(new Date(), {
                    days: 1
                }), 'yyyy-MM-dd')
                this.filter_dateTo = format(new Date(), 'yyyy-MM-dd')
            }     
            this.setGraph();          
        }
    }
}
</script>

<style scoped>

table, th, td{
    border-collapse: collapse;
    border-spacing: 0;  
    border: 2px solid black  
}
th, td{
    text-align:center;
    padding: 15px;
}
</style>
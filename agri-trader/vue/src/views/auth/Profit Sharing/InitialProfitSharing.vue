<template>
    <div class="initialProfitSharing">
        <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
            <h3>Profit Sharing</h3>        
        </div>
        <div class="w-100 ps-3" style="height:90%; position: relative;">
            <div class="d-flex mb-3 w-100 justify-content-between align-items-baseline">
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getProfitSharing.project">Project #: {{ getProfitSharing.project.id }}</h5>                    
                </div>
                <div class="d-flex align-items-baseline w-50">
                    <h5>Project Status: {{ getProfitSharing.project && (getProfitSharing.project.project_status_id == 4 || getProfitSharing.project.project_status_id == 5) ? getProjectStatus : getStatus }}</h5>                    
                </div>
            </div>
            <div class="d-flex mb-3 w-100 justify-content-between align-items-baseline">
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getProfitSharing.contract">Farm: {{ getProfitSharing.contract.farm_name }}</h5>                    
                </div>
                <div class="d-flex align-items-baseline w-50">
                    <h5>Date of Termination: {{ getProfitSharing.profit_sharing ? getProfitSharing.profit_sharing.created_at.split('T')[0] : dateToday }}</h5>                    
                </div>
            </div>
            <div class="d-flex mb-3 w-100 justify-content-between align-items-baseline">
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getProfitSharing.farm_owner">Farm Owner: {{ getProfitSharing.farm_owner.owner_firstName + ' ' + getProfitSharing.farm_owner.owner_lastName }}</h5>                    
                </div>
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getOverallSales || getOverallSales == 0">Total Sales: {{ getOverallSales.toFixed(2) }}</h5>                    
                </div>
            </div>
            <div class="d-flex mb-3 w-100 justify-content-between align-items-baseline">
                <div class="d-flex align-items-baseline w-50">

                </div>
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getOverallExpenses">Total Expenses: {{ getOverallExpenses.toFixed(2) }}</h5>                                     
                </div>
            </div>
            <div class="d-flex mb-4 w-100 justify-content-between align-items-baseline">
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getProfitSharing.produce">Produce: {{ getProfitSharing.produce.prod_name + ' ' + getProfitSharing.produce.prod_type }}</h5>     
                </div>
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getOwnerShare || getOwnerShare == 0" class="pb-3" style="border-bottom:2px solid black; width:40%;">Owner Share: {{ getOwnerShare.toFixed(2) }}</h5>                    
                </div>
            </div>
            <div class="d-flex mb-3 w-100 justify-content-between align-items-baseline">
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getProfitSharing.project && getProfitSharing.project.project_status_id != 5" :style="data.newShare ? {'color': 'grey'} : {}">{{ getProfitSharing.profit_sharing ? 'Agreed Profit Share:' : 'Original Profit Share:' }}</h5>                 
                </div>
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getTotal">Total: {{ getTotal }}</h5>                    
                </div>
            </div>
            <div class="d-flex mb-3 w-100 justify-content-between align-items-baseline">
                <div class="d-flex align-items-baseline w-50">
                    <h5 :style="data.newShare ? {'color': 'grey'} : {}" v-if="getProfitSharing.contract_share && getProfitSharing.contract_share.contractShare_type == 'Amount' && (getOwnerShare || getOwnerShare == 0)">Owner Amount: {{ getOwnerShare }}</h5>                 
                    <h5 :style="data.newShare ? {'color': 'grey'} : {}" v-else-if="getProfitSharing.contract_share && getProfitSharing.contract_share.contractShare_type == 'Percentage' && getProfitSharing.project && getProfitSharing.project.project_status_id != 5">Owner Percentage: {{ getOwnerPercentage }}</h5>                 
                </div>
                <div class="d-flex align-items-baseline w-50">
                    <h5 v-if="getProfit">Profit: {{ getProfit }}</h5>                    
                </div>
            </div>
            <div class="d-flex mb-5 w-100 justify-content-between align-items-baseline">
                <div class="d-flex align-items-baseline w-50">
                    
                </div>
                <div class="d-flex align-items-baseline w-50">
                    <h5>Profit Status: {{ getProfitStatus }}</h5>                    
                </div>
            </div>
            <div class="d-flex mb-5 w-100">
                <div v-if="!getProfitSharing.profit_sharing" class="p-4 me-3" style="width:45%; background:lightgray">
                    <div class="d-flex mb-3 justify-content-between align-items-baseline">
                        <div class="d-flex w-50 align-items-baseline">
                            <input type="checkbox" :disabled="!data.newShare" @click="stayTrue($event)" :checked="data.newShare" class="me-2">
                            <h5 class="me-3">Updated Profit Share</h5>
                        </div>
                        <div class="d-flex w-100 align-items-baseline">
                            <input type="text" v-model="data.ar_ownerShareType" disabled class="form-control me-2" style="width:150px" name="" id="">
                            <!-- <select class="form-select" :disabled="!(data.newShare)" name="" id="" style="width:150px">
                                <option value="Percentage">Percentage</option>
                                <option value="Amount">Amount</option>
                            </select> -->
                        </div>                    
                    </div>
                    <div class="d-flex mb-2 justify-content-between align-items-baseline">
                        <div class="d-flex w-50 align-items-baseline">                        
                            <h5 class="me-3">Trader Share: 0.00</h5>
                        </div>
                        <div class="d-flex w-100 align-items-baseline">
                            <input type="number" v-model="data.ar_ownerShare" :disabled="!(data.newShare)" class="form-control me-2" style="width:150px" name="" id="">
                            <p>(owner)</p>                       
                        </div>                    
                    </div>
                    <div class="d-flex w-100 mb-5 justify-content-between align-items-baseline">
                        <div class="d-flex w-100 align-items-baseline">                        
                            <h5 class="me-3">Owner Share: {{ data.newShare ? data.ar_ownerShare : '0.00' }}</h5>
                        </div>                                       
                    </div>
                    <div class="d-flex w-100 justify-content-between align-items-baseline">
                        <div class="d-flex w-100 align-items-baseline">                        
                            <h5 class="me-3">Remarks:</h5>
                            <textarea class="form-control" v-model="data.ar_remark" :disabled="!(data.newShare)" name="" id="" cols="30" rows="5"></textarea>
                        </div>                                       
                    </div>
                </div>
                <div class="p-4" style="width:30%; background:lightgray">
                    <div class="d-flex align-items-baseline mb-3">
                        <h5 class="me-3" style="width:40%">Payment Method:</h5>
                        <select v-if="!getProfitSharing.profit_sharing" class="form-select" name="" id="" style="width:150px" @change="setPaymentMethod($event)">
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                        <h5 v-else>{{ getProfitSharing.profit_sharing.ar_paymentMethod }}</h5>
                    </div>
                    <div v-if="data.ar_paymentMethod == 'Bank'" class="d-flex align-items-baseline mb-3">
                        <h5 class="me-3" style="width:40%">Bank Name:</h5>
                        <input type="text" v-if="!getProfitSharing.profit_sharing" v-model="data.ar_bankName" class="form-control" style="width:150px" name="" id="">
                        <h5 v-else>{{ getProfitSharing.profit_sharing.ar_bankName }}</h5>
                    </div>
                    <div v-if="data.ar_paymentMethod == 'Bank'" class="d-flex align-items-baseline mb-3">
                        <h5 class="me-3" style="width:40%">Account #:</h5>
                        <input type="text" v-if="!getProfitSharing.profit_sharing" v-model="data.ar_accNum" class="form-control" style="width:150px" name="" id="">
                        <h5 v-else>{{ getProfitSharing.profit_sharing.ar_accNum }}</h5>
                    </div>
                    <div class="d-flex align-items-baseline mb-3">
                        <h5 class="me-3" style="width:40%">Account Name:</h5>
                        <input type="text" v-if="!getProfitSharing.profit_sharing" v-model="data.ar_accName" class="form-control" style="width:150px" name="" id="">
                        <h5 v-else>{{ getProfitSharing.profit_sharing.ar_accName }}</h5>
                    </div>
                    <div class="d-flex align-items-baseline mb-3">
                        <h5 class="me-3" style="width:40%">Amount:</h5>
                        <input type="number" v-if="!getProfitSharing.profit_sharing" :value="getOwnerShare ? getOwnerShare : 0.00" disabled class="form-control" style="width:150px" name="" id="">
                        <h5 v-else>{{ getProfitSharing.profit_sharing.ar_ownerShare.toFixed(2) }}</h5>
                    </div>
                    <div class="d-flex align-items-baseline mb-3">
                        <h5 class="me-3" style="width:40%">Date Paid:</h5>
                        <input type="date" v-if="!getProfitSharing.profit_sharing" class="form-control" style="width:150px" v-model="data.ar_datePaid" name="" id="">
                        <h5 v-else>{{ getProfitSharing.profit_sharing.ar_datePaid }}</h5>
                    </div>
                </div>
            </div>
            <button v-if="!getProfitSharing.profit_sharing" style="position:absolute; bottom:3%; right:6%" class="btn btn-success" @click="submitReport()">Generate Acknowledgment Report</button>
        </div>      
    </div>
</template>

<script>
import { format } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'InitialProfitSharing',
    created(){
        if(!this.getStatusForProfit){
            this.$router.push({ path: `/projects/${this.$route.params.id}` })
        }
        else{
            this.fetchInitProfitSharing(this.$route.params.id)
            .then(() => {
                this.data.project_id = this.$route.params.id
                this.data.farm_id = this.getProfitSharing.contract.farm_id
                this.data.produce_id = this.getProfitSharing.contract.produce_id                
                this.data.farm_owner_id = this.getProfitSharing.farm_owner.id         
                if(this.getStatusForProfit == 5){
                    this.data.newShare = true
                    this.data.ar_ownerShareType = 'Amount'

                }   
                this.readyApp()
            })
            .catch((err) => {
                console.error(err)
                this.$router.push({ path: `/projects/${this.$route.params.id}` })
            })
        }        
    },
    methods: {
        ...mapActions(['readyApp', 'fetchInitProfitSharing', 'sendProfitSharing']),
        setPaymentMethod(e){
            this.data.ar_paymentMethod = e.target.value
        },
        stayTrue(e){
            e.target.checked = true
        },
        submitReport(){
            this.data.ar_totalSales = this.getOverallSales
            this.data.ar_totalExpenses = this.getTotal
            if(!this.data.ar_ownerShare){
                this.data.ar_ownerShare = this.getOwnerShare
                this.data.ar_ownerShareType = this.getProfitSharing.contract_share.contractShare_type
                if(this.data.ar_ownerShareType == 'Percentage'){
                    this.data.ar_ownerPercentage = this.getProfitSharing.contract_share.contractShare_amount / 100
                }
            }
            this.data.ar_profit = this.getProfit
            this.sendProfitSharing(this.data)
            .then(() => {
                this.$toastr.s('Acknowledgment Report Sent!')
                setTimeout(() => {
                    this.$router.push({ path: `/projects/${this.$route.params.id}` })
                }, 5000)
            })
            .catch((err) => {
                console.log(err)
                var errors = err.response.data.errors
                for(var error in errors){
                    this.$toastr.e(errors[error][0])
                } 
            })
        }
    },
    watch:{
        'data.ar_ownerShare'(newVal){
            this.data.ar_ownerShare = Math.abs(newVal)
        }
    },
    data(){
        return {
            data: {
                project_id: null,
                farm_id: null,
                farm_owner_id: null,
                produce_id: null,
                ar_totalSales: null,
                ar_totalExpenses: null,
                ar_ownerShare: null,
                ar_ownerShareType: null,
                ar_ownerPercentage: null,
                ar_profit: null,
                ar_paymentMethod: 'Cash',
                ar_accName: null,
                ar_accNum: null,
                ar_bankName: null,
                ar_datePaid: format(new Date(), 'yyyy-MM-dd'),  
                ar_dateTerminate: format(new Date(), 'yyyy-MM-dd'),
                ar_remark: null,
                newShare: false,
            },
            errors: null,            
            dateToday: format(new Date(), 'yyyy-MM-dd')        
        }
    },
    computed: {
        ...mapGetters(['getStatusForProfit', 'getProfitSharing']),
        getOverallExpenses(){
            var stockOuts = []
            var expenditures = [] 
            if(this.getProfitSharing.expenditures && this.getProfitSharing.stockOut){
                this.getProfitSharing.expenditures.forEach((e) => {
                    expenditures.push(e.exp_amount)
                })
                this.getProfitSharing.stockOut.forEach((s) => {
                    var supplyObj = this.getProfitSharing.supplies.filter((ss) => {
                        return parseInt(s.supply_id) === parseInt(ss.id)
                    })
                    stockOuts.push(supplyObj[0].supply_initialPrice * s.supply_qty)
                })
                var totalExpenses = expenditures.reduce((a, b) => a + b, 0)
                totalExpenses = stockOuts.reduce((a, b) => a + b, totalExpenses)

                }                       
            return totalExpenses
        },
        getOverallSales(){
            var sales = [];
            if(this.getProfitSharing.sales){
                this.getProfitSharing.sales.forEach((s) => {
                    sales.push(s.sale_total)
                })
                var totalSales = sales.reduce((a, b) => a + b, 0)
            }            
            return totalSales ? totalSales : 0
        },
        getStatus(){
            if(this.getStatusForProfit == 4){
                return 'Terminated Successfully'
            }
            else{
                return 'Terminated w/ Complications'
            }
        },
        getProjectStatus(){
            if(this.getProfitSharing.project){
                if(this.getProfitSharing.project.project_status_id == 4){
                    return 'Terminated Successfully'
                }
                else if(this.getProfitSharing.project.project_status_id == 5){
                    return 'Terminated w/ Complications'
                }
            }
            return null
        },
        getOwnerShare(){
            if(this.getProfitSharing.contract_share){
                if(this.data.ar_ownerShare){
                    return parseFloat(this.data.ar_ownerShare)
                }
                else if(this.getProfitSharing.profit_sharing){
                    return parseFloat(this.getProfitSharing.profit_sharing.ar_ownerShare)
                }
                else if(this.getProfitSharing.contract_share.contractShare_type == 'Percentage'){
                    return parseFloat(this.getOverallSales * (this.getProfitSharing.contract_share.contractShare_amount / 100))
                }
                else{
                    return parseFloat(this.getOverallSales - this.getProfitSharing.contract_share.contractShare_amount)
                }
            }
            return null
        },
        getOwnerPercentage(){
            if(this.getProfitSharing.contract_share){
                if(this.getProfitSharing.contract_share.contractShare_type == 'Percentage'){
                    return parseFloat(this.getProfitSharing.contract_share.contractShare_amount) + '%'
                }
                else if(this.getProfitSharing.profit_sharing && this.getProfitSharing.profit_sharing.ar_ownerPercentage){
                    return parseFloat(this.getProfitSharing.profit_sharing.ar_ownerPercentage * 100) + '%'
                }
                else if(this.getProfitSharing.profit_sharing && !this.getProfitSharing.profit_sharing.ar_ownerPercentage){
                    return parseFloat((this.getProfitSharing.profit_sharing.ar_ownerShare / this.getProfitSharing.profit_sharing.ar_totalSales) * 100) + '%'
                }
                else{
                    return parseFloat(this.getProfitSharing.contract_share.contractShare_amount)
                }
            }
            return null
        },
        getTotal(){
            return parseFloat(this.getOwnerShare + this.getOverallExpenses)
        },
        getProfit(){                                      
            return parseFloat(this.getOverallSales - (this.getOwnerShare + this.getOverallExpenses))                        
        },
        getProfitStatus(){
            if(this.getProfit > 0){
                return 'Gained'
            }
            else if(this.getProfit < 0){
                return 'Deficit'
            }
            else{
                return 'No Profit'
            }
        }
    }
}
</script>

<style>

</style>
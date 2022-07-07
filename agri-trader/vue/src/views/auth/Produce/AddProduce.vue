<template>
  <div class="addProduce" style="position:relative;">
        <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%;">
        <h3>Add Produce</h3>        
    </div>
    <div v-if="errors">
        <div v-for="(error, index) in errors" :key="index">
        {{error[0]}}
        </div>    
    </div>       
    <div class="container-fluid" style="height:60%; float:left; width:40%;">    
        <div style="width:100%; height:100%;" class="pb-5">
            <form action="" class="d-flex flex-column justify-content-between" style="height:100%;" @submit.prevent="addProduceToTrader()">
                <div class="d-flex align-items-center w-100">
                    <label for="produce_id" class="form-label me-4" style="width:15%;">Select Produce:</label>
                    <select name="produce_id" id="" @change="setProduceId($event)" class="form-select">
                        <option :value="produce.id" v-for="(produce, index) in getAllProduceOptions" :key="index">{{ produce.id }}. {{ produce.prod_name }}</option>
                    </select>
                </div>
                <div class="d-flex w-100">
                    <label for="prod_type" class="form-label me-4" style="width:15%;">Produce Type: </label>
                    <p>{{ prod_type }}</p>
                </div>
                <div class="d-flex align-items-center w-100">
                    <label for="produce_numOfGrades" class="form-label me-4" style="width:15%;">No. of Grades: </label>
                    <select name="produce_numOfGrades" id="" class="form-select" @change="setNumOfGrades($event)">
                        <option value="1" selected>1</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <div class="d-flex align-items-center w-100">
                    <label for="prod_timeOfHarvest" class="form-label me-4" style="width:15%;">Time to Harvest (in days): </label>
                    <p>{{ prod_timeOfHarvest }}</p>
                </div>                
                <div class="d-flex align-items-center w-100">
                    <label for="prod_details" class="form-label me-4" style="width:15%;">Details: </label>
                        <b-form-textarea
                            id="textarea"
                            v-model="prod_details"
                            placeholder="Details about the produce"
                            rows="3"
                            max-rows="6"
                        ></b-form-textarea>
                    <!-- <input type="text" name="prod_details" style="width:30%;" class="form-control" v-model="prod_details"> -->
                </div>                
                <input type="submit" value="Add Owner" class="btn btn-primary" style="position:absolute; right:5%; bottom:5%;">
            </form>
        </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'AddProduce',
    created(){
        this.getAllProduces()
        .then(() => {        
            this.prod_type = this.getAllProduceOptions[0].prod_type
            this.prod_timeOfHarvest = this.getAllProduceOptions[0].prod_timeOfHarvest
            this.readyApp()
        })        
    },
    computed: {
        ...mapGetters(['getAllProduceOptions'])
    },
    data(){
        return {
            produce_id: 1,
            prod_details: null,
            produce_numOfGrades: 1,
            prod_type: null,
            prod_timeOfHarvest: null,
            errors: null
        }
    },
    methods: {
        ...mapActions(['readyApp', 'addProduce', 'getAllProduces']),
        addProduceToTrader(){            
            var produce = {
                produce_id: this.produce_id,
                produce_numOfGrades: this.produce_numOfGrades,
                prod_details: this.prod_details
            }
            this.addProduce(produce)
            .then(() => {
                this.$router.push({name: 'AllProduces'})
            })
            .catch((err) => {
                console.log(err.response.data.errors)
                this.errors = err.response.data.errors
            })
        },
        setProduceId(e){
            this.produce_id = e.target.value            
            var filter = this.getAllProduceOptions.filter((produce) => {                
                return produce.id === parseInt(this.produce_id)
            })            
            this.prod_type = filter[0].prod_type
            this.prod_timeOfHarvest = filter[0].prod_timeOfHarvest
        },
        setNumOfGrades(e){
            this.produce_numOfGrades = e.target.value
        }
    }
}
</script>

<style>

</style>
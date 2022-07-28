// import axiosClient from "../../../axios";

import axiosClient from "../../../axios"



const state = {
    progress_data: {
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
        project_images: null
    }
}

const getters = {
    getProgressData(){
        return state.progress_data
    }
}

const actions = {
    fetchProjectProgress({ commit }, id){
        return axiosClient.get(`/bid/project/${id}`)
        .then((res) => {
            console.log(res.data)
            commit('setProjectt', res.data)
        })
    }
}

const mutations = {
    setProjectt: (state, data) => {
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


    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
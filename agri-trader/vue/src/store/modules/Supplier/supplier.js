import axiosClient from '../../../axios'

const state = {

}

const getters = {

}

const actions = {
    addSupplier({ commit }, data){
        console.log(data)
        return axiosClient.post(`/supplier/add`, data)
        .then((res) => {
            console.log(res.data)
            commit('asd')
        })
    }


}

const mutations = {
    asd: () => {
        console.log(1)
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
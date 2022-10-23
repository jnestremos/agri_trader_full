<template>
  <div class="progressImages" style="position:relative">
    <div class="container-fluid w-100 d-flex pe-5 justify-content-between align-items-center" style="height:10%;">
        <h3>Progress Images</h3>        
    </div>
    <div class="d-flex justify-content-center mb-3" style="width:95%; margin:0 auto">
        <h4>{{ title }}</h4>
    </div>
    <div class="mb-4" style="height:50vh; width:95%; margin:0 auto; background:grey">
      <b-carousel class="mb-2" id="carousel-1" :interval="0"
      controls indicators background="#ababab" @sliding-end="onSlideEnd" style="text-shadow: 1px 1px 2px #333; width:100%; height:100%;">      
        <b-carousel-slide v-for="(image, index) in getProjectImages.images" :key="index" style="height:50vh;">
          <template #img>
            <img class="d-block img-fluid w-100" style="width:100%; height:100%; object-fit:cover" :src="getProjectImages.images && getProjectImages.images.length > 0 && image && image.project_image_path ? require(`../../../../../public/storage/project/progress_images/${image.project_image_path}`) : ''" alt="image slot">        
          </template>     
        </b-carousel-slide>        
      </b-carousel>     
    </div>   
    <div v-if="getProjectImages.project && (getProjectImages.project.project_status_id != 4 || getProjectImages.project.project_status_id != 5)" style="width:95%; margin:0 auto;">
        <div class="d-flex align-items-baseline mb-4">
            <label for="" class="form-label me-3">Add Images:</label>
            <input type="file" @change="setImages($event)" name="" class="form-control" style="width:300px;" multiple id="">
        </div>
        <div class="d-flex align-items-baseline">
            <label for="" class="form-label me-3">Current Stage:</label>
            <label for="" class="form-label me-3"><b>{{ stage }}</b></label>
        </div>
    </div>
    <button v-if="getProjectImages.project && (getProjectImages.project.project_status_id != 4 || getProjectImages.project.project_status_id != 5)" style="position:absolute; right:5%; bottom:5%;" @click="sendPictures()" class="btn btn-primary">Add Pictures</button> 
  </div>
</template>

<script>
var formData = new FormData();
import { format } from 'date-fns'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: "ProgressImages",
    created(){
        this.fetchProjectImages(this.$route.params.id)
        .then(() => {
            var stage = null
            var dateToday = format(new Date(), 'yyyy-MM-dd')

            var floweringCheck = this.getProjectImages.project.project_floweringStart <= dateToday
            && this.getProjectImages.project.project_floweringEnd >= dateToday

            var buddingCheck = this.getProjectImages.project.project_fruitBuddingStart <= dateToday
            && this.getProjectImages.project.project_fruitBuddingEnd >= dateToday

            var devFruitCheck = this.getProjectImages.project.project_devFruitStart <= dateToday
            && this.getProjectImages.project.project_devFruitEnd >= dateToday

            var harvestableCheck = this.getProjectImages.project.project_harvestableStart <= dateToday
            && this.getProjectImages.project.project_harvestableEnd >= dateToday
            
            if(floweringCheck){
                stage = 'Flowering'
            } 
            else if(buddingCheck){
                stage = 'Fruit Budding'
            } 
            else if(devFruitCheck){
                stage = 'Developing Fruit'
            } 
            else if(harvestableCheck){
                stage = 'Harvestable'
            }  
            else{
                stage = 'No Stage'
            }
            if(stage == 'No Stage'){
                this.$router.push({ path: `projects/${this.$route.params.id}}` })
            }
            this.stage = stage        
            if(this.getProjectImages.images && this.getProjectImages.images.length > 0){
                this.title = `Picture # 1 (${this.getProjectImages.images[0].project_image_stage} Stage)`
            }   
            this.readyApp()
        })        
    },
    data(){
        return {
            stage: null,
            title: ''
        }
    },
    methods: {
        ...mapActions(['readyApp', 'fetchProjectImages', 'addProjectImage']),
        onSlideEnd(slide){
            this.title = `Picture # ${slide + 1} (${this.getProjectImages.images[slide].project_image_stage} Stage)`
        },
        setImages(e){
            const files = e.target.files
            for(var i = 0; i < files.length; i++){
                let file = files.item(i)                
                formData.append('images[]', file, file.name)
            }  
        },
        sendPictures(){
            formData.append('project_id', this.$route.params.id)
            formData.append('stage', this.stage)
            this.addProjectImage(formData)
            .then(() => {
                this.$router.push({ path: `/projects/${this.$route.params.id}` })
            })
        }
    },
    computed: {
        ...mapGetters(['getProjectImages'])
    }
}
</script>

<style>

</style>
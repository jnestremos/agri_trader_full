  <script>
  import { Line } from 'vue-chartjs/legacy'
  import { mapGetters } from 'vuex'
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    CategoryScale
  } from 'chart.js'
  
  ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)
  
  export default {
    extends: Line,
    name: 'LineChart',
    props:{
      label: {
        type: String
      },
      chartData: {
        type: Object
      },
      options: {
        type: Object
      }
    },       
    mounted(){
      console.log(this.chartData.datasets)       
       this.renderChart({
        labels: this.chartData.labels,
        datasets: [{
          label: this.label,
          data: this.chartData.datasets,
          backgroundColor: 'black',
          borderColor: 'green'
        }]
       }, 
       {
          responsive: true,
          maintainAspectRatio: false
      })
    },   
    computed:{
        ...mapGetters(['getProgressData'])
    }
  }
  </script>
  
<script>
    import { Line } from 'vue-chartjs/legacy'    
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
      name: 'TotalSalesOwner',
      props:{
        label: {
          type: String
        },
        chartData: {
          type: Array
        },
        labels: {
            type: Array
        }      
      },       
      mounted(){              
         this.renderChart({
          labels: this.labels,
          datasets: [{
            label: this.label,
            data: this.chartData,
            backgroundColor: 'black',
            borderColor: 'green'
          }]
         }, 
         {
            responsive: true,
            maintainAspectRatio: false,            
        })
      },         
    }
    </script>
    
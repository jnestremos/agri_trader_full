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
      name: 'TotalSales',
      props:{
        label: {
          type: String
        },
        chartData: {
          type: Array
        },      
      },       
      mounted(){  
        var labels = [];
        var data = [];
        this.chartData.forEach((d) => {
            labels.push(d.created_at.split('T')[0])
            data.push(d['sum(sale_total)'])
        })         
         this.renderChart({
          labels: labels,
          datasets: [{
            label: this.label,
            data: data,
            backgroundColor: 'black',
            borderColor: 'green'
          }]
         }, 
         {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Total Sales for the Month (in Php)',
                    font: {
                        size: 20,                        
                    }
                }
            }
        })
      },         
    }
    </script>
    
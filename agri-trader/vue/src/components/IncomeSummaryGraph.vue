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
      name: 'IncomeSummary',
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
        var dateNow = new Date();
        var month = dateNow.toDateString().split(' ')[1]
        var day = dateNow.toDateString().split(' ')[2]
        var year = dateNow.toDateString().split(' ')[3]
        console.log(dateNow.toDateString())        
        this.chartData.forEach((d) => {
            labels.push(d.created_at.split('T')[1].split('.')[0])
            data.push(d.bid_order_acc_amount)
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
                    text: `Income Summary for ${month} ${day}, ${year}`,
                    font: {
                        size: 20,                        
                    }
                }
            }
        })
      },         
    }
    </script>
    
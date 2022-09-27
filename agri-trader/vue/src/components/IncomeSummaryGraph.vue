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
        text: {
          type: String
        }      
      },
      watch:{
        'chartData'(){
          this.$data._chart.destroy()
          this.renderLineChart()
        }
      },
      methods:{
        renderLineChart(){
          var labels = [];
          var data = [];
          var dateNow = new Date();
          var month = dateNow.toDateString().split(' ')[1]
          var day = dateNow.toDateString().split(' ')[2]
          var year = dateNow.toDateString().split(' ')[3]
          console.log(dateNow.toDateString())    
          console.log(this.chartData)    
          this.chartData.forEach((d) => {   
              if(this.$route.name == 'IncomeSummary'){
                // labels.push(d['created_at'].split('T')[0].split('.')[0])
                labels.push(d['created_at'])
              }
              else{
                labels.push(d['created_at'].split('T')[1].split('.')[0])
              }                   
              data.push(d['sum(bid_order_acc_amount)'])
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
                      text: this.text ? `Income Summary from ${this.text}`  : `Income Summary for ${month} ${day}, ${year}`,
                      font: {
                          size: 20,                        
                      }
                  }
              }
          })
        }
      },      
      mounted(){          
        console.log(123)       
        this.renderLineChart()
      },         
    }
    </script>
    
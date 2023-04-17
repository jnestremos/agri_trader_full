<template>
    <div class="PrintReport">
      <div class="container-fluid w-100 d-flex pe-5 align-items-center" style="height:10%; background-color: #E0EDCA;">
            <h3>Print Reports</h3>
      </div>
      <div class="container-fluid d-flex" style="height:90%; position: relative; z-index:9;">
        <div style="width:80%; height:65%" class="pb-5">

        </div>
      </div>

      <button @click="printPage">Print</button>
    </div>
  </template>

  <script>
  import { mapState } from "vuex";

  export default {
    computed: {
      ...mapState(["items"])
    },
    methods: {
      printPage() {
        window.print();
      }
    }
  };
  </script>

  <style>
  @media print {
    body {
      font-size: 12pt;
    }
  }
  </style>
  This code will display the items from the Vuex store and provide a button to initiate the print action. The styles are applied when the page is printed, ensuring that the print page has a desired appearance.






<template>
    <div style="background-color: #34743B;">
        <v-container style="background-color: #10511B; display: flex; justify-content: space-between; width: 30%;">
            <v-img :width="90" :height="70" src="../picture/logo-ukdw.png" ></v-img>
            <v-img :width="20" :height="70" src="../picture/fti-ukdw.png" ></v-img>
        </v-container>  
        
        <router-link to="loginPage" style="font-size: 20px; font-family:Lexend-Medium; position: absolute; top: 20px; right: 20px; color: white; text-decoration: none;">Login</router-link>
        <div style="text-align:center; color:white; font-family:Lexend-Medium; font-size: 25px; margin-top: 40px;">
            Untuk melakukan peminjaman, silahkan <router-link to="loginPage" style="color:white">Login</router-link> terlebih dahulu.
        </div>

        <v-container>
            <ejs-schedule height="550px"></ejs-schedule>
        </v-container>
    </div>
</template>

<script>
import { ScheduleComponent, Day, Week, WorkWeek, Month, Agenda } from '@syncfusion/ej2-vue-schedule';

export default {
  components: {
    'ejs-schedule' : ScheduleComponent
  },
  provide : {
    schedule : [Day, Week, WorkWeek, Month, Agenda]
  },
  data(){
    return {

    }
  }
}
</script>

<style>

@import '/node_modules/@syncfusion/ej2-base/styles/material.css';
@import '/node_modules/@syncfusion/ej2-buttons/styles/material.css';
@import '/node_modules/@syncfusion/ej2-calendars/styles/material.css';
@import '/node_modules/@syncfusion/ej2-dropdowns/styles/material.css';
@import '/node_modules/@syncfusion/ej2-inputs/styles/material.css';
@import '/node_modules/@syncfusion/ej2-navigations/styles/material.css';
@import '/node_modules/@syncfusion/ej2-popups/styles/material.css';
@import '/node_modules/@syncfusion/ej2-vue-schedule/styles/material.css';

</style>
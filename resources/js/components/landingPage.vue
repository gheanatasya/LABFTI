<template>
  <div style="background-color: #34743B; height: 100%">
    <v-container style="background-color: #10511B; display: flex; justify-content: space-between; width: 30%;">
      <v-img :width="90" :height="70" src="../picture/logo-ukdw.png"></v-img>
      <v-img :width="20" :height="70" src="../picture/fti-ukdw.png"></v-img>
    </v-container>

    <router-link to="loginPage"
      style="font-size: 20px; font-family:Lexend-Medium; position: absolute; top: 20px; right: 20px; color: white; text-decoration: none;">Login</router-link>
    <div style="text-align:center; color:white; font-family:Lexend-Medium; font-size: 25px; margin-top: 40px;">
      Untuk melakukan peminjaman, silahkan <router-link to="loginPage" style="color:white">Login</router-link> terlebih
      dahulu.
    </div>

    <v-container>
      <ScheduleXCalendar :calendar-app="calendarApp" />
    </v-container>
  </div>
</template>

<script setup>
import { ScheduleXCalendar } from '@schedule-x/vue'
import {
  createCalendar,
  viewDay,
  viewWeek,
  viewMonthAgenda,
} from '@schedule-x/calendar'
import '@schedule-x/theme-default/dist/index.css'
import axios from 'axios';

// Do not use a ref here, as the calendar instance is not reactive, and doing so might cause issues
// For updating events, use the events service plugin
const today = new Date();
const year = today.getFullYear();
const month = String(today.getMonth() + 1).padStart(2, '0');
const day = String(today.getDate()).padStart(2,
  '0');

const formattedDate = `${year}-${month}-${day}`;
const recordRuangan =  axios.get('http://localhost:8000/api/getDataforCalender/').then((response) => {
  return response.data.peminjamanruangan;
});

console.log(recordRuangan);

const coba = recordRuangan.data;
console.log(coba);
const calendarApp = createCalendar({
  selectedDate: formattedDate,
  views: [viewDay, viewWeek, viewMonthAgenda],
  defaultView: viewWeek.name,
  events: [
    {
      id: 1,
      title: 'Event 1',
      start: '2023-12-19',
      end: '2023-12-19',
    },
    {
      id: 2,
      title: 'Event 2',
      start: '2023-12-20 12:00',
      end: '2023-12-20 13:00',
    },
  ],
})
</script>
<template>
  <div style="background-color: #34743B; height: 100%">
    <v-overlay v-model="overlay" style="background-color: white; z-index: 0">
      <v-container style="height: 660px; margin-left: 440px;">
        <v-row align-content="center" class="fill-height" justify="center">
          <v-col class="text-subtitle-1 text-center" cols="12" style="font-family: Lexend-Regular;">
            Memuat halaman
          </v-col>
          <v-col cols="6">
            <v-progress-linear color="primary" height="6" indeterminate rounded></v-progress-linear>
          </v-col>
        </v-row>
      </v-container>
    </v-overlay>

    <v-container id="logo"
      style="background-color: #10511B; display: flex; justify-content: space-between; width: 30%;">
      <v-img :width="90" :height="70" src="../picture/logo-ukdw.png"></v-img>
      <v-img :width="20" :height="70" src="../picture/fti-ukdw.png"></v-img>
    </v-container>

    <router-link to="loginPage"
      style="font-size: 20px; font-family:Lexend-Medium; position: absolute; top: 20px; right: 20px; color: white; text-decoration: none;">Login</router-link>
      <div style="text-align:center; color:white; font-family:Lexend-Medium; font-size: 25px; margin-top: 40px;">
        Peminjaman Ruangan dan Alat LAB FTI UKDW
      </div>
    <div style="text-align:center; color:white; font-family:Lexend-Regular; font-size: 20px; margin-top: 10px;">
      Untuk melakukan peminjaman, silahkan <router-link to="loginPage" style="color:white">Login</router-link> terlebih
      dahulu.
    </div>

    <v-container style="height: 700px; display: flex;">
      <div id="calendar" style="width: 1000px; float: left; margin-left: -80px; margin-right: 50px;"></div>
      <div style="float: clear; font-family:Lexend-Regular; font-size: 20px; color: white; margin-top: 30px;">
        <p style="margin-bottom: 10px;">Keterangan : </p>
        <p
          style="background-color: #fff5aa; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          SIC</p>
        <p
          style="background-color: #ffd2dc; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Byte</p>
        <p
          style="background-color: #dafff0; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Debug</p>
        <p
          style="background-color: #d2e7ff; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Firewall</p>
        <p
          style="background-color: #f0e0b4; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Gateway</p>
        <p
          style="background-color: #ab826c; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Interface</p>
        <p
          style="background-color: #b5ad8b; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Hypertext</p>
        <p
          style="background-color: #c2d49b; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Java</p>
        <p
          style="background-color: #c2f2b6; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Kernel</p>
        <p
          style="background-color: #b9f0c7; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          L.Mobile</p>
        <p
          style="background-color: #91abb8; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          L.BigData</p>
        <p
          style="background-color: #6c5b6e; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          L.AI</p>
        <p
          style="background-color: #a38c8e; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          L.MIS</p>
        <p
          style="background-color: #f5c9e2; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Alat</p>
      </div>
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
import { createEventModalPlugin } from '@schedule-x/event-modal'
import { ref } from 'vue';

const overlay = ref(true);
const today = new Date();
const year = today.getFullYear();
const month = String(today.getMonth() + 1).padStart(2, '0');
const day = String(today.getDate()).padStart(2,
  '0');

const formattedDate = `${year}-${month}-${day}`;

async function initializeCalendar() {
  try {
    const response = await axios.get('http://localhost:8000/api/getDataforCalender/');

    const allEvents = [...response.data.peminjamanruangan, ...response.data.peminjamanalat];

    const events = allEvents.map((event) => ({
      title: event.title,
      start: event.start,
      end: event.end,
      id: event.id,
      description: event.desc,
      calendarId: event.calendarId
    }));

    const calendarApp = createCalendar({
      selectedDate: formattedDate,
      views: [viewDay, viewWeek, viewMonthAgenda],
      defaultView: viewDay.name,
      events: events,
      plugins: [createEventModalPlugin()],
      calendars: {
        sic: {
          colorName: 'sic',
          lightColors: {
            main: '#f9d71c',
            container: '#fff5aa',
            onContainer: '#594800',
          },
          darkColors: {
            main: '#fff5c0',
            onContainer: '#fff5de',
            container: '#a29742',
          },
        },
        byte: {
          colorName: 'byte',
          lightColors: {
            main: '#f91c45',
            container: '#ffd2dc',
            onContainer: '#59000d',
          },
          darkColors: {
            main: '#ffc0cc',
            onContainer: '#ffdee6',
            container: '#a24258',
          },
        },
        debug: {
          colorName: 'debug',
          lightColors: {
            main: '#1cf9b0',
            container: '#dafff0',
            onContainer: '#004d3d',
          },
          darkColors: {
            main: '#c0fff5',
            onContainer: '#e6fff5',
            container: '#42a297',
          },
        },
        firewall: {
          colorName: 'firewall',
          lightColors: {
            main: '#1c7df9',
            container: '#d2e7ff',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        gateway: {
          colorName: 'gateway',
          lightColors: {
            main: '#fab802',
            container: '#f0e0b4',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        interface: {
          colorName: 'interface',
          lightColors: {
            main: '#b04105',
            container: '#ab826c',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        hypertext: {
          colorName: 'hypertext',
          lightColors: {
            main: '#b0920b',
            container: '#b5ad8b',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        java: {
          colorName: 'java',
          lightColors: {
            main: '#87c208',
            container: '#c2d49b',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        kernel: {
          colorName: 'kernel',
          lightColors: {
            main: '#37ed09',
            container: '#c2f2b6',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        mobile: {
          colorName: 'mobile',
          lightColors: {
            main: '#09e845',
            container: '#b9f0c7',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        bigdata: {
          colorName: 'bigdata',
          lightColors: {
            main: '#0771a6',
            container: '#91abb8',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        ai: {
          colorName: 'ai',
          lightColors: {
            main: '#47024f',
            container: '#6c5b6e',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        mis: {
          colorName: 'mis',
          lightColors: {
            main: '#590308',
            container: '#a38c8e',
            onContainer: '#002859',
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#dee6ff',
            container: '#426aa2',
          },
        },
        alat: {
          colorName: 'alat',
          lightColors: {
            main: '#fc0892', //bar kecil disamping kiri
            container: '#f5c9e2', //warna dalam kotak
            onContainer: '#002859', //tulisan didalam 
          },
          darkColors: {
            main: '#c0dfff',
            onContainer: '#f5a2d1',
            container: '#426aa2',
          },
        },
      }
    });

    return calendarApp;
  } catch (error) {
    console.error(error);
  }
}

initializeCalendar().then((calendarApp) => {
  calendarApp.render(document.getElementById('calendar'));
  overlay.value = false;
});
</script>
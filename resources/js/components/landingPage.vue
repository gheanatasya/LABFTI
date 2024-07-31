<template>
  <div style="background-color: #34743B; height: 100%">
    <v-container id="logo"
      style="background-color: #10511B; display: flex; justify-content: space-between; width: 30%;">
      <v-img :width="90" :height="70" src="../picture/logo-ukdw.png"></v-img>
      <v-img :width="20" :height="70" src="../picture/fti-ukdw.png"></v-img>
    </v-container>

    <router-link to="loginPage"
      style="font-size: 20px; font-family:Lexend-Medium; position: absolute; top: 20px; right: 20px; color: white; text-decoration: none;">Login</router-link>
    <div style="text-align:center; color:white; font-family:Lexend-Medium; font-size: 25px; margin-top: 40px;">
      Untuk melakukan peminjaman, silahkan <router-link to="loginPage" style="color:white">Login</router-link> terlebih
      dahulu.
    </div>

    <v-container id="calendar">
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

const today = new Date();
const year = today.getFullYear();
const month = String(today.getMonth() + 1).padStart(2, '0');
const day = String(today.getDate()).padStart(2,
  '0');

const formattedDate = `${year}-${month}-${day}`;

async function initializeCalendar() {
  try {
    const response = await axios.get('http://localhost:8000/api/getDataforCalender/');
    /* const peminjamanruangan = response.data.peminjamanruangan;

    const events = peminjamanruangan.map((ruangan) => ({
      title: ruangan.title,
      start: ruangan.start,
      end: ruangan.end,
      id: ruangan.id,
    })); */

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
});
</script>
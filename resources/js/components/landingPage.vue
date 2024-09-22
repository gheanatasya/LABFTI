<template>
  <div style="background-color: #BBDEFB; height: 100%">
    <v-overlay v-model="overlay" style="background-color: white; z-index: 0">
      <v-container style="height: 660px; margin-left: 440px;">
        <v-row align-content="center" class="fill-height" justify="center">
          <v-col class="text-subtitle-1 text-center" cols="12" style="font-family: Lexend-Regular;">
            Memuat halaman
          </v-col>
          <v-col cols="6">
            <v-progress-linear color="#0D47A1" height="6" indeterminate rounded></v-progress-linear>
          </v-col>
        </v-row>
      </v-container>
    </v-overlay>

    <v-container id="logo"
      style="background-color: #0D47A1; display: flex; justify-content: space-between; width: 30%;">
      <v-img :width="90" :height="70" src="../picture/logo-ukdw.png"></v-img>
      <v-img :width="20" :height="70" src="../picture/fti-ukdw.png"></v-img>
    </v-container>

    <div style="text-align:center; color:black; font-family:Lexend-Medium; font-size: 25px; margin-top: 40px;">
      Peminjaman Ruangan dan Alat LAB FTI UKDW
    </div>
    <div style="text-align:center; color:black; font-family:Lexend-Regular; font-size: 20px; margin-top: 10px;">
      Untuk melakukan peminjaman, silahkan <router-link to="loginPage"
        style="color:#0D47A1; font-family: Lexend-Bold">Login</router-link> terlebih
      dahulu.
    </div>

    <v-container style="height: 100%; display: flex;">
      <div id="calendar" style="width: 1000px; height: 700px; float: left; margin-left: -80px; margin-right: 50px;">
      </div>
      <div style="float: clear; font-family:Lexend-Regular; font-size: 20px; color: black; margin-top: 30px;">
        <p style="margin-bottom: 10px;">Keterangan : </p>
        <p v-for="(item, index) in dataRuangan" :key="index" @click="openInformation(item)"
          :style="{ backgroundColor: item.BackgroundColor, color: 'black', width: '120px', fontSize: '15px', paddingLeft: '20px', marginBottom: '5px', cursor: 'pointer' }">
          {{ item.Nama_ruangan }}</p>
        <p
          style="background-color: #E65100; color: black; width: 120px; font-size: 15px; padding-left: 20px; margin-bottom: 5px;">
          Alat</p>
      </div>
    </v-container>

    <v-dialog v-model="dialog" max-width="600" persistent>
      <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 700px; height: 350px;">
        <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
          {{ dataPerRuangan.Nama_ruangan }}</v-card-title>
        <v-card-text style="text-align: justify; justify-content:center; display: flex;">
          <div style="width: 40%">
            <v-img v-if="dataPerRuangan.Foto !== null" :src="'../storage/' + dataPerRuangan.Foto[0]"
              style="width: 200px; height: 200px;"></v-img>
            <v-img v-else src="../picture/no-image.png" style="width: 200px; height: 200px;"></v-img>
          </div>

          <p style="width: 60%" v-if="dataPerRuangan.Lokasi === 'Lab FTI 3' || dataPerRuangan.Lokasi === 'Fakultas'">{{
            dataPerRuangan.Nama_ruangan }} adalah salah satu ruangan yang dimiliki oleh Laboratori Fakultas Teknologi
            Informasi UKDW yang terletak pada
            {{ dataPerRuangan.Lokasi }} di Gedung Agape Lantai 3. Ruangan ini biasanya digunakan sebagai {{
              dataPerRuangan.Kategori }}. Adapun fasilitas yang terdapat pada ruangan
            ini adalah {{ dataPerRuangan.fasilitas }}.
          </p>

          <p style="width: 60%" v-else-if="dataPerRuangan.Lokasi === 'Lab FTI 2'">{{ dataPerRuangan.Nama_ruangan }}
            adalah salah satu ruangan yang dimiliki oleh Laboratori Fakultas Teknologi Informasi UKDW yang terletak pada
            {{ dataPerRuangan.Lokasi }} di Gedung Agape Lantai 2. Ruangan ini biasanya digunakan sebagai {{
              dataPerRuangan.Kategori }}. Adapun fasilitas yang terdapat pada ruangan
            ini adalah {{ dataPerRuangan.fasilitas }}.
          </p>

          <p style="width: 60%" v-else-if="dataPerRuangan.Lokasi === 'Lab FTI 4'">{{ dataPerRuangan.Nama_ruangan }}
            adalah salah satu ruangan yang dimiliki oleh Laboratori Fakultas Teknologi Informasi UKDW yang terletak pada
            {{ dataPerRuangan.Lokasi }} di Gedung Agape Lantai 4. Ruangan ini biasanya digunakan sebagai {{
              dataPerRuangan.Kategori }}. Adapun fasilitas yang terdapat pada ruangan
            ini adalah {{ dataPerRuangan.fasilitas }}.
          </p>
        </v-card-text>

        <v-card-actions style="justify-content:center;">
          <!-- <v-btn @click="filter(dataPerRuangan.Nama_ruangan)" style="color: #0D47A1; margin-left: 90px; background: none;
          text-decoration: underline; box-shadow: none; text-transform: none;
          ">Tampilkan Peminjaman {{ dataPerRuangan.Nama_ruangan }} saja.</v-btn> --> <v-btn
            style="position: absolute; bottom: 0; right: 0; margin-bottom: 20px; margin-right: 30px;"
            @click="dialog = false">Tutup</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <footerPage></footerPage>
  </div>
</template>

<script setup>
import { ScheduleXCalendar } from '@schedule-x/vue'
import {
  createCalendar,
  viewDay,
  viewWeek,
  viewMonthAgenda,
  createViewDay
} from '@schedule-x/calendar'
import '@schedule-x/theme-default/dist/index.css'
import axios from 'axios';
import { createEventModalPlugin } from '@schedule-x/event-modal'
import { ref, onMounted } from 'vue';
import footerPage from '../components/footerPage.vue'
/* import { createCalendarControlsPlugin } from '@schedule-x/calendar-controls'
 */
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
      title: event.desc,
      start: event.start,
      end: event.end,
      id: event.id,
      description: event.title,
      calendarId: event.calendarId
    }));

    const calendarApp = createCalendar({
      selectedDate: formattedDate,
      views: [viewDay, viewWeek, viewMonthAgenda],
      defaultView: viewDay.name,
      events: events,
      plugins: [createEventModalPlugin()],
      dayBoundaries: {
        start: '06:00', //kurangin sejam dari jam yang diinginkan karena pluginnya nanti nambah sejam dari jam yg diinginkan
        end: '22:00',
      },
      locale: 'id-ID',
      calendars: {
        sic: {
          colorName: 'sic',
          lightColors: {
            main: '#F44336',
            container: '#FFCDD2',
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
            main: '#9C27B0',
            container: '#E1BEE7',
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
            main: '#E91E63',
            container: '#F8BBD0',
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
            main: '#F57F17',
            container: '#FFFF8D',
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
            main: '#FFEB3B',
            container: '#FFF9C4',
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
            main: '#4CAF50',
            container: '#C8E6C9',
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
            main: '#76FF03',
            container: '#CCFF90',
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
            main: '#673AB7',
            container: '#D1C4E9',
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
            main: '#2196F3',
            container: '#E3F2FD',
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
            main: '#795548',
            container: '#D7CCC8',
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
            main: '#FF5722',
            container: '#FFCCBC',
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
            main: '#607D8B',
            container: '#CFD8DC',
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
            main: '#880E4F',
            container: '#FF80AB',
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
            main: '#E65100', //bar kecil disamping kiri
            container: '#FFB74D', //warna dalam kotak
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

const dialog = ref(false)
const dataRuangan = ref([])
const dataPerRuangan = ref([])
const openInformation = (ruangan) => {
  dialog.value = true
  dataPerRuangan.value = ruangan
  console.log(dataPerRuangan.value)
};

/* const filter = (Nama) => {} */

async function getDataRuangan() {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/ruangan');
    dataRuangan.value = response.data;
    //overlay.value = false;
    console.log(dataRuangan.value);
  } catch (error) {
    console.error('Error fetching ruangan data:', error);
  }
}

const ruanganDone = ref(false)
const kalenderDone = ref(false)

onMounted(() => {
  Promise.all([
    getDataRuangan(),
  ])
    .then(() => {
      //overlay.value = false;
      ruanganDone.value = true;
      if (kalenderDone.value === true && ruanganDone.value === true) {
        overlay.value = false;
      }
    })
    .catch((error) => {
      console.error(error);
    });
});

initializeCalendar().then((calendarApp) => {
  calendarApp.render(document.getElementById('calendar'));
  kalenderDone.value = true;
  //overlay.value = false;
});


</script>
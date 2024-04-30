<template>
  <v-app>
    <headerUser></headerUser>

    <router-link to="/berandaUser" style="width: 200px; font-size:17px; color: rgb(2,39, 10, 0.9); margin-left: 20px; margin-top: 10px; font-family: 'Lexend-Regular'"><v-icon style="font-size: 25px;">mdi-keyboard-backspace</v-icon> Beranda</router-link>

    <div style="height: 100%; display: flex;">
      <v-container style="font-family: Lexend-Regular; width: 50%; margin-left:-250px; margin-right: 30px;">
        <div>
          <v-form @submit.prevent="saveItem" ref="peminjamanForm" method="post">
            
            <div v-for="item, index in form" :key="item">
              <div
                style="font-size: 25px; font-family: Lexend-Medium; margin-top: 20px; margin-left: 300px; margin-right: 200px; margin-bottom: -80px;width: 60%">
                Peminjaman Ruangan {{ index + 1 }}</div>
              <div style="display: flex; align-items: center; grid-column: span 2; width: 110%;">
                <v-text-field type="date" label="Tanggal Pakai Awal" v-model="item.tanggalAwal" variant="outlined"
                  style="width: 280px; margin-left: 300px; margin-top: 100px; margin-right: 80px;"></v-text-field>
                <v-text-field type="date" label="Tanggal Selesai" v-model="item.tanggalSelesai" variant="outlined"
                  style="width: 300px; margin-left: -75px; margin-top: 100px;"></v-text-field>
              </div>

              <div style="display: flex; align-items: center; grid-column: span 2; width: 110%;">
                <v-text-field type="time" label="Waktu Pakai" v-model="item.waktuPakai" variant="outlined"
                  style="width: 280px; margin-left: 300px; margin-top: 5px; margin-right: 80px;"></v-text-field>
                <v-text-field type="time" label="Waktu Selesai" v-model="item.waktuSelesai" variant="outlined"
                  style="width: 300px; margin-left: -75px; margin-top: 5px;"></v-text-field>
              </div>

              <v-select v-model="item.selectedRuangan" :items="item.Ruangan" label="Ruangan" variant="outlined" clearable
                style="width: 300px; margin-left: 303px; margin-top: 8px;"></v-select>

              <div style="margin-left: 303px; margin-right: -80px;">
                <label for="isPersonal">Apakah peminjaman dilakukan untuk keperluan pribadi (belajar, skripsian, kerja
                  kelompok)?</label>
                <v-radio-group v-model="item.selectedOptionPersonal" id="isPersonal">
                  <v-row>
                    <v-col cols="auto">
                      <v-radio label="Ya" value="Ya"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Tidak" value="Tidak"></v-radio>
                    </v-col>
                  </v-row>
                </v-radio-group>

                <label for="isOrganisation">Apakah peminjaman dilakukan untuk keperluan organisasi?</label>
                <v-radio-group v-model="item.selectedOptionOrganisation" id="isOrganisation">
                  <v-row>
                    <v-col cols="auto">
                      <v-radio label="Ya" value="Ya"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Tidak" value="Tidak"></v-radio>
                    </v-col>
                  </v-row>
                </v-radio-group>

                <label for="isEksternal">Apakah peminjaman digunakan bersama dengan pihak Eksternal / Luar
                  Kampus?</label>
                <v-radio-group v-model="item.selectedOptionEksternal" id="isEksternal">
                  <v-row>
                    <v-col cols="auto">
                      <v-radio label="Ya" value="Ya"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Tidak" value="Tidak"></v-radio>
                    </v-col>
                  </v-row>
                </v-radio-group>
              </div>

              <v-textarea v-model="item.keterangan" style="margin-left: 303px; margin-right: -80px;" label="Keterangan"
                row-height="25" rows="5" variant="outlined" auto-grow shaped></v-textarea>

              <p style="margin-left: 303px; margin-right: -80px;">Tambahkan Add-on</p>

              <v-combobox v-model="item.alat" :items="item.items" label="Alat yang ingin dipinjam" multiple clearable
                variant="outlined" style="margin-left: 303px; margin-right: -80px;">
                <template v-slot:selection="{ item: selectedAlat, index }">
                  <v-chip v-if="index < 2">
                    <span>{{ selectedAlat.title }}</span>
                  </v-chip>
                  <span v-if="index === 2" class="text-grey text-caption align-self-center">
                    (+{{ item.alat.length - 2 }} others)
                  </span>
                </template>
              </v-combobox>

              <v-file-input v-if="item.selectedOptionOrganisation === 'Ya' || item.selectedOptionEksternal === 'Ya'" type="file" accept="file/pdf"
              :no-icon="true" v-model="item.dokumen"
              style="width: 505px; margin-left: 303px; margin-top: 5px;" variant="outlined" label="Surat Peminjaman"></v-file-input>

              <div
                style="display: flex; justify-content: space-between; margin-left: 320px; margin-right: 20px; margin-bottom: 50px;">
                <v-btn @click="addNewForm" id="tambah" style="margin-right: 10px; margin-left: -5px;"
                  prepend-icon=mdi-plus-circle color="primary">Tambah
                  Peminjaman</v-btn>
                <v-btn @click="removeForm(index)" id="hapus" prepend-icon="mdi-minus-circle" color="error">Hapus
                  Peminjaman</v-btn>
              </div>
            </div>
            <v-btn @click="saveItem" id="simpan" type="submit"
              style="margin-left: 430px; margin-top: -5px; border-radius: 20px; font-size: 15px; width: 250px;"
              color="primary">
              Pinjam Ruangan </v-btn>
          </v-form>
        </div>
      </v-container>

      <v-container style="font-family: Lexend-Regular; width: 45%; margin-left: 300px; margin-right: 20px;">
        <div v-for="(item, index) in form" :key="'ruangantersedia-' + index" :id="'ruangantersedia-' + index"
          style="border-radius: 10px; border: 1px solid; padding: 30px; margin-bottom: 750px;">
          <p style="font-size: 20px; font-family: Lexend-Medium; margin-bottom: 20px;">Ruangan Tersedia</p>
          <v-card
            style="border-radius: 10px; background-color: rgb(3, 138, 33, 0.3); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3)">
            <v-row align="center">
              <v-col cols="auto">
                <v-img :width="200" cover src="../picture/fti-ukdw.png"></v-img>
              </v-col>
              <v-col>
                <div class="sebelah">
                  <p>Firewall</p>
                  <p>Lab FTI 3 <v-icon icon="mdi-account"></v-icon> 8 <v-icon icon="mdi-projector"></v-icon> Proyektor
                  </p>
                  <p> Kategori : Ruang Rapat/Diskusi </p>
                  <p> Pemakaian Selanjutnya : 13.00 - 16.00 </p>
                  <router-link to="/loginPage">Lihat detail</router-link>
                </div>
              </v-col>
            </v-row>
          </v-card>
        </div>
      </v-container>
    </div>
  </v-app>
</template>

<script>
import { reactive, onMounted, watch } from 'vue';
import headerUser from '../components/headerUser.vue'
import axios from 'axios';

export default {
  name: 'berandaUser',
  components: {
    headerUser
  },
  setup() {
    const form = reactive([
      {
        dateDialogAwal: false,
        dateDialogAkhir: false,
        tanggalAwal: '',
        modal: false,
        tanggalSelesai: '',
        waktuPakai: '',
        waktuSelesai: '',
        Ruangan: [],
        selectedRuangan: '',
        isPersonal: '',
        isOrganisation: '',
        isEksternal: '',
        selectedOptionPersonal: '',
        selectedOptionEksternal: '',
        selectedOptionOrganisation: '',
        items: [],
        alat: [],
        selectedItems: '',
        keterangan: '',
        dokumen: ''
      }
    ])

    const addNewForm = () => {
      form.push({
        dateDialogAwal: false,
        dateDialogAkhir: false,
        tanggalAwal: new Date(),
        modal: false,
        tanggalSelesai: new Date(),
        waktuPakai: '',
        waktuSelesai: '',
        Ruangan: [],
        selectedRuangan: '',
        isPersonal: '',
        isOrganisation: '',
        isEksternal: '',
        selectedOptionPersonal: '',
        selectedOptionEksternal: '',
        selectedOptionOrganisation: '',
        items: [],
        alat: [],
        selectedItems: '',
        keterangan: '',
        dokumen: ''
      })
    }

    const removeForm = (index) => {
      if (form.length > 1) {
        form.splice(index, 1)
      }
    }

    const saveItem = async () => {
      await axios({
        method: 'POST',
        url: 'http://127.0.0.1:8000/api/peminjamanRuangan/',
        data: form,
        headers: {
          'Access-Control-Allow-Origin' : '*',
          'Content-Type' : 'application/json'
        }
      })

      /* axios.post("http://127.0.0.1:8000/api/peminjamanRuangan/", form) */
      /* .then(response => { */
      /*   console.log(response.data); */
      /* }) */
    }

    const fetchRuangan = () => {
      axios.get("http://127.0.0.1:8000/api/ruangan/")
        .then(response => {
          form.forEach(item => {
            item.Ruangan = response.data.map(ruangan => ruangan.Nama_ruangan);
            console.log(item.Ruangan);
          });
        })
        .catch(error => {
          console.error("Error gagal mengambil data Ruangan", error);
        });
    };

    const fetchAlat = () => {
      axios.get("http://127.0.0.1:8000/api/detail/")
        .then(response => {
          form.forEach(item => {
            item.items = response.data.map(detail_alat => detail_alat.Nama_alat);
            console.log(item.items);
          });
        })
        .catch(error => {
          console.error("Error gagal mengambil data Alat", error);
        });
    }

   /*  const fetchRuanganAfter = async () => {
      const allFilled = form.every(
        item =>
          item.tanggalAwal &&
          item.tanggalSelesai &&
          item.waktuPakai &&
          item.waktuSelesai
      );
      if (allFilled) {
        try {
          // Dapatkan jadwal peminjaman
          const promises = form.map(async item => {
            const firstdate = item.tanggalAwal;
            const seconddate = item.tanggalSelesai;
            const firsttime = item.waktuPakai;
            const secondtime = item.waktuSelesai;

            const res = await axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjaman/${firstdate}/${seconddate}/${firsttime}/${secondtime}`);
            return res.data;
          });

          const jadwalPeminjaman = await Promise.all(promises);

          // Dapatkan daftar ruangan
          const response = await axios.get("http://127.0.0.1:8000/api/ruangan/");
          const ruangan = response.data.map(ruangan => ruangan.Nama_ruangan);

          // Filter ruangan yang tersedia
          for (let i = 0; i < form.length; i++) {
            const item = form[i];
            const room = ruangan.filter(r => !jadwalPeminjaman[i].includes(r));
            item.Ruangan = room;
            console.log(item.Ruangan);
          }
        } catch (error) {
          console.error("Error gagal mengambil data Ruangan tabrakan", error);
        }
      }
    };
 */

    /* const fetchRuanganAfter = async () => {
      const allFilled = form.every(
        item =>
          item.tanggalAwal &&
          item.tanggalSelesai &&
          item.waktuPakai &&
          item.waktuSelesai
      );
      if (allFilled) {
        try {
          const response = await axios.get("http://127.0.0.1:8000/api/ruangan/");
          for (const item of form) {
            const firstdate = item.tanggalAwal;
            const seconddate = item.tanggalSelesai;
            const firsttime = item.waktuPakai;
            const secondtime = item.waktuSelesai;
            console.log(firstdate, seconddate, firsttime, secondtime);
            const res = await axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjaman/${firstdate}/${seconddate}/${firsttime}/${secondtime}`);
            const room = response.data.map(ruangan => ruangan.Nama_ruangan);
            item.Ruangan = room.filter(r => !res.data.includes(r));
            console.log(item.Ruangan);
            await new Promise(resolve => setTimeout(resolve, 10000)); // Delay for 1 second between requests
          }
        } catch (error) {
          console.error("Error gagal mengambil data Ruangan tabrakan", error);
        }
      }
    };
 */
    onMounted(() => {
      fetchRuangan();
      fetchAlat();
    });

   /*  watch(() => form, () => { */
   /*    fetchRuanganAfter(); */
   /*  }, { deep: true }); */

    return { form, addNewForm, removeForm, fetchRuangan, fetchAlat, saveItem }
  },
};
</script>
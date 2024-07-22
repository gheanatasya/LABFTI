<template>
  <v-app>
    <headerUser v-if="User_role === 'Mahasiswa' || User_role === 'Dosen' || User_role === 'Staff'" style="z-index: 1">
    </headerUser>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
      style="z-index: 1"></headerDekanat>

    <v-dialog v-if="Total_batal > 3" v-model="confirmBeforeCancel"
      style="justify-content: center; background-color: rgb(2, 39, 10, 0.7); z-index: 0;" persistent max-width="500">
      <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 500px; height: 250px;">
        <v-card-title style="font-family: 'Lexend-Medium'; text-align: center; margin-top: 20px;">
          Batas Maksimal Pembatalan Peminjaman
        </v-card-title>
        <v-card-text style="text-align: center;">
          Mohon maaf, anda melewati batas maksimal pembatalan peminjaman!
          Peminjaman tidak dapat dilakukan hingga sebulan kedepan.
          Atas perhatiannya kami ucapkan terima kasih.
        </v-card-text>
        <v-card-actions style="position: absolute; top: 0; right: 0; margin-right: -15px;">
          <v-btn @click="navigateToBeranda"><v-icon style="font-size: 30px;">mdi-close-circle</v-icon></v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-else v-model="confirmBefore"
      style="justify-content: center; background-color: rgb(2, 39, 10, 0.7); z-index: 0;" persistent max-width="500">
      <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 500px; height: 250px;">
        <v-card-title style="font-family: 'Lexend-Medium'; text-align: center; margin-top: 20px;">
          Apakah ingin meminjam ruangan <br>secara manual atau dengan <br>bantuan Rekomendasi Ruangan?
        </v-card-title>
        <v-card-text>
          <v-radio-group v-model="selectedConfirmBefore" id="confirmBefore" style="margin-left: 40px; margin-top: 5px;">
            <v-row>
              <v-col cols="auto">
                <v-radio label="Manual" value="true" @change="closeDialog"></v-radio>
              </v-col>
              <v-col cols="auto">
                <v-radio label="Rekomendasi Ruangan" value="false" @change="navigateToRekomendasi"></v-radio>
              </v-col>
            </v-row>
          </v-radio-group>
        </v-card-text>
        <v-card-actions style="position: absolute; top: 0; right: 0; margin-right: -15px;">
          <v-btn @click="navigateToBeranda"><v-icon style="font-size: 30px;">mdi-close-circle</v-icon></v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <router-link to="/berandaUser"
      style="width: 200px; font-size:17px; color: rgb(2,39, 10, 0.9); margin-left: 20px; margin-top: 70px; font-family: 'Lexend-Regular'"><v-icon
        style="font-size: 25px;">mdi-keyboard-backspace</v-icon> Beranda</router-link>

    <div style="height: 100%; display: flex;">
      <v-container style="font-family: Lexend-Regular; width: 50%; margin-left:-250px; margin-right: 30px;">
        <div>
          <v-form @submit.prevent="saveItem" ref="peminjamanForm" method="post">
            <div v-for="item, index in form" :key="item">
              <div
                style="font-size: 25px; font-family: Lexend-Medium; margin-top: 20px; margin-left: 300px; margin-right: 200px; margin-bottom: -80px;width: 60%">
                Peminjaman Ruangan {{ index + 1 }}</div>
              <div style="display: flex; align-items: center; grid-column: span 4; width: 130%;">
                <v-text-field type="datetime-local" label="Tanggal Pakai Awal" v-model="item.tanggalAwal"
                  variant="outlined"
                  style="width: 280px; margin-left: 300px; margin-top: 100px; margin-right: 80px;"></v-text-field>
                <v-text-field type="datetime-local" label="Tanggal Selesai" v-model="item.tanggalSelesai"
                  variant="outlined"
                  style="width: 300px; margin-left: -75px; margin-top: 100px; margin-right: 20px;"></v-text-field>
                <v-btn
                  @click="availableRoom(item.tanggalAwal, item.tanggalSelesai), fetchAlat(item.tanggalAwal, item.tanggalSelesai)"
                  style="width: 120px; margin-left: 10px; margin-top: 80px; font-size: 11px; border-radius: 20px; margin-right:20px; padding-left: 50px; padding-right: 50px;"
                  color="primary">
                  Cek ruangan</v-btn>
              </div>

              <v-select v-model="item.selectedRuangan" :items="item.Ruangan" label="Ruangan" variant="outlined"
                clearable style="width: 300px; margin-left: 303px; margin-top: 8px;"></v-select>

              <div style="margin-left: 303px; margin-right: -80px;">
                <label for="isPersonal">Apakah peminjaman dilakukan untuk keperluan pribadi (belajar, skripsian, kerja
                  kelompok)?</label>
                <v-radio-group v-model="item.selectedOptionPersonal" id="isPersonal">
                  <v-row>
                    <v-col cols="auto">
                      <v-radio label="Ya" value="True"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Tidak" value="False"></v-radio>
                    </v-col>
                  </v-row>
                </v-radio-group>

                <label for="isOrganisation">Apakah peminjaman dilakukan untuk keperluan organisasi?</label>
                <v-radio-group v-model="item.selectedOptionOrganisation" id="isOrganisation">
                  <v-row>
                    <v-col cols="auto">
                      <v-radio label="Ya" value="True"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Tidak" value="False"></v-radio>
                    </v-col>
                  </v-row>
                </v-radio-group>

                <label for="isEksternal">Apakah peminjaman digunakan bersama dengan pihak Eksternal / Luar
                  Kampus?</label>
                <v-radio-group v-model="item.selectedOptionEksternal" id="isEksternal">
                  <v-row>
                    <v-col cols="auto">
                      <v-radio label="Ya" value="True"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Tidak" value="False"></v-radio>
                    </v-col>
                  </v-row>
                </v-radio-group>
              </div>

              <v-textarea v-model="item.keterangan" style="margin-left: 303px; margin-right: -90px;" label="Keterangan"
                row-height="25" rows="5" variant="outlined" auto-grow shaped></v-textarea>

              <p style="margin-left: 303px; margin-right: -80px;">Tambahkan Add-on</p>

              <div v-for="(alatItem, alatIndex) in item.alat" :key="alatIndex"
                style="display: flex; align-items: center; grid-column: span 4; width: 145%;">
                <v-combobox v-model="alatItem.nama" :items="item.items" label="Alat yang ingin dipinjam" clearable
                  variant="outlined" style="margin-left: 303px; margin-right: -5px; width: 50px;">
                </v-combobox>

                <div>
                <v-text-field type="number" label="Jumlah" v-model="alatItem.jumlahPinjam" variant="outlined" clearable
                  v-if="alatItem.maxValue = item.itemsAll.find(item => item.NamaAlat === alatItem.nama)" min="0"
                  :max="alatItem.maxValue.Jumlah_ketersediaan"
                  style="margin-right: -35px; margin-left: 10px; margin-top: 10px;"></v-text-field>

                <p v-if="maksimalPinjam = item.itemsAll.find(item => item.NamaAlat === alatItem.nama)"
                  style="margin-top: -15px; margin-left: 10px;">
                  Jumlah tersedia : {{ maksimalPinjam.Jumlah_ketersediaan }}
                </p>
                </div>

                <v-btn @click="tambahAlat(index)" style="font-size: 18px; margin-left: 45px; margin-right: 90px; border-radius: 50%; width: 60px; height: 60px; background-color: none; box-shadow: none;
                margin-top: -18px;"><v-icon>mdi-plus-circle</v-icon></v-btn>

                <v-btn @click="hapusAlat(index, alatIndex)" style="font-size: 18px; margin-left: -90px; margin-right: 100px; border-radius: 50%; width: 60px; height: 60px; background-color: none; box-shadow: none;
                margin-top: -18px;">
                  <v-icon>mdi-minus-circle</v-icon></v-btn>
              </div>

              <v-file-input v-if="item.selectedOptionOrganisation === 'True' || item.selectedOptionEksternal === 'True'"
                type="file" accept="file/pdf" :no-icon="true" v-model="item.dokumen"
                style="width: 505px; margin-left: 303px; margin-top: 5px;" variant="outlined" label="Surat Peminjaman"
                ref="dokumenPendukung" :id="'dokumen-' + index" @change="handleFileChange(index)"></v-file-input>

              <div
                style="display: flex; justify-content: space-between; margin-left: 320px; margin-right: 20px; margin-bottom: 50px; margin-top: 20px;">
                <v-btn @click="addNewForm" id="tambah" style="margin-right: 10px; margin-left: -5px;"
                  prepend-icon=mdi-plus-circle color="primary">Tambah
                  Peminjaman</v-btn>
                <v-btn @click="removeForm(index)" id="hapus" prepend-icon="mdi-minus-circle" color="error">Hapus
                  Peminjaman</v-btn>
              </div>
            </div>
            <v-btn @click="saveItem" id="simpan"
              style="margin-left: 430px; margin-top: -5px; border-radius: 20px; font-size: 15px; width: 250px;"
              color="primary">
              Pinjam Ruangan </v-btn>
          </v-form>
        </div>
      </v-container>

      <v-container
        style="font-family: Lexend-Regular; width: 45%; margin-left: 300px; margin-right: 20px; margin-bottom: 0px; height: 600px;">
        <div v-for="(item, index) in form" :key="'ruangantersedia-' + index" :id="'ruangantersedia-' + index"
          style="border-radius: 10px; border: 1px solid; padding: 30px; margin-bottom: 750px; height: 600px; overflow-y: auto; margin-bottom: 320px;">
          <p v-if="item.detailRuangan.length > 0"
            style="font-size: 25px; font-family: Lexend-Medium; margin-bottom: 20px;">
            Ruangan Tersedia ( {{
              item.detailRuangan.length }} )</p>
          <p v-else
            style="font-size: 25px; font-family: Lexend-Medium; margin-bottom: 20px; text-align: center; margin-top: 200px;">
            Silahkan masukkan tanggal penggunaan ruangan untuk melihat
            ruangan yang tersedia.
          </p>
          <v-card v-for="(ruangan, i) in item.detailRuangan" :key="i"
            style="border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); margin-bottom: 20px;">
            <v-hover>
              <template v-slot:default="{ isHovering, props }">
                <div v-bind="props"
                  :style="{ backgroundColor: isHovering ? 'rgba(3, 138, 33, 0.4)' : 'rgb(3, 138, 33, 0.3)' }">
                  <v-row align="center">
                    <v-col cols="auto">
                      <v-img :width="200" cover src="../picture/fti-ukdw.png"></v-img>
                    </v-col>
                    <v-col>
                      <div class="sebelah">
                        <p>{{ ruangan.Nama_ruangan }}</p>
                        <p>{{ ruangan.Lokasi }} <v-icon icon="mdi-account"></v-icon> {{ ruangan.Kapasitas }} <v-icon
                            icon="mdi-projector"></v-icon> Proyektor
                        </p>
                        <p> {{ ruangan.Kategori }} </p>
                        <p> Pemakaian Selanjutnya : 13.00 - 16.00 </p>
                        <router-link to="/ruangan">Lihat detail</router-link>
                      </div>
                    </v-col>
                  </v-row>
                </div>
              </template>
            </v-hover>

          </v-card>
        </div>
      </v-container>
    </div>

    <footerPage></footerPage>
  </v-app>
</template>

<script>
import { reactive, onMounted, ref, computed } from 'vue';
import headerUser from '../components/headerUser.vue'
import footerPage from '../components/footerPage.vue'
import axios from 'axios';
import headerAdmin from './headerAdmin.vue'
import headerDekanat from './headerDekanat.vue'
import headerSuperAdmin from './headerSuperAdmin.vue'

export default {
  name: 'berandaUser',
  components: {
    headerUser,
    footerPage,
    headerAdmin,
    headerDekanat,
    headerSuperAdmin
  },
  setup() {
    const form = reactive([
      {
        dateDialogAwal: false,
        dateDialogAkhir: false,
        tanggalAwal: '',
        modal: false,
        tanggalSelesai: '',
        waktuPakai: null,
        waktuSelesai: null,
        Ruangan: [],
        selectedRuangan: '',
        isPersonal: '',
        isOrganisation: '',
        isEksternal: '',
        selectedOptionPersonal: '',
        selectedOptionEksternal: '',
        selectedOptionOrganisation: '',
        items: [],
        itemsAll: [],
        alat: reactive([{
          nama: '',
          jumlahPinjam: 0,
          maxValue: null,
        }]),
        selectedItems: '',
        keterangan: '',
        dokumen: null,
        detailRuangan: [],
      }
    ])

    const addNewForm = () => {
      form.push({
        dateDialogAwal: false,
        dateDialogAkhir: false,
        tanggalAwal: '',
        modal: false,
        tanggalSelesai: '',
        waktuPakai: null,
        waktuSelesai: null,
        Ruangan: [],
        selectedRuangan: '',
        isPersonal: '',
        isOrganisation: '',
        isEksternal: '',
        selectedOptionPersonal: '',
        selectedOptionEksternal: '',
        selectedOptionOrganisation: '',
        items: [],
        itemsAll: [],
        alat: reactive([{
          nama: '',
          jumlahPinjam: 0,
          maxValue: null,
        }]),
        selectedItems: '',
        keterangan: '',
        dokumen: null,
        detailRuangan: [],
      })
    }

    const removeForm = (index) => {
      if (form.length > 1) {
        form.splice(index, 1)
      }
    }

    const saveItem = async () => {
      const savedItems = [];
      const dataSend = [];

      const UserID = localStorage.getItem('UserID');

      //console.log(form);
      for (let i = 0; i < form.length; i++) {
        if (form[i].alat.length > 0){
          for (let j = 0; j < form[i].alat.length; j++) {
            if (form[i].alat[j].jumlahPinjam > form[i].alat[j].maxValue.Jumlah_ketersediaan) {
              alert('Jumlah pinjam melebihi jumlah ketersediaan alat!');
              return;
            }
          }
        }

        const FORMDATA = new FormData();
        const file = document.querySelector('#dokumen-' + i);
        const today = new Date();
        const formattedDate = `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;

        if (file !== null) {
          FORMDATA.append('dokumen', file.files[0]);
          FORMDATA.append('UserID', UserID);
          FORMDATA.append('Tanggal_pinjam', formattedDate);
        }

        const dataToSave = {
          tanggalSelesai: form[i].tanggalSelesai,
          tanggalAwal: form[i].tanggalAwal,
          selectedRuangan: form[i].selectedRuangan,
          selectedOptionPersonal: form[i].selectedOptionPersonal,
          selectedOptionEksternal: form[i].selectedOptionEksternal,
          selectedOptionOrganisation: form[i].selectedOptionOrganisation,
          alat: form[i].alat,
          keterangan: form[i].keterangan,
          dokumen: null,
          UserID: UserID
        };

        //dataSend.push(dataToSave);
        //console.log(dataSend);

        try {
          const response = await axios({
            method: 'POST',
            url: 'http://localhost:8000/api/peminjamanRuangan/',
            data: dataToSave,
            headers: {
              'Access-Control-Allow-Origin': '*',
              'Content-Type': 'multipart/form-data',
            },
          });

          const peminjamanruanganid = response.data.peminjaman_ruangan_bridge[0]['Peminjaman_Ruangan_ID'];
          FORMDATA.append('peminjamanruanganid', peminjamanruanganid);

          if (response.data.peminjaman_alat_bridge.length > 0) {
            for (let j = 0; j < response.data.peminjaman_alat_bridge.length; j++) {
              const peminjamanalatid = response.data.peminjaman_alat_bridge[j]['Peminjaman_Alat_ID'];
              FORMDATA.append('peminjamanalatid' + j, peminjamanalatid);
            }
            FORMDATA.append('totalalat', response.data.peminjaman_alat_bridge.length);
          }
          console.log(peminjamanruanganid);

          //if (file !== null) {
          const response2 = await axios({
            method: 'POST',
            url: 'http://localhost:8000/api/dokumen/',
            data: FORMDATA,
            headers: {
              'Access-Control-Allow-Origin': '*',
              'Content-Type': 'multipart/form-data',
            },
          })
          //}
          //savedItems.push(response.data);
          console.log('Peminjaman saved successfully: response2', response2.data);
          console.log('Peminjaman saved successfully:', response.data);
        } catch (error) {
          console.error('Error menyimpan data peminjaman ruangan', error);
        }
      }
    }

    const fetchAlat = async (tanggalAwal, tanggalSelesai) => {
      if (tanggalAwal && tanggalSelesai) {
        try {
          const response = await axios.get(
            `http://127.0.0.1:8000/api/peminjamanAlat/jadwalAlat/${tanggalAwal}/${tanggalSelesai}`
          );

          const alat = response.data.daftarAlatfix;
          let namaAlat = [];
          let jumlahAlat = [];
          console.log(alat);

          for (let i = 0; i < alat.length; i++) {
            namaAlat.push(alat[i].NamaAlat);
            jumlahAlat.push(alat[i].Jumlah_ketersediaan);
          }

          const index = form.findIndex(item => item.tanggalAwal === tanggalAwal && item.tanggalSelesai === tanggalSelesai);
          if (index > -1) {
            form[index].items = namaAlat;
            form[index].itemsAll = alat;
          } else {
            console.warn("Could not find matching form item for fetched alat");
          }
        } catch (error) {
          console.error("Error gagal mengambil data Alat", error);
        }
      }
    }

    const availableRoom = async (tanggalAwal, tanggalSelesai) => {
      if (tanggalAwal && tanggalSelesai) {
        try {
          const response = await axios.get(
            `http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjaman/${tanggalAwal}/${tanggalSelesai}`
          );
          const availableRuangan = response.data.availableRoom;
          const roomdetail = response.data.detailRuangan;

          console.log(availableRuangan);
          console.log(roomdetail);

          const index = form.findIndex(item => item.tanggalAwal === tanggalAwal && item.tanggalSelesai === tanggalSelesai);
          if (index > -1) {
            form[index].Ruangan = availableRuangan;
            form[index].detailRuangan = roomdetail;
          } else {
            console.warn("Could not find matching form item for fetched available rooms.");
          }

        } catch (error) {
          console.error("Error fetching available rooms:", error);
        }
      }
    };

    const tambahAlat = (index) => {
      console.log('Index:', index);
      console.log('Form length:', form.length);
      console.log('Form', form[index])

      const alat = form[index].alat;
      const newAlat = {
        nama: '',
        jumlahPinjam: 0,
      };

      alat.push(newAlat);
    };

    const hapusAlat = (index, alatIndex) => {
      if (form.length > 0 && form[index].alat.length > 1) {
        form[index].alat.splice(alatIndex, 1);
        console.log('Form', form[index]);
      } else {
        // kalau tinggal 1 peminjaman
        console.warn('Tidak dapat menghapus alat atau form');
      }
    }

    return { form, addNewForm, removeForm, fetchAlat, saveItem, availableRoom, tambahAlat, hapusAlat };
  },
  data() {
    return {
      confirmBefore: true,
      confirmBeforeCancel: true,
      selectedConfirmBefore: true,
      User_role: localStorage.getItem('User_role'),
      Total_batal: localStorage.getItem('Total_batal'),
    }
  },
  methods: {
    closeDialog() {
      this.confirmBefore = false;
    },
    navigateToRekomendasi() {
      this.confirmBefore = false;
      this.$router.push('/rekomendasiRuangan');
    },
    navigateToBeranda() {
      this.confirmBefore = false;
      this.$router.push('/berandaUser')
    }
  }
};
</script>
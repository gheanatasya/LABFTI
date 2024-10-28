<template>
  <v-app>
    <headerUser v-if="User_role === 'Mahasiswa' || User_role === 'Dosen' || User_role === 'Staff'"
      style="z-index: 1; position: fixed; width: 100%;">
    </headerUser>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'"
      style="z-index: 1; position: fixed; width: 100%;">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1; position: fixed; width: 100%;"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
      style="z-index: 1; position: fixed; width: 100%;"></headerDekanat>

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
      style="width: 200px; font-size:17px; color: #0D47A1; margin-left: 20px; margin-top: 70px; font-family: 'Lexend-Regular'"><v-icon
        style="font-size: 25px;">mdi-keyboard-backspace</v-icon>Beranda</router-link>

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
                  variant="outlined" style="width: 280px; margin-left: 300px; margin-top: 100px; margin-right: 80px;">
                  <template v-slot:label>
                    Tanggal Pakai Awal <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon>
                  </template>
                </v-text-field>
                <v-text-field type="datetime-local" label="Tanggal Selesai" v-model="item.tanggalSelesai"
                  variant="outlined" style="width: 300px; margin-left: -75px; margin-top: 100px; margin-right: 20px;">
                  <template v-slot:label>
                    Tanggal Selesai <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon>
                  </template>
                </v-text-field>
                <v-btn :loading="item.loading" v-if="User_role === 'Mahasiswa' || User_role === 'Petugas'"
                  @click="availableRoom(item.tanggalAwal, item.tanggalSelesai, index), fetchAlat(item.tanggalAwal, item.tanggalSelesai, index)"
                  style="text-transform: none; width: 120px; margin-left: 10px; margin-top: 80px; font-size: 12px; border-radius: 20px; margin-right:20px; padding-left: 50px; padding-right: 50px;"
                  color="#0D47A1">
                  Cek Ruangan</v-btn>

                <v-btn :loading="item.loading" v-else
                  @click="availableRoomDosen(item.tanggalAwal, item.tanggalSelesai, index), fetchAlat(item.tanggalAwal, item.tanggalSelesai, index)"
                  style="text-transform: none; width: 120px; margin-left: 10px; margin-top: 80px; font-size: 12px; border-radius: 20px; margin-right:20px; padding-left: 50px; padding-right: 50px;"
                  color="#0D47A1">
                  Cek Ruangan</v-btn>
              </div>

              <!-- <v-select v-model="item.selectedRuangan" :items="item.Ruangan" label="Ruangan" variant="outlined"
                clearable style="width: 300px; margin-left: 303px; margin-top: 8px;"><template v-slot:label>
                  Ruangan <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon>
                </template></v-select> -->

              <div style="margin-left: 303px; margin-right: -80px;">
                <label for="keperluan">
                  Keperluan peminjaman untuk <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon></label>
                <v-radio-group v-model="item.selectedOption">
                  <v-row>
                    <v-col cols="auto">
                      <v-radio label="Personal" value="personalTrue"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Organisasi" value="organisationTrue"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Eksternal" value="eksternalTrue"></v-radio>
                    </v-col>
                  </v-row>
                </v-radio-group>
                <!-- <label for="isPersonal">
                  Apakah peminjaman dilakukan untuk keperluan pribadi (rapat, belajar, skripsian, kerja
                  kelompok)? <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon></label>
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

                <label for="isOrganisation">Apakah peminjaman dilakukan untuk keperluan organisasi (contoh: BPMFTI, BEMFTI)? <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon></label>
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
                  Kampus? <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon></label>
                <v-radio-group v-model="item.selectedOptionEksternal" id="isEksternal">
                  <v-row>
                    <v-col cols="auto">
                      <v-radio label="Ya" value="True"></v-radio>
                    </v-col>
                    <v-col cols="auto">
                      <v-radio label="Tidak" value="False"></v-radio>
                    </v-col>
                  </v-row>
                </v-radio-group> -->
              </div>

              <v-textarea v-model="item.keterangan" style="margin-left: 303px; margin-right: -90px;" label="Keterangan"
                row-height="25" rows="5" variant="outlined" auto-grow shaped><template v-slot:label>
                  Alasan Peminjaman<v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon>
                </template></v-textarea>

              <p style="margin-left: 303px; margin-right: -80px;">Tambahkan Add-on (opsional)</p>

              <div v-for="(alatItem, alatIndex) in item.alat" :key="alatIndex"
                style="display: flex; align-items: center; grid-column: span 4; width: 145%;">
                <v-combobox v-model="alatItem.nama" :items="item.items" label="Alat yang ingin dipinjam" clearable
                  variant="outlined" style="margin-left: 303px; margin-right: -5px; width: 50px;">
                </v-combobox>

                <div>
                  <v-text-field type="number" label="Jumlah" v-model="alatItem.jumlahPinjam" variant="outlined"
                    clearable v-if="alatItem.maxValue = item.itemsAll.find(item => item.NamaAlat === alatItem.nama)"
                    min="0" :max="alatItem.maxValue.Jumlah_ketersediaan"
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

              <v-file-input v-if="item.selectedOption !== 'personalTrue'" type="file" accept="file/pdf"
                :prepend-inner-icon="'mdi-paperclip'" prepend-icon="" v-model="item.dokumen"
                style="width: 505px; margin-left: 303px; margin-top: 5px;" variant="outlined" label="Surat Peminjaman"
                @change="handleDokumen(index)" ref="dokumenPendukung" :id="'dokumen-' + index">
                <template v-slot:label>
                  Surat Peminjaman <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon>
                </template>
              </v-file-input>

              <div
                style="margin-left: 380px; margin-right: 0px; margin-bottom: 50px; margin-top: 20px; text-align: center;">
                <v-row>
                  <v-col>
                    <v-btn @click="addNewForm(index)" id="tambah"
                      style="margin-right: 10px; margin-left: -5px; text-transform: none" prepend-icon=mdi-plus-circle
                      color="grey">Tambah Form</v-btn>
                    <v-btn @click="removeForm(index)" id="hapus" prepend-icon="mdi-minus-circle" color="error"
                      style="text-transform: none">Hapus
                      Form</v-btn>
                  </v-col>
                </v-row>
              </div>
            </div>

            <v-btn @click="dialogTambahNomor = true" id="simpan"
              style="margin-left: 430px; margin-top: -5px; border-radius: 20px; font-size: 15px; width: 250px; text-transform: none;"
              color="#01579B">
              Selanjutnya</v-btn>
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
                <div v-bind="props" :style="{ backgroundColor: isHovering ? '#BBDEFB' : '#E3F2FD' }">
                  <v-row align="center">
                    <v-col cols="auto">
                      <v-img :width="200" :height="150" cover :src="'../storage/' + ruangan.Foto[0]"></v-img>
                    </v-col>
                    <v-col>
                      <div class="sebelah">
                        <p>{{ ruangan.Nama_ruangan }}</p>
                        <p><v-icon icon="mdi-map-marker"></v-icon> {{ ruangan.Lokasi }} <v-icon
                            icon="mdi-account"></v-icon> {{ ruangan.Kapasitas }}
                        </p>
                        <p> {{ ruangan.Kategori }} </p>
                        <p style="text-decoration: underline; cursor: pointer; color: #0D47A1"
                          @click="tampilkanDetail(ruangan.Nama_ruangan, ruangan.Lokasi, ruangan.Kapasitas, ruangan.Kategori, ruangan.fasilitas)">
                          Lihat selengkapnya >>></p>
                        <v-btn v-if="item.sudahDipilih === false" @click="pilihRuangan(ruangan.Nama_ruangan, index)" style="background-color: #0D47A1; color: white; text-transform: none; margin-top: 5px; font-size: 15px;">Pilih</v-btn>
                        <v-btn v-if="item.sudahDipilih === true && ruangan.Nama_ruangan === item.selectedRuangan" @click="batalkanRuangan(index)" style="text-transform: none; border: 3px solid #0D47A1;  box-shadow: none; background-color: none; color: #0D47A1; margin-top: 5px; font-size: 15px;">Batal</v-btn>
                      </div>
                    </v-col>
                  </v-row>
                </div>
              </template>
            </v-hover>
          </v-card>
        </div>
      </v-container>

      <v-dialog style="justify-content:center;" v-model="dialogTambahNomor" persistent max-width="500">
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 500px;">
          <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;"></v-card-title>
          <v-card-text>
            <p>Nomor Handphone Yang Dapat Dihubungi</p>
            <v-text-field label="No. Handphone" v-model="Nohp" variant="outlined" clearable><template v-slot:label>
                No. Handphone <v-icon style="color: red; font-size: 15px;">mdi-asterisk</v-icon>
              </template></v-text-field>
          </v-card-text>
          <v-card-actions style="justify-content:center;">
            <v-btn @click="dialogTambahNomor = false"
              style="position: absolute; top: 0; right: 0; margin-top: 10px;"><v-icon
                style="font-size: 30px;">mdi-close-circle</v-icon></v-btn>
            <v-btn @click="saveItem()" id="simpan" :loading="loading"
              style="background-color: #01579B; color: white; border-radius: 20px; font-size: 15px; text-transform: none; width: 200px;">
              Pinjam Ruangan </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-overlay v-model="dialog">
        <v-card
          style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px; height: 250px; margin-left: 550px; margin-top: 200px;">
          <v-card-title style="font-family: 'Lexend-Medium'; text-align: center; margin-top: 20px;">
            <div class="py-1 text-center">
              <v-icon class="mb-6" color="success" icon="mdi-check-circle-outline" size="120"></v-icon>
              <div class="text-h5 font-weight-bold">Peminjaman Berhasil</div>
            </div>
          </v-card-title>
          <v-card-text>
          </v-card-text>
          <v-card-actions style="position: absolute; top: 0; right: 0; margin-right: -15px;">
            <v-btn @click="dialog = false"><v-icon style="font-size: 30px;">mdi-close-circle</v-icon></v-btn>
          </v-card-actions>
        </v-card>
      </v-overlay>

      <v-dialog v-model="detailRuangan" max-width="400" persistent>
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px; height: 250px;">
          <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
            {{ detailRoom.Nama_ruangan }}</v-card-title>
          <v-card-text style="text-align: left;">
            <p>Lokasi: {{ detailRoom.Lokasi }}</p>
            <p>Kapasitas: {{ detailRoom.Kapasitas }}</p>
            <p>Kategori: {{ detailRoom.Kategori }}</p>
            <p>Fasilitas: {{ detailRoom.fasilitas }}</p>
          </v-card-text>
          <v-card-actions style="justify-content:center;">
            <v-btn @click="detailRuangan = false"
              style="position: absolute; top: 0; right: 0; margin-top: 17px;"><v-icon
                style="font-size: 30px;">mdi-close-circle</v-icon></v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>

    <footerPage></footerPage>
  </v-app>
</template>

<script>
import { reactive, onMounted, ref, computed, watch } from 'vue';
import headerUser from '../components/headerUser.vue'
import footerPage from '../components/footerPage.vue'
import axios from 'axios';
import headerAdmin from './headerAdmin.vue'
import headerDekanat from './headerDekanat.vue'
import headerSuperAdmin from './headerSuperAdmin.vue'

export default {
  name: 'peminjamanRuangan',
  components: {
    headerUser,
    footerPage,
    headerAdmin,
    headerDekanat,
    headerSuperAdmin
  },
  setup() {
    const loading = ref(false);
    const dialog = ref(false);
    const Nohp = ref('');
    const dialogTambahNomor = ref(false);
    const detailRuangan = ref(false);
    const detailRoom = ref({
      Nama_ruangan: '',
      Lokasi: '',
      Kapasitas: '',
      Kategori: '',
      fasilitas: ''
    })

    const form = reactive([
      {
        sudahDipilih: false,
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
        selectedOption: '',
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
        loading: false,
        datatabrak: [],
        tambahformbaru: 0,
      }
    ])

    const addNewForm = (index) => {
      form.push({
        sudahDipilih: false,
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
        selectedOption: '',
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
        loading: false,
        datatabrak: [],
        tambahformbaru: 0,
      })
      form[index].tambahformbaru = form[index].tambahformbaru + 1;
      console.log('form baru ditambahkan');
    }

    const removeForm = (index) => {
      if (form.length > 1) {
        form.splice(index, 1)
      } else {
        form.splice(0, form.length);
        form.push({
          sudahDipilih: false,
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
          selectedOption: '',
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
          loading: false,
          datatabrak: [],
          tambahformbaru: 0,
        })
      }
    }

    const pilihRuangan = async (Nama_ruangan, index) => {
      form[index].selectedRuangan = Nama_ruangan;
      form[index].sudahDipilih = true;
      console.log(form[index])
    }

    const batalkanRuangan = async (index) => {
      form[index].selectedRuangan = '';
      form[index].sudahDipilih = false;
      console.log(form[index])
    }

    const saveItem = async () => {
      loading.value = true;
      const savedItems = [];
      const dataSend = [];

      const UserID = localStorage.getItem('UserID');
      const aman = ref(false);

      for (let i = 0; i < form.length; i++) {
        const pdfRegex1 = /\.pdf$/i;
        if ((form[i].tanggalSelesai === '') || (form[i].tanggalAwal === '') || (form[i].selectedRuangan === '')
          || (form[i].selectedOption === '')
          || (form[i].keterangan === '') || (Nohp.value === '')) {
          aman.value = false;
          alert('Terdapat data yang kosong!');
          loading.value = false;
          return
        }
        /* if (form[i].selectedOptionPersonal === '' || form[i].selectedOptionOrganisation === '' || form[i].selectedOptionEksternal === '') {
          alert('Pilihlah salah satu dari peminjaman untuk Personal, Organisasi, dan Eksternal!');
          loading.value = false;
          return
        } else if (form[i].selectedOptionPersonal === 'False' && form[i].selectedOptionOrganisation === 'False' && form[i].selectedOptionEksternal === 'False') {
          alert('Pilihlah salah satu dari peminjaman untuk Personal, Organisasi, dan Eksternal!');
          loading.value = false;
          return
        } else if (form[i].selectedOptionPersonal === 'True' && form[i].selectedOptionOrganisation === 'True' && form[i].selectedOptionEksternal === 'True') {
          alert('Pilihlah salah satu dari peminjaman untuk Personal, Organisasi, dan Eksternal!');
          loading.value = false;
          return
        } else if (form[i].selectedOptionPersonal === 'True' && form[i].selectedOptionOrganisation === 'True') {
          alert('Pilihlah salah satu dari peminjaman untuk Personal atau Organisasi!');
          loading.value = false;
          return
        } else if (form[i].selectedOptionPersonal === 'True' && form[i].selectedOptionEksternal === 'True') {
          alert('Pilihlah salah satu dari peminjaman untuk Personal atau Eksternal!');
          loading.value = false;
          return
        } else if (form[i].selectedOptionOrganisation === 'True' && form[i].selectedOptionEksternal === 'True') {
          alert('Pilihlah salah satu dari peminjaman untuk Organisasi atau Eksternal!');
          loading.value = false;
          return
        } */

        if (form[i].selectedOption === '') {
          alert('Pilihlah salah satu dari keperluan peminjaman!');
          loading.value = false;
          return
        }

        if (form[i].selectedOption === 'eksternalTrue' && form[i].dokumen === null) {
          alert('Peminjaman dengan pihak Eksternal memerlukan surat pendukung peminjaman!');
          loading.value = false;
          return
        }
        if (form[i].selectedOption === 'organisationTrue' && form[i].dokumen === null) {
          alert('Peminjaman dengan pihak Organisasi memerlukan surat pendukung peminjaman!');
          loading.value = false;
          return
        }

        console.log(form[i].dokumen);
        /* if (form[i].selectedOptionEksternal === 'True' && form[i].dokumen === null) {
          alert('Peminjaman dengan pihak Eksternal memerlukan surat pendukung peminjaman!');
          loading.value = false;
          return
        }
        if (form[i].selectedOptionOrganisation === 'True' && form[i].dokumen === null) {
          alert('Peminjaman dengan pihak Organisasi memerlukan surat pendukung peminjaman!');
          loading.value = false;
          return
        } */

        if (form[i].alat.length > 0 && form[i].alat[0].nama !== '') {
          for (let j = 0; j < form[i].alat.length; j++) {
            if (form[i].alat[j].jumlahPinjam > form[i].alat[j].maxValue.Jumlah_ketersediaan) {
              alert('Jumlah pinjam melebihi jumlah ketersediaan alat! Pada form ke - ' + (i + 1));
              loading.value = false;
              return;
            }

            if (form[i].alat[j].jumlahPinjam === 0 || form[i].alat[j].jumlahPinjam < 0 || !Number.isInteger(Number(form[i].alat[j].jumlahPinjam))) {
              alert('Jumlah pinjam alat tidak valid! Pada form ke - ' + (i + 1));
              loading.value = false;
              return;
            }

            if (form[i].alat[j].maxValue.WajibSurat === true && form[i].dokumen === null) {
              alert('Alat ' + form[i].alat[j].nama + ' memerlukan surat peminjaman! Silahkan melakukan peminjaman alat secara terpisah.');
              loading.value = false;
              return;
            }
          }
        }

        if (form[i].selectedOption === 'personalTrue') {
          form[i].dokumen = null;
          console.log('berhasil')
        }

        if (form[i].selectedOption === 'eksternalTrue' && form[i].dokumen !== null) {
          if (pdfRegex1.test(form[i].dokumen.name)) {
            console.log('aman')
          } else {
            alert('File harus berupa pdf!');
            loading.value = false;
            return
          }
        }

        if (form[i].selectedOption === 'organisationTrue' && form[i].dokumen !== null) {
          if (pdfRegex1.test(form[i].dokumen.name)) {
            console.log('aman')
          } else {
            alert('File harus berupa pdf!');
            loading.value = false;
            return
          }
        }

        const nohpRegex = /^(08)[0-9]{9,12}$/;
        if (!nohpRegex.test(Nohp.value)) {
          alert('Nomor HP tidak valid!')
          loading.value = false;
          return
        }
      }

      //console.log(form);
      for (let i = 0; i < form.length; i++) {
        const FORMDATA = new FormData();
        const file = document.querySelector('#dokumen-' + i);
        const today = new Date();
        const formattedDate = `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;

        if (form[i].dokumen !== null) {
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
          selectedOption: form[i].selectedOption,
          alat: form[i].alat,
          keterangan: form[i].keterangan,
          dokumen: null,
          UserID: UserID,
          Nohp: Nohp.value
        };

        //dataSend.push(dataToSave);
        console.log(dataToSave);
        console.log(form[i].datatabrak);

        if (form[i].datatabrak.length > 0) {
          for (let j = 0; j < form[i].datatabrak.length; j++) {
            if (form[i].datatabrak[j].Nama_ruangan === form[i].selectedRuangan) {
              const dataCancel = form[i].datatabrak[j].Peminjaman_Ruangan_ID;
              console.log(dataCancel);
              const response = await axios({
                method: 'GET',
                url: `http://127.0.0.1:8000/api/peminjamanRuangan/cancelPeminjaman/${dataCancel}`,
                headers: {
                  'Access-Control-Allow-Origin': '*',
                  'Content-Type': 'multipart/form-data',
                }
              })
              console.log(response.data.message);
            }
          }
        }

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

          if (form[i].dokumen !== null) {
            const response2 = await axios({
              method: 'POST',
              url: 'http://localhost:8000/api/dokumen/',
              data: FORMDATA,
              headers: {
                'Access-Control-Allow-Origin': '*',
                'Content-Type': 'multipart/form-data',
              },
            });
            console.log('Peminjaman saved successfully: response2', response2.data);
          }
          //savedItems.push(response.data);
          console.log('Peminjaman saved successfully:', response.data);
          dialog.value = true;
          dialogTambahNomor.value = false;
          form[i].dateDialogAwal = false,
          form[i].sudahDipilih = false,
            form[i].dateDialogAkhir = false,
            form[i].tanggalAwal = '',
            form[i].modal = false,
            form[i].tanggalSelesai = '',
            form[i].waktuPakai = null,
            form[i].waktuSelesai = null,
            form[i].Ruangan = [],
            form[i].selectedRuangan = '',
            form[i].isPersonal = '',
            form[i].isOrganisation = '',
            form[i].isEksternal = '',
            form[i].selectedOptionPersonal = '',
            form[i].selectedOptionEksternal = '',
            form[i].selectedOptionOrganisation = '',
            form[i].selectedOption = '',
            form[i].items = [],
            form[i].itemsAll = [],
            form[i].alat = reactive([{
              nama: '',
              jumlahPinjam: 0,
              maxValue: null,
            }]),
            form[i].selectedItems = '',
            form[i].keterangan = '',
            form[i].dokumen = null,
            form[i].detailRuangan = [],
            form[i].loading = false,
            form[i].datatabrak = [],
            form[i].tambahformbaru = 0
          Nohp.value = ''
        } catch (error) {
          console.error('Error menyimpan data peminjaman ruangan', error);
          loading.value = false;
          form[i].dateDialogAwal = false,
            form[i].dateDialogAkhir = false,
            form[i].sudahDipilih = false,
            form[i].tanggalAwal = '',
            form[i].modal = false,
            form[i].tanggalSelesai = '',
            form[i].waktuPakai = null,
            form[i].waktuSelesai = null,
            form[i].Ruangan = [],
            form[i].selectedRuangan = '',
            form[i].isPersonal = '',
            form[i].isOrganisation = '',
            form[i].isEksternal = '',
            form[i].selectedOptionPersonal = '',
            form[i].selectedOptionEksternal = '',
            form[i].selectedOptionOrganisation = '',
            form[i].selectedOption = '',
            form[i].items = [],
            form[i].itemsAll = [],
            form[i].alat = reactive([{
              nama: '',
              jumlahPinjam: 0,
              maxValue: null,
            }]),
            form[i].selectedItems = '',
            form[i].keterangan = '',
            form[i].dokumen = null,
            form[i].detailRuangan = [],
            form[i].loading = false,
            form[i].datatabrak = [],
            form[i].tambahformbaru = 0,
            Nohp.value = ''
        }
      }
      loading.value = false;
    }

    const fetchAlat = async (tanggalAwal, tanggalSelesai, index) => {
      const today = new Date();
      const startDate = new Date(tanggalAwal);
      const endDate = new Date(tanggalSelesai);
      if (tanggalAwal > tanggalSelesai) {
        form[index].loading = false;
        return;
      } else if (tanggalAwal === '' || tanggalSelesai === '') {
        form[index].loading = false;
        return;
      } else if (tanggalAwal === tanggalSelesai) {
        form[index].loading = false;
        return
      } else if (startDate < today || endDate < today) {
        form[index].loading = false;
        return
      }

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

        form[index].items = namaAlat;
        form[index].itemsAll = alat;
        form[index].loading = false;

      } catch (error) {
        console.error("Error gagal mengambil data Alat", error);
        form[index].loading = false;
      }
    }

    const fetchAlatDosen = async (tanggalAwal, tanggalSelesai, index) => {
      const today = new Date();
      const startDate = new Date(tanggalAwal);
      const endDate = new Date(tanggalSelesai);
      if (tanggalAwal > tanggalSelesai) {
        form[index].loading = false;
        return;
      } else if (tanggalAwal === '' || tanggalSelesai === '') {
        form[index].loading = false;
        return;
      } else if (tanggalAwal === tanggalSelesai) {
        form[index].loading = false;
        return
      } else if (startDate < today || endDate < today) {
        form[index].loading = false;
        return
      }

      try {
        const response = await axios.get(
          `http://127.0.0.1:8000/api/peminjamanAlat/jadwalAlatforDosen/${tanggalAwal}/${tanggalSelesai}`
        );

        const alat = response.data.daftarAlatfix;
        let namaAlat = [];
        let jumlahAlat = [];
        console.log(alat);

        for (let i = 0; i < alat.length; i++) {
          namaAlat.push(alat[i].NamaAlat);
          jumlahAlat.push(alat[i].Jumlah_ketersediaan);
        }

        form[index].items = namaAlat;
        form[index].itemsAll = alat;
        form[index].loading = false;

      } catch (error) {
        console.error("Error gagal mengambil data Alat", error);
        form[index].loading = false;
      }
    }

    const availableRoom = async (tanggalAwal, tanggalSelesai, index) => {
      const today = new Date();
      const startDate = new Date(tanggalAwal);
      const endDate = new Date(tanggalSelesai);

      form[index].loading = true;
      if (tanggalAwal > tanggalSelesai) {
        alert('Tanggal awal peminjaman melebihi tanggal selesai peminjaman!');
        form[index].loading = false;
        return;
      } else if (tanggalAwal === '' || tanggalSelesai === '') {
        alert('Salah satu tanggal belum dipilih!');
        form[index].loading = false;
        return;
      } else if (tanggalAwal === tanggalSelesai) {
        alert('Tanggal tidak boleh sama persis!')
        form[index].loading = false;
        return
      } else if (startDate < today || endDate < today) {
        alert('Tidak boleh melebihi tanggal hari ini!');
        form[index].loading = false;
        return
      }

      try {
        console.log('oke')
        const response = await axios.get(
          `http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjaman/${tanggalAwal}/${tanggalSelesai}`
        );
        const availableRuangan = response.data.availableRoom;
        const roomdetail = response.data.detailRuangan;

        console.log(availableRuangan);
        console.log(roomdetail);
        console.log(response);

        form[index].Ruangan = availableRuangan;
        form[index].detailRuangan = roomdetail;
        form[index].loading = false;
      } catch (error) {
        console.error("Error fetching available rooms:", error);
        form[index].loading = false;
        alert('Tidak ada tanggal yang dipilih');
      }
    };

    const availableRoomDosen = async (tanggalAwal, tanggalSelesai, index) => {
      form[index].loading = true;
      const today = new Date();
      const startDate = new Date(tanggalAwal);
      const endDate = new Date(tanggalSelesai);
      if (tanggalAwal > tanggalSelesai) {
        alert('Tanggal awal peminjaman melebihi tanggal selesai peminjaman!');
        form[index].loading = false;
        return;
      } else if (tanggalAwal === '' || tanggalSelesai === '') {
        alert('Salah satu tanggal belum dipilih!');
        form[index].loading = false;
        return;
      } else if (tanggalAwal === tanggalSelesai) {
        alert('Tanggal tidak boleh sama persis!')
        form[index].loading = false;
        return
      } else if (startDate < today || endDate < today) {
        alert('Tidak boleh melebihi tanggal hari ini!');
        form[index].loading = false;
        return
      }

      try {
        console.log('oke')
        const response = await axios.get(
          `http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjamanforDosen/${tanggalAwal}/${tanggalSelesai}`
        );
        const availableRuangan = response.data.availableRoom;
        const roomdetail = response.data.detailRuangan;

        console.log(availableRuangan);
        console.log(roomdetail);
        console.log(response);

        form[index].Ruangan = availableRuangan;
        form[index].detailRuangan = roomdetail;
        form[index].loading = false;
        form[index].datatabrak = response.data.datatabrak;
      } catch (error) {
        console.error("Error fetching available rooms:", error);
        form[index].loading = false;
        alert('Tidak ada tanggal yang dipilih');
      }
    };

    const tambahAlat = (index) => {
      console.log('Index:', index);
      console.log('Form length:', form.length);
      console.log('Form', form[index])

      for (let i = 0; i < form[index].alat.length; i++) {
        if (form[index].alat[i].nama === '') {
          alert('Pilih alat lebih dahulu!');
          return;
        }
      }

      const alat = form[index].alat;
      const newAlat = {
        nama: '',
        jumlahPinjam: 0,
        maxValue: null,
      };

      alat.push(newAlat);
    };

    const hapusAlat = (index, alatIndex) => {
      if (form.length > 0 && form[index].alat.length > 1) {
        form[index].alat.splice(alatIndex, 1);
        console.log('Form', form[index]);
      } else {
        // kalau tinggal 1 peminjaman
        form[index].alat.splice(0, form[index].alat.length);
        form[index].alat.push({
          nama: '',
          jumlahPinjam: 0,
          maxValue: null,
        });
      }
    }

    const tampilkanDetail = (Nama_ruangan, Lokasi, Kapasitas, Kategori, fasilitas) => {
      detailRuangan.value = true;
      detailRoom.value = {
        Nama_ruangan,
        Lokasi,
        Kapasitas,
        Kategori,
        fasilitas
      }
    }

    return { detailRuangan, detailRoom, form, loading, dialog, Nohp, dialogTambahNomor, pilihRuangan, batalkanRuangan, tampilkanDetail, addNewForm, removeForm, fetchAlat, fetchAlatDosen, saveItem, availableRoom, availableRoomDosen, tambahAlat, hapusAlat };
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
    },
    resetDocumentIfBothFalse(newValue, oldValue) {
      const index = this.form.findIndex(item => item === oldValue);
      if (index !== -1 && this.form[index].selectedOptionOrganisation === 'False' && this.form[index].selectedOptionEksternal === 'False') {
        this.form[index].dokumen = null;
        console.log(this.form[index].dokumen)
      }
    },
    handleDokumen(index) {
      if (this.form[index].selectedOptionOrganisation === 'False' && this.form[index].selectedOptionEksternal === 'False') {
        this.form[index].dokumen = null;
        console.log(this.form[index].dokumen, 'berhasil')
      }
    }
  },

};
</script>
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
                                <v-text-field type="datetime-local" label="Tanggal Pakai Awal"
                                    v-model="item.tanggalAwal" variant="outlined"
                                    style="width: 280px; margin-left: 300px; margin-top: 100px; margin-right: 80px;"></v-text-field>
                                <v-text-field type="datetime-local" label="Tanggal Selesai"
                                    v-model="item.tanggalSelesai" variant="outlined"
                                    style="width: 300px; margin-left: -75px; margin-top: 100px; margin-right: 20px;"></v-text-field>
                                <v-btn :loading="item.loading"
                                    v-if="User_role === 'Mahasiswa' || User_role === 'Petugas'"
                                    @click="availableRoom(item.tanggalAwal, item.tanggalSelesai, index), fetchAlat(item.tanggalAwal, item.tanggalSelesai, index)"
                                    style="width: 120px; margin-left: 10px; margin-top: 80px; font-size: 11px; border-radius: 20px; margin-right:20px; padding-left: 50px; padding-right: 50px;"
                                    color="primary">
                                    Cek ruangan</v-btn>

                                <v-btn :loading="item.loading" v-else
                                    @click="availableRoomDosen(item.tanggalAwal, item.tanggalSelesai, index), fetchAlatDosen(item.tanggalAwal, item.tanggalSelesai, index)"
                                    style="width: 120px; margin-left: 10px; margin-top: 80px; font-size: 11px; border-radius: 20px; margin-right:20px; padding-left: 50px; padding-right: 50px;"
                                    color="primary">
                                    Cek ruangan</v-btn>
                            </div>

                            <v-select v-model="item.selectedRuangan" :items="item.Ruangan" label="Ruangan"
                                variant="outlined" clearable
                                style="width: 300px; margin-left: 303px; margin-top: 8px;"></v-select>

                            <div style="margin-left: 303px; margin-right: -80px;">
                                <label for="isPersonal">Apakah peminjaman dilakukan untuk keperluan pribadi (belajar,
                                    skripsian, kerja
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

                                <label for="isOrganisation">Apakah peminjaman dilakukan untuk keperluan
                                    organisasi?</label>
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

                                <label for="isEksternal">Apakah peminjaman digunakan bersama dengan pihak Eksternal /
                                    Luar
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

                            <v-textarea v-model="item.keterangan" style="margin-left: 303px; margin-right: -90px;"
                                label="Keterangan" row-height="25" rows="5" variant="outlined" auto-grow
                                shaped></v-textarea>

                            <p style="margin-left: 303px; margin-right: -80px;">Tambahkan Add-on</p>

                            <div v-for="(alatItem, alatIndex) in item.alat" :key="alatIndex"
                                style="display: flex; align-items: center; grid-column: span 4; width: 145%;">
                                <v-combobox v-model="alatItem.nama" :items="item.items" label="Alat yang ingin dipinjam"
                                    clearable variant="outlined"
                                    style="margin-left: 303px; margin-right: -5px; width: 50px;">
                                </v-combobox>

                                <div>

                                    <v-text-field type="number" label="Jumlah" v-model="alatItem.jumlahPinjam"
                                        variant="outlined" clearable
                                        v-if="alatItem.maxValue = item.itemsAll.find(item => item.NamaAlat === alatItem.nama)"
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

                            <v-file-input
                                v-if="item.selectedOptionOrganisation === 'True' || item.selectedOptionEksternal === 'True'"
                                type="file" accept="file/pdf" :no-icon="true" v-model="item.dokumen"
                                style="width: 505px; margin-left: 303px; margin-top: 5px;" variant="outlined"
                                label="Surat Peminjaman" ref="dokumenPendukung" :id="'dokumen-' + index"
                                @change="handleFileChange(index)"></v-file-input>

                            <div
                                style="display: flex; justify-content: space-between; margin-left: 320px; margin-right: 20px; margin-bottom: 50px; margin-top: 20px;">
                                <v-btn @click="addNewForm(index)" id="tambah"
                                    style="margin-right: 10px; margin-left: -5px;" prepend-icon=mdi-plus-circle
                                    color="primary">Tambah
                                    Peminjaman</v-btn>
                                <v-btn @click="removeForm(index)" id="hapus" prepend-icon="mdi-minus-circle"
                                    color="error">Hapus
                                    Peminjaman</v-btn>
                            </div>
                        </div>
                        <v-btn @click="saveItem()" id="simpan" :loading="loading"
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
                                                <p>{{ ruangan.Lokasi }} <v-icon icon="mdi-account"></v-icon> {{
                                                    ruangan.Kapasitas }} <v-icon icon="mdi-projector"></v-icon>
                                                    Proyektor
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
                        <v-btn @click="dialog = false"><v-icon
                                style="font-size: 30px;">mdi-close-circle</v-icon></v-btn>
                    </v-card-actions>
                </v-card>
            </v-overlay>
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
    name: 'peminjamanRuanganRekomendasi',
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
                loading: false,
                datatabrak: [],
                tambahformbaru: 0
            }
        ])

        const addNewForm = (index) => {
            if (form[index].tambahformbaru === 1) {
                alert('Form baru sudah ditambahkan sebelumnya!');
                return
            } else if (form[index].selectedOptionPersonal === '' || form[index].selectedOptionOrganisation === '' || form[index].selectedOptionEksternal === '') {
                alert('Pilihlah salah satu dari peminjaman untuk Personal, Organisasi, dan Eksternal!');
                return
            } else if (form[index].selectedOptionPersonal === 'False' && form[index].selectedOptionOrganisation === 'False' && form[index].selectedOptionEksternal === 'False') {
                alert('Pilihlah salah satu dari peminjaman untuk Personal, Organisasi, dan Eksternal!');
                return
            } else if (form[index].selectedOptionPersonal === 'True' && form[index].selectedOptionOrganisation === 'True' && form[index].selectedOptionEksternal === 'True') {
                alert('Pilihlah salah satu dari peminjaman untuk Personal, Organisasi, dan Eksternal!');
                return
            } else if (form[index].selectedOptionPersonal === 'True' && form[index].selectedOptionOrganisation === 'True') {
                alert('Pilihlah salah satu dari peminjaman untuk Personal atau Organisasi!');
                return
            } else if (form[index].selectedOptionPersonal === 'True' && form[index].selectedOptionEksternal === 'True') {
                alert('Pilihlah salah satu dari peminjaman untuk Personal atau Eksternal!');
                return
            } else if (form[index].selectedOptionOrganisation === 'True' && form[index].selectedOptionEksternal === 'True') {
                alert('Pilihlah salah satu dari peminjaman untuk Organisasi atau Eksternal!');
                return
            }

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
                loading: false,
                datatabrak: [],
                tambahformbaru: 0
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
                    loading: false,
                    datatabrak: [],
                    tambahformbaru: 0
                })
            }
        }

        const saveItem = async () => {
            loading.value = true;
            const savedItems = [];
            const dataSend = [];

            const UserID = localStorage.getItem('UserID');
            const aman = ref(false);

            for (let i = 0; i < form.length; i++) {
                if (form[i].selectedOptionPersonal === '' || form[i].selectedOptionOrganisation === '' || form[i].selectedOptionEksternal === '') {
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
                }
                if ((form[i].tanggalSelesai === '') || (form[i].tanggalAwal === '') || (form[i].selectedRuangan === '')
                    || (form[i].selectedOptionPersonal === '') || (form[i].selectedOptionOrganisation === '') || (form[i].selectedOptionEksternal === '')
                    || (form[i].keterangan === '')) {
                    aman.value = false;
                    alert('Terdapat data yang kosong!');
                    loading.value = false;
                    return
                }
                if (form[i].selectedOptionEksternal === 'True' && form[i].dokumen === null) {
                    alert('Peminjaman dengan pihak Eksternal memerlukan surat pendukung peminjaman!');
                    loading.value = false;
                    return
                }
                if (form[i].selectedOptionOrganisation === 'True' && form[i].dokumen === null) {
                    alert('Peminjaman dengan pihak Organisasi memerlukan surat pendukung peminjaman!');
                    loading.value = false;
                    return
                }

                if (form[i].alat.length > 0 && form[i].alat[0].nama !== '') {
                    for (let j = 0; j < form[i].alat.length; j++) {
                        if (form[i].alat[j].jumlahPinjam > form[i].alat[j].maxValue.Jumlah_ketersediaan) {
                            alert('Jumlah pinjam melebihi jumlah ketersediaan alat! Pada form ke - ' + (i + 1));
                            loading.value = false;
                            return;
                        }

                        if (form[i].alat[j].maxValue.WajibSurat === true && form[i].dokumen === null) {
                            alert('Alat ' + form[i].alat[j].nama + ' memerlukan surat peminjaman! Silahkan mengupload surat pendukung peminjaman alat atau lakukan peminjaman alat secara terpisah.');
                            loading.value = false;
                            return;
                        }
                    }
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
                    alat: form[i].alat,
                    keterangan: form[i].keterangan,
                    dokumen: null,
                    UserID: UserID
                };

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
                    console.log('Peminjaman saved successfully:', response.data);
                    dialog.value = true;
                } catch (error) {
                    console.error('Error menyimpan data peminjaman ruangan', error);
                    loading.value = false;
                }
            }
            loading.value = false;
        }

        const fetchAlat = async (tanggalAwal, tanggalSelesai, index) => {
            if (tanggalAwal > tanggalSelesai) {
                form[index].loading = false;
                return;
            } else if (tanggalAwal === '' || tanggalSelesai === '') {
                form[index].loading = false;
                return;
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
            if (tanggalAwal > tanggalSelesai) {
                form[index].loading = false;
                return;
            } else if (tanggalAwal === '' || tanggalSelesai === '') {
                form[index].loading = false;
                return;
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
            form[index].loading = true;
            if (tanggalAwal > tanggalSelesai) {
                alert('Tanggal awal peminjaman melebihi tanggal selesai peminjaman!');
                form[index].loading = false;
                return;
            } else if (tanggalAwal === '' || tanggalSelesai === '') {
                alert('Salah satu tanggal belum dipilih!');
                form[index].loading = false;
                return;
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
            if (tanggalAwal > tanggalSelesai) {
                alert('Tanggal awal peminjaman melebihi tanggal selesai peminjaman!');
                form[index].loading = false;
                return;
            } else if (tanggalAwal === '' || tanggalSelesai === '') {
                alert('Salah satu tanggal belum dipilih!');
                form[index].loading = false;
                return;
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
        return { form, loading, dialog, addNewForm, removeForm, fetchAlat, fetchAlatDosen, saveItem, availableRoom, availableRoomDosen, tambahAlat, hapusAlat };
    },
    data() {
        return {
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
    },
    mounted() {
        const { tanggalAwal, tanggalSelesai, Nama_ruangan } = this.$route.query;
        this.form[0].tanggalAwal = tanggalAwal;
        this.form[0].tanggalSelesai = tanggalSelesai;
        this.form[0].selectedRuangan = Nama_ruangan;

        if (this.User_role === 'Mahasiswa' || this.User_role === 'Petugas') {
            this.fetchAlat(tanggalAwal, tanggalSelesai, 0);
        } else {
            this.fetchAlatDosen(tanggalAwal, tanggalSelesai, 0);
        }
    }
};
</script>
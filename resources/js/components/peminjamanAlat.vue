<template>
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

    <div style="padding-top: 80px; height: 80%">
        <router-link to="/berandaUser"
            style="width: 200px; font-size:17px; color: rgb(2,39, 10, 0.9); margin-left: 20px; margin-top: 70px; font-family: 'Lexend-Regular'"><v-icon
                style="font-size: 25px;">mdi-keyboard-backspace</v-icon> Beranda</router-link>

        <div style="display: flex; height: 100%;">
            <v-container style="font-family: Lexend-Regular; width: 50%; margin-left:-250px; margin-right: 30px;">
                <div>
                    <v-form @submit.prevent="saveItem" ref="peminjamanForm" method="post">
                        <div v-for="item, index in form" :key="item">
                            <div
                                style="font-size: 25px; font-family: Lexend-Medium; margin-top: 20px; margin-left: 300px; margin-right: 200px; margin-bottom: -80px;width: 60%">
                                Peminjaman Alat FTI UKDW {{ index + 1 }}</div>
                            <div style="display: flex; align-items: center; grid-column: span 4; width: 138%;">
                                <v-text-field type="datetime-local" label="Tanggal Pakai Awal"
                                    v-model="item.tanggalAwal" variant="outlined"
                                    style="width: 280px; margin-left: 300px; margin-top: 100px; margin-right: 80px;"></v-text-field>
                                <v-text-field type="datetime-local" label="Tanggal Selesai"
                                    v-model="item.tanggalSelesai" variant="outlined"
                                    style="width: 300px; margin-left: -75px; margin-top: 100px;"></v-text-field>

                                <v-tooltip text="Perhatikan WAKTU PADA TANGGAL PAKAI AWAL adalah termasuk WAKTU PENGAMBILAN ALAT
                            dan WAKTU PADA TANGGAL SELESAI adalah WAKTU PENGEMBALIAN ALAT." location="end" :width="300"
                                    style="font-family: 'Lexend-Regular';">
                                    <template v-slot:activator="{ props }">
                                        <v-btn v-bind="props" icon="mdi-information"
                                            style="box-shadow: none; margin-top: 75px;"></v-btn>
                                    </template>
                                </v-tooltip>

                                <v-btn :loading="item.loading"
                                    v-if="User_role === 'Mahasiswa' || User_role === 'Petugas'"
                                    @click="fetchAlat(item.tanggalAwal, item.tanggalSelesai, index)"
                                    style="width: 120px; margin-left: 10px; margin-top: 80px; font-size: 11px; border-radius: 20px; margin-right:20px; padding-left: 50px; padding-right: 50px;"
                                    color="primary">
                                    Cek Alat</v-btn>

                                <v-btn :loading="item.loading" v-else
                                    @click="fetchAlatDosen(item.tanggalAwal, item.tanggalSelesai, index)"
                                    style="width: 120px; margin-left: 10px; margin-top: 80px; font-size: 11px; border-radius: 20px; margin-right:20px; padding-left: 50px; padding-right: 50px;"
                                    color="primary">
                                    Cek Alat</v-btn>
                            </div>

                            <v-textarea v-model="item.keterangan" style="margin-left: 303px; margin-right: -80px;"
                                label="Keterangan" row-height="25" rows="5" variant="outlined" auto-grow
                                shaped></v-textarea>

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
                                    organisasi (contoh: BPMFTI, BEMFTI)?</label>
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

                            <v-file-input type="file" accept="file/pdf" :no-icon="true" v-model="item.dokumen"
                                style="width: 505px; margin-left: 303px; margin-top: 5px;" variant="outlined"
                                label="Surat Peminjaman" ref="dokumenPendukung" :id="'dokumen-' + index"></v-file-input>

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

                        <p style="margin-left: 303px; margin-right: -80px;">Nomor Handphone Yang Dapat Dihubungi</p>

                        <v-text-field label="No. Handphone" v-model="Nohp" variant="outlined" clearable
                            style="width: 505px; margin-left: 303px; margin-top: 5px;"></v-text-field>

                        <v-checkbox label="Apabila terjadi kerusakan alat atau kehilangan alat 
                        maka bersedia untuk ganti rugi sesuai dengan persyaratan yang telah ditentukan." value="true"
                            style="margin-left: 295px; margin-right: -80px;" v-model="ketentuan"></v-checkbox>

                        <v-btn @click="saveItem" id="simpan" :loading="loading"
                            style="margin-left: 430px; margin-top: -5px; border-radius: 20px; font-size: 15px; width: 250px;"
                            color="primary">
                            Pinjam Alat </v-btn>
                    </v-form>
                </div>
            </v-container>

            <v-container
                style="font-family: Lexend-Regular; width: 45%; margin-left: 300px; margin-right: 20px; margin-bottom: 0px; height: 400px;">
                <div v-for="(item, index) in form" :key="'alattersedia-' + index" :id="'alattersedia-' + index"
                    style="border-radius: 10px; border: 1px solid; padding: 30px; margin-bottom: 750px; height: 550px; overflow-y: auto; margin-bottom: 280px;">
                    <p v-if="item.itemsAll.length > 0"
                        style="font-size: 25px; font-family: Lexend-Medium; margin-bottom: 20px;">
                        Daftar Peralatan FTI UKDW</p>
                    <p v-else
                        style="font-size: 25px; font-family: Lexend-Medium; margin-bottom: 20px; text-align: center; margin-top: 200px;">
                        Silahkan masukkan tanggal penggunaan alat untuk melihat
                        alat yang tersedia.
                    </p>
                    <v-card v-if="item.itemsAll.length > 0"
                        style="border-radius: 10px; background-color: rgb(3, 138, 33, 0.3); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3)">
                        <v-table style="overflow: hidden;">
                            <thead style="font-family: Lexend-Regular; font-size: 15px;">
                                <tr>
                                    <th class="text-center"
                                        style="background-color: rgb(3, 138, 33, 0.1); width: 20px;">No</th>
                                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Nama Alat
                                    </th>
                                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Jumlah
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(alat, index) in item.itemsAll" :key="index"
                                    style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                                    <td style="width: 20px; text-align: center;"> {{ index + 1 }}
                                    </td>
                                    <td style="width: 50px; "> {{ alat.NamaAlat }} </td>
                                    <td style="width: 50px; "> {{ alat.Jumlah_ketersediaan }} </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card>
                </div>
            </v-container>
        </div>

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
    </div>

    <footerPage></footerPage>
</template>

<script>
import { reactive, onMounted, ref } from 'vue';
import headerUser from '../components/headerUser.vue'
import footerPage from '../components/footerPage.vue'
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerDekanat from '../components/headerDekanat.vue'
import headerAdmin from '../components/headerAdmin.vue'

export default {
    name: "peminjamanAlat",
    components: {
        headerUser,
        footerPage,
        headerSuperAdmin,
        headerDekanat,
        headerAdmin
    },
    setup() {
        const loading = ref(false);
        const dialog = ref(false);
        const ketentuan = ref(false);
        const Nohp = ref('');

        const form = reactive([
            {
                tanggalAwal: '',
                modal: false,
                tanggalSelesai: '',
                isPersonal: '',
                isOrganisation: '',
                isEksternal: '',
                selectedOptionPersonal: '',
                selectedOptionEksternal: '',
                selectedOptionOrganisation: '',
                items: [],
                itemsAll: [],
                daftarAlat: [],
                alat: reactive([{
                    nama: '',
                    jumlahPinjam: 0,
                    maxValue: null,
                }]),
                keterangan: '',
                dokumen: null,
                loading: false,
                tambahformbaru: 0,
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
                tanggalAwal: '',
                modal: false,
                tanggalSelesai: '',
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
                keterangan: '',
                dokumen: null,
                daftarAlat: [],
                loading: false,
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
                    tambahformbaru: 0,
                })
            }
        }

        const saveItem = async () => {
            loading.value = true;

            if (ketentuan.value === false) {
                alert('Wajib mengisi persetujuan ketentuan.')
                loading.value = false;
                return;
            }

            if (ketentuan.value === 'true') {
                //alert('tunggu, peminjamanmu sedang diproses')
                const savedItems = [];
                const dataSend = [];

                const UserID = localStorage.getItem('UserID');

                for (let i = 0; i < form.length; i++) {
                    const pdfRegex1 = /\.pdf$/i;
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

                    console.log(form[i].dokumen);
                    if ((form[i].tanggalSelesai === '') || (form[i].tanggalAwal === '') || (form[i].alat.length === 0) || (form[i].alat.length === 1 && form[i].alat[0].nama === '')
                        || (form[i].selectedOptionPersonal === '') || (form[i].selectedOptionOrganisation === '') || (form[i].selectedOptionEksternal === '')
                        || (form[i].keterangan === '') || (Nohp.value === '')) {
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

                    let total = 0;
                    if (form[i].alat.length > 0 && form[i].alat[0].nama !== '') {
                        for (let j = 0; j < form[i].alat.length; j++) {
                            if (form[i].alat[j].jumlahPinjam > form[i].alat[j].maxValue.Jumlah_ketersediaan) {
                                alert('Jumlah pinjam melebihi jumlah ketersediaan alat! Pada form ke - ' + (i + 1));
                                loading.value = false;
                                return;
                            }

                            if (form[i].alat[j].jumlahPinjam == 0 || form[i].alat[j].jumlahPinjam < 0) {
                                alert('Jumlah pinjam alat tidak valid! Pada form ke - ' + (i + 1));
                                loading.value = false;
                                return;
                            }

                            if (form[i].alat[j].maxValue.WajibSurat === true && form[i].dokumen === null) {
                                alert('Alat ' + form[i].alat[j].nama + ' memerlukan surat peminjaman! Silahkan mengupload surat pendukung peminjaman alat atau lakukan peminjaman alat secara terpisah.');
                                loading.value = false;
                                return;
                            }

                            if (form[i].alat[j].maxValue.WajibSurat === false) {
                                total += 1;
                            }
                        }
                    }

                    if (form[i].selectedOptionEksternal === 'False' && form[i].selectedOptionOrganisation === 'False' && form[i].alat.length === total) {
                        form[i].dokumen = null;
                        console.log('berhasil')
                    }

                    if (form[i].selectedOptionEksternal === 'True' && form[i].dokumen !== null) {
                        if (pdfRegex1.test(form[i].dokumen.name)) {
                            console.log('aman')
                        } else {
                            alert('File harus berupa pdf!');
                            loading.value = false;
                            return
                        }
                    }

                    if (form[i].selectedOptionOrganisation === 'True' && form[i].dokumen !== null) {
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
                        alert('Nomo HP tidak valid!')
                        loading.value = false;
                        return
                    }
                }

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
                        UserID: UserID,
                        Nohp: Nohp.value
                    };

                    try {
                        const response = await axios({
                            method: 'POST',
                            url: 'http://localhost:8000/api/peminjamanAlat/',
                            data: dataToSave,
                            headers: {
                                'Access-Control-Allow-Origin': '*',
                                'Content-Type': 'application/json'
                            },
                        });

                        if (response.data.peminjaman_alat_bridge.length > 0) {
                            for (let j = 0; j < response.data.peminjaman_alat_bridge.length; j++) {
                                const peminjamanalatid = response.data.peminjaman_alat_bridge[j]['Peminjaman_Alat_ID'];
                                FORMDATA.append('peminjamanalatid' + j, peminjamanalatid);
                            }
                            FORMDATA.append('totalalat', response.data.peminjaman_alat_bridge.length);
                        }

                        if (form[i].dokumen !== null) {
                            const response2 = axios({
                                method: 'POST',
                                url: 'http://localhost:8000/api/dokumenAlat/',
                                data: FORMDATA,
                                headers: {
                                    'Access-Control-Allow-Origin': '*',
                                    'Content-Type': 'multipart/form-data',
                                },
                            });
                            console.log('Peminjaman saved successfully: response2', response2.data);
                        }
                        dialog.value = true;
                        console.log('Peminjaman saved successfully:', response.data);
                        form[i].tanggalAwal = '',
                            form[i].modal = false,
                            form[i].tanggalSelesai = '',
                            form[i].isPersonal = '',
                            form[i].isOrganisation = '',
                            form[i].isEksternal = '',
                            form[i].selectedOptionPersonal = '',
                            form[i].selectedOptionEksternal = '',
                            form[i].selectedOptionOrganisation = '',
                            form[i].items = [],
                            form[i].itemsAll = [],
                            form[i].daftarAlat = [],
                            form[i].alat = reactive([{
                                nama: '',
                                jumlahPinjam: 0,
                                maxValue: null,
                            }]),
                            form[i].keterangan = '',
                            form[i].dokumen = null,
                            form[i].loading = false,
                            form[i].tambahformbaru = 0,
                            Nohp.value = ''
                    } catch (error) {
                        console.error('Error menyimpan data peminjaman alat', error);
                        loading.value = false;
                        form[i].tanggalAwal = '',
                            form[i].modal = false,
                            form[i].tanggalSelesai = '',
                            form[i].isPersonal = '',
                            form[i].isOrganisation = '',
                            form[i].isEksternal = '',
                            form[i].selectedOptionPersonal = '',
                            form[i].selectedOptionEksternal = '',
                            form[i].selectedOptionOrganisation = '',
                            form[i].items = [],
                            form[i].itemsAll = [],
                            form[i].daftarAlat = [],
                            form[i].alat = reactive([{
                                nama: '',
                                jumlahPinjam: 0,
                                maxValue: null,
                            }]),
                            form[i].keterangan = '',
                            form[i].dokumen = null,
                            form[i].loading = false,
                            form[i].tambahformbaru = 0,
                            Nohp.value = ''
                    }
                }
                loading.value = false;
            }
        }

        const fetchAlat = async (tanggalAwal, tanggalSelesai, index) => {
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
                maxValue: null
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

        return { form, loading, dialog, ketentuan, addNewForm, removeForm, fetchAlat, saveItem, tambahAlat, hapusAlat, fetchAlatDosen, fetchAlat, Nohp }
    },
    data() {
        return {
            User_role: localStorage.getItem('User_role'),
            Total_batal: localStorage.getItem('Total_batal'),
            confirmBeforeCancel: true
        }
    },
    methods: {
        navigateToBeranda() {
            this.confirmBeforeCancel = false;
            this.$router.push('/berandaUser')
        }
    }
}
</script>

<style lang="scss" scoped></style>
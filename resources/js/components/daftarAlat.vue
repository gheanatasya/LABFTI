<template>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'"
        style="z-index: 1; position: fixed; width: 100%;">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1; position: fixed; width: 100%;"></headerAdmin>

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

    <div style="font-family: 'Lexend-Medium'; font-size: 25px; text-align: center; padding-top: 100px;"> Daftar Alat LAB
        FTI UKDW </div>

    <div style="margin-top: 30px;">
        <v-row>
            <v-col>
                <v-text-field v-model="this.searchAlat" append-inner-icon="mdi-magnify" density="compact"
                    label="Cari alat" variant="solo" hide-details single-line
                    style="width: 500px; margin-left: 50px; font-family: Lexend-Regular;"></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>

            <v-col>
                <v-btn @click="this.grafikDialog = true"
                    style="text-transform: none; font-family: Lexend-Regular; background-color: rgb(2,39, 10, 0.9); color:white;">
                    <v-icon>mdi-chart-line</v-icon>
                    Grafik Peminjaman</v-btn>
            </v-col>

            <v-col>
                <v-btn style="text-transform: none; font-family: Lexend-Regular; background-color: rgb(2,39, 10, 0.9); color:white;
                    margin-right: 50px;" @click="this.dialogTambahAlat = true"><v-icon>mdi-plus</v-icon>Tambah
                    Alat
                </v-btn>
            </v-col>
        </v-row>
    </div>

    <div style="margin-top: 20px; margin-left: 50px; margin-right: 50px;">
        <v-card>
            <v-table style="height: 400px;">
                <thead style="font-family: Lexend-Regular; font-size: 15px;">
                    <tr>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">No</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Nama Alat</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Kode Alat</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Jumlah</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Jumlah Rusak</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Status</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Action</th>
                    </tr>
                </thead>
                <tbody v-if="this.filteredTools.length > 0">
                    <tr v-for="(alat, index) in paginatedTools" :key="index"
                        style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                        <td style="width: 20px; text-align: center;"> {{ (currentPageAlat - 1) * itemsPerPage + index +
                            1 }} </td>

                        <td style="width: 150px;"> {{ alat.Nama }} </td>

                        <td style="width: 100px; text-align: center;"> {{ alat.KodeAlat }} </td>

                        <td style="width: 100px; text-align: center;"> {{ alat.JumlahAlat }} </td>

                        <td style="width: 100px; text-align: center;"> {{ alat.JumlahRusak }} </td>

                        <td style="width: 100px; text-align: center;"> {{ alat.StatusAlat }} </td>

                        <td style="width: 100px; font-size: 25px; text-align: center;">
                            <v-icon style="color: rgb(2, 39, 10, 1);"
                                @click="editDataAlat(alat.Nama, alat.KodeAlat, alat.StatusAlat, alat.AlatID, alat.WajibSurat)">mdi-pencil-circle</v-icon>
                            <v-icon style="color: rgb(206, 0, 0, 0.91);"
                                @click="confirmDeleteAlat(alat.KodeAlat, alat.Nama, alat.AlatID)">mdi-delete-circle</v-icon>
                            <v-icon style="color:  rgb(0, 0, 0, 0.5);"
                                @click="moreData(alat.detailAlat, alat.KodeAlat, alat.AlatID)">mdi-dots-horizontal-circle</v-icon>
                        </td>
                    </tr>
                </tbody>

                <tbody v-else>
                    <td></td>
                    <td></td>
                    <td></td>
                    <div class="py-1 text-center" style="content: center; margin-top: 80px;">
                        <v-icon class="mb-6" color="primary" icon="mdi-alert-circle-outline" size="40"></v-icon>
                        <div class="text-h7 font-weight-bold">Maaf, tidak ada data yang bisa ditampilkan.</div>
                    </div>
                    <td></td>
                    <td></td>
                    <td></td>
                </tbody>
            </v-table>
            <v-pagination v-model="currentPageAlat" :length="Math.ceil(filteredTools.length / itemsPerPage)"
                @change="updateCurrentPageAlat"></v-pagination>
        </v-card>

        <!-- data tabel detail alat -->
        <v-dialog v-model="expanded" persistent>
            <v-card style="border-radius: 20px; width: 1470px">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="expanded = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-text style="margin-top: -30px; margin-left: 20px; margin-right: 50px;">
                    <v-btn style="text-transform: none; font-family: Lexend-Regular; background-color: rgb(2,39, 10, 0.9); color:white;
                        margin-bottom: 20px;" @click="dialogTambahDetailAlat = true"><v-icon>mdi-plus</v-icon>Tambah
                        Detail
                        Alat
                    </v-btn>
                    <v-table>
                        <thead style="font-family: Lexend-Regular; font-size: 15px;">
                            <tr>
                                <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">No</th>
                                <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1);">Nama Detail Alat
                                </th>
                                <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Kode Detail Alat
                                </th>
                                <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Status
                                    Kebergunaan</th>
                                <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Status Peminjaman
                                </th>
                                <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Foto</th>
                                <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="this.itemforDetailAlat.length > 0">
                            <tr v-for="(detail, index) in paginatedDetailAlat(currentPageDetailAlat)" :key="index"
                                style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                                <td style="width: 20px; text-align: center;"> {{ (currentPageDetailAlat - 1) *
                                    itemsPerPage + index + 1 }} </td>
                                <td style="width: 150px;"> {{ detail.NamaDetailAlat }} </td>
                                <td style="width: 150px; text-align: center;"> {{ detail.KodeDetailAlat }} </td>
                                <td style="width: 200px; text-align: center;"> {{ detail.StatusKebergunaan }} </td>
                                <td style="width: 200px; text-align: center;"> {{ detail.StatusPeminjaman }} </td>
                                <td style="width: 500px;">
                                    <v-btn @click="morePicture(detail.Foto)" style="color: rgb(2,39, 10, 0.9); margin-left: 90px; background: none;
                                                text-decoration: underline; box-shadow: none; 
                                                ">L<p style="text-transform: lowercase;">ihat lebih banyak
                                            gambar>></p></v-btn>
                                </td>
                                <td style="width: 150px; font-size: 25px;">
                                    <v-icon style="color: rgb(2, 39, 10, 1);"
                                        @click="editDataDetailAlat(detail.NamaDetailAlat, detail.KodeDetailAlat, detail.StatusKebergunaan, detail.StatusPeminjaman, detail.Foto, detail.DetailAlatID)">mdi-pencil-circle</v-icon>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <td></td>
                            <td></td>
                            <div class="py-1 text-center"
                                style="content: center; margin-top: 60px; margin-left: 150px; margin-right: -50px;">
                                <v-icon class="mb-6" color="primary" icon="mdi-alert-circle-outline" size="40"></v-icon>
                                <div class="text-h7 font-weight-bold">Tidak terdapat data detail alat.</div>
                            </div>
                            <td></td>
                            <td></td>
                        </tbody>
                    </v-table>

                    <v-pagination v-model="currentPageDetailAlat"
                        :length="Math.ceil(this.itemforDetailAlat.length / itemsPerPage)"
                        @change="updateCurrentPageDetailAlat"></v-pagination>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- edit alat -->
        <v-dialog style="justify-content:center; margin-top: -50px;" v-model="editActionAlat" persistent
            padding-left="80px" max-width="800">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Edit Alat</v-card-title>
                <v-card-text style="text-align: center;">
                    <v-text-field label="Kode Alat" v-model="this.alatEdit.kodeAlat" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="Nama Alat" v-model="this.alatEdit.namaAlat" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-select v-model="this.alatEdit.statusAlat" :items="['Tersedia', 'Tidak Tersedia']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Status">
                    </v-select>

                    <v-select v-model="this.alatEdit.wajibSurat" :items="['Perlu Surat', 'Tidak Perlu Surat']"
                        persistent-hint variant="outlined" style="margin-right: 100px; margin-left:40px;"
                        label="Ketentuan Surat">
                    </v-select>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="editActionAlat = false">Batal</v-btn>
                    <v-btn
                        @click="updateAlat(alatEdit.namaAlat, alatEdit.kodeAlat, alatEdit.statusAlat, alatEdit.alatID, alatEdit.wajibSurat)"
                        :loading="this.alatEdit.loading"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Simpan</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- edit detail alat -->
        <v-dialog style="justify-content:center; margin-top: -50px;" v-model="editActionDetailAlat" persistent
            padding-left="80px" max-width="800">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Edit Detail Alat</v-card-title>
                <v-card-text style="text-align: center; margin-left: 40px;">
                    <v-text-field label="Nama Detail Alat" v-model="this.detailalatEdit.namaDetailAlat"
                        variant="outlined" style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="Kode Detail Alat" v-model="this.detailalatEdit.kodeDetailAlat"
                        variant="outlined" style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-select v-model="this.detailalatEdit.statusKebergunaan" :items="['Dapat Digunakan', 'Rusak']"
                        persistent-hint variant="outlined" style="margin-right: 100px; margin-left:40px;"
                        label="Status Kebergunaan">
                    </v-select>

                    <v-select v-model="this.detailalatEdit.statusPeminjaman" :items="['Dipinjam', 'Tersedia']"
                        persistent-hint variant="outlined" style="margin-right: 100px; margin-left:40px;"
                        label="Status Peminjaman">
                    </v-select>

                    <v-file-input label="Foto" variant="outlined" v-model="this.detailalatEdit.foto" multiple
                        id="editFotoAlat" style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="editActionDetailAlat = false">Batal</v-btn>
                    <v-btn :loading="this.detailalatEdit.loading"
                        @click="updateDetailAlat(detailalatEdit.namaDetailAlat, detailalatEdit.kodeDetailAlat, detailalatEdit.statusKebergunaan, detailalatEdit.statusPeminjaman, detailalatEdit.foto, detailalatEdit.detailalatID)"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Simpan</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- konfirmasi hapus alat -->
        <v-dialog style="justify-content:center;" v-model="dialogHapusAlat" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Konfirmasi
                    Penghapusan</v-card-title>
                <v-card-text style="text-align: center;">Yakin ingin menghapus alat <strong>{{
                    itemforHapusAlat.Nama }}</strong>?</v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogHapusAlat = false">Batal</v-btn>
                    <v-btn :loading="this.itemforHapusAlat.loading"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;"
                        @click="deleteAlat(itemforHapusAlat.AlatID)">Hapus</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- gagal hapus alat -->
        <v-dialog style="justify-content:center;" v-model="gagalDeleteAlat" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="gagalDeleteAlat = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Gagal Melakukan
                    Penghapusan</v-card-title>
                <v-card-text style="text-align: center;">Gagal menghapus alat <strong>{{
                    itemforHapusAlat.Nama }}</strong>, masih terdapat detail alat yang tersedia!</v-card-text>
            </v-card>
        </v-dialog>

        <!-- tambah alat -->
        <v-dialog style="justify-content:center;" v-model="dialogTambahAlat" persistent max-width="650">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 700px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Tambah Alat</v-card-title>
                <v-card-text style="text-align: center; margin-left: 50px;">
                    <v-text-field label="Kode Alat" v-model="this.alatTambah.kodeAlat" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>
                    <v-text-field label="Nama Alat" v-model="this.alatTambah.namaAlat" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>
                    <v-select v-model="this.alatTambah.statusAlat" :items="['Tersedia', 'Tidak Tersedia']"
                        persistent-hint variant="outlined" style="margin-right: 100px; margin-left:40px;"
                        label="Status">
                    </v-select>
                    <v-select v-model="this.alatTambah.wajibSurat" :items="['Perlu Surat', 'Tidak Perlu Surat']"
                        persistent-hint variant="outlined" style="margin-right: 100px; margin-left:40px;"
                        label="Ketentuan Surat">
                    </v-select>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogTambahAlat = false">Batal</v-btn>
                    <v-btn @click="tambahAlat(this.alatTambah)" :loading="this.alatTambah.loading"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Tambah</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!--  tambah detail alat -->
        <v-dialog style="justify-content:center;" v-model="dialogTambahDetailAlat" persistent max-width="650">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 700px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Tambah Detail Alat</v-card-title>
                <v-card-text style="text-align: center; margin-left: 50px;">
                    <v-text-field label="Kode Alat" v-model="this.detailalatTambah.kodeAlat" variant="outlined" readonly
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>
                    <v-text-field label="Kode Detail Alat" v-model="this.detailalatTambah.kodeDetailAlat"
                        variant="outlined" style="margin-right: 100px; margin-left:40px;"></v-text-field>
                    <v-text-field label="Nama Detail Alat" v-model="this.detailalatTambah.namaDetailAlat"
                        variant="outlined" style="margin-right: 100px; margin-left:40px;"></v-text-field>
                    <v-select v-model="this.detailalatTambah.statusKebergunaan" :items="['Dapat Digunakan', 'Rusak']"
                        persistent-hint variant="outlined" style="margin-right: 100px; margin-left:40px;"
                        label="Status Kebergunaan">
                    </v-select>
                    <v-select v-model="this.detailalatTambah.statusPeminjaman" :items="['Tersedia', 'Dipinjam']"
                        persistent-hint variant="outlined" style="margin-right: 100px; margin-left:40px;"
                        label="Status Peminjaman">
                    </v-select>
                    <v-file-input label="Foto" variant="outlined" v-model="this.detailalatTambah.foto" multiple
                        id="tambahFotoAlat" style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogTambahDetailAlat = false">Batal</v-btn>
                    <v-btn @click="tambahDetailAlat(detailalatTambah)" :loading="this.detailalatTambah.loading"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Tambah</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- grafik peminjaman perbulan -->
        <v-dialog style="justify-content:center;" v-model="grafikDialog" persistent max-width="500">
            <v-card
                style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 500px; height: 500px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="grafikDialog = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Grafik Peminjaman Alat
                </v-card-title>
                <v-card-text style="text-align: center;">
                    <v-btn @click="createChart()" :loading="this.loadinggrafik">Lihat Grafik</v-btn>
                    <canvas id="chart" max-width="300" height="200"></canvas>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- lihat gambar -->
        <v-dialog v-model="showImageDialog">
            <v-container fluid>
                <v-carousel height="600" show-arrows="hover" cycle hide-delimiter-background>
                    <v-carousel-item v-for="(picture, index) in gambarTampil" :key="index"
                        :src="'../storage/' + picture">
                    </v-carousel-item>
                </v-carousel>
                <v-btn icon small style="position: absolute; top: 20px; right:20px;" @click="showImageDialog = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-container>
        </v-dialog>
    </div>

    <footerPage></footerPage>
</template>

<script>
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerAdmin from '../components/headerAdmin.vue'
import footerPage from '../components/footerPage.vue'
import axios from 'axios'
import Chart from "chart.js/auto"

export default {
    name: "daftarAlat",
    components: {
        footerPage,
        headerSuperAdmin,
        headerAdmin
    },
    data() {
        return {
            User_role: localStorage.getItem('User_role'),
            searchAlat: '',
            expanded: false,
            editActionAlat: false,
            grafikDialog: false,
            itemforDetailAlat: [],
            alatEdit: {
                namaAlat: null,
                kodeAlat: null,
                statusAlat: null,
                loading: false,
                alatID: null,
                wajibSurat: null
            },
            alatTambah: {
                namaAlat: null,
                kodeAlat: null,
                statusAlat: null,
                jumlahKetersediaan: 0,
                loading: false,
                alatID: null,
                wajibSurat: null,
            },
            detailalatTambah: {
                namaDetailAlat: null,
                kodeAlat: null,
                kodeDetailAlat: null,
                statusKebergunaan: null,
                statusPeminjaman: null,
                foto: null,
                loading: false,
                detailalatID: null,
                AlatID: null
            },
            editActionDetailAlat: false,
            detailalatEdit: {
                namaDetailAlat: null,
                kodeDetailAlat: null,
                kodeAlat: null,
                statusKebergunaan: null,
                statusPeminjaman: null,
                foto: null,
                loading: false,
                detailalatID: null
            },
            dialogHapusAlat: false,
            itemforHapusAlat: null,
            gagalDeleteAlat: false,
            dialogTambahAlat: false,
            dialogTambahDetailAlat: false,
            allData: [],
            showImageDialog: false,
            gambarTampil: [],
            overlay: true,
            loadinggrafik: false,
            currentPageAlat: 1,
            currentPageDetailAlat: 1,
            itemsPerPage: 6
        }
    },
    methods: {
        async fetchAlat() {
            try {
                await axios.get("http://127.0.0.1:8000/api/alatforDaftarAlat").
                    then(response => {
                        this.allData = response.data;
                    }).catch(error => {
                        console.error("Error gagal mengambil data alat dan detail alat", error);
                    });
                console.log(this.allData)
            } catch {
                console.error()
            }
        },
        moreData(detailAlat, kodeAlat, AlatID) {
            this.expanded = !this.expanded;
            this.itemforDetailAlat = detailAlat;
            this.detailalatTambah.kodeAlat = kodeAlat;
            this.detailalatTambah.AlatID = AlatID;
            //console.log(this.itemforDetailAlat);
        },
        editDataAlat(namaAlat, kodeAlat, statusAlat, alatID, wajibSurat) {
            this.editActionAlat = !this.editActionAlat;
            this.alatEdit = {
                namaAlat,
                kodeAlat,
                statusAlat,
                loading: false,
                alatID: alatID,
                wajibSurat
            }
            console.log(this.alatEdit)
        },
        editDataDetailAlat(namaDetailAlat, kodeDetailAlat, statusKebergunaan, statusPeminjaman, foto, detailalatID) {
            this.editActionDetailAlat = !this.editActionDetailAlat;
            this.detailalatEdit = {
                namaDetailAlat,
                kodeDetailAlat,
                statusKebergunaan,
                statusPeminjaman,
                foto,
                loading: false,
                detailalatID
            }
            //console.log(this.detailalatEdit)
        },
        confirmDeleteAlat(KodeAlat, Nama, AlatID) {
            this.dialogHapusAlat = !this.dialogHapusAlat;
            this.itemforHapusAlat = {
                KodeAlat,
                Nama,
                AlatID
            }
        },
        deleteAlat(AlatID) {
            this.itemforHapusAlat.loading = true;
            axios.delete(`http://127.0.0.1:8000/api/alat/${AlatID}`)
                .then(response => {
                    if (response.data != 'Gagal') {
                        console.log("Alat deleted successfully:", response.data);
                        const newData = response.data.data
                        const indexAlat = this.allData.findIndex(alat => alat.AlatID === newData.AlatID);
                        this.allData[indexAlat] = newData;
                        this.itemforHapusAlat.loading = false;
                        this.dialogHapusAlat = false;
                    } else {
                        this.itemforHapusAlat.loading = false;
                        this.gagalDeleteAlat = true;
                        this.dialogHapusAlat = false;
                    }
                }).catch(error => {
                    console.error("Error deleting Alat:", error);
                    this.itemforHapusAlat.loading = false;
                    this.gagalDeleteAlat = true;
                    this.dialogHapusAlat = false;
                });
        },
        updateAlat(namaAlat, kodeAlat, statusAlat, alatID, wajibSurat) {
            this.alatEdit.loading = true;
            console.log(namaAlat, kodeAlat, statusAlat, alatID);
            if (namaAlat === '' || statusAlat === '' || kodeAlat === '' || wajibSurat === '' || wajibSurat === undefined || wajibSurat === null) {
                alert('Terdapat data yang belum diisi!');
                this.alatEdit.loading = false;
                return
            }

            const updateData = {
                namaAlat,
                statusAlat,
                kodeAlat,
                wajibSurat
            }

            console.log(updateData);
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(res => {
                    axios.put(`http://127.0.0.1:8000/api/alat/${alatID}`, updateData, {
                        withCredentials: true,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).
                        then(response => {
                            if (response.status === 200) {
                                console.log("Alat updated successfully:", response.data);

                                const newData = response.data.data
                                const indexAlat = this.allData.findIndex(alat => alat.AlatID === newData.AlatID);
                                this.allData[indexAlat] = newData;

                                this.alatEdit.loading = false;
                                this.editActionAlat = false;
                            } else {
                                console.error("Error updating Alat:", response.data.message);
                                this.alatEdit.loading = false;
                            }
                        })
                        .catch(error => {
                            console.error("Error updating Alat:", error);
                            this.alatEdit.loading = false;
                        });
                })
        },
        updateDetailAlat(namaDetailAlat, kodeDetailAlat, statusKebergunaan, statusPeminjaman, foto, detailalatID) {
            this.detailalatEdit.loading = true;
            console.log(foto)

            if (namaDetailAlat === '' || statusKebergunaan === '' || statusPeminjaman === '' || kodeDetailAlat === '') {
                alert('Terdapat data yang belum diisi!');
                this.detailalatEdit.loading = false;
                return
            }

            const formData = new FormData();

            if (foto !== null) {
                const file = document.getElementById('editFotoAlat');
                for (let i = 0; i < file.files.length; i++) {
                    const imageRegex1 = /\.png$/i;
                    const imageRegex2 = /\.img$/i;
                    const imageRegex3 = /\.jpg$/i;
                    if (imageRegex1.test(file.files[i].name) || imageRegex2.test(file.files[i].name) || imageRegex3.test(file.files[i].name)) {
                        formData.append('foto[]', file.files[i]);
                    } else {
                        alert('File harus berupa gambar!')
                        this.detailalatEdit.loading = false;
                        return
                    }
                }
                console.log('ada')
            } else {
                console.log('tidak ada')
            }

            const updateData = {
                namaDetailAlat,
                statusKebergunaan,
                statusPeminjaman,
                kodeDetailAlat
            }
            console.log(updateData);

            axios.put(`http://127.0.0.1:8000/api/detail/${detailalatID}`, updateData, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (response.status === 200) {
                        console.log("Detail alat updated successfully:", response.data);
                        if (foto[0].name && foto[0].size && foto[0].type) {
                            axios.post(`http://127.0.0.1:8000/api/detail/tambahFoto/${detailalatID}`, formData)
                                .then(res => {
                                    console.log("Foto ditambahkan successfully:", res.data);
                                    const newData = res.data.dataTambah
                                    const indexAlat = this.allData.findIndex(alat => alat.AlatID === newData.AlatID);
                                    const indexDetailAlat = this.allData[indexAlat].detailAlat.findIndex(detailalat => detailalat.DetailAlatID === newData.DetailAlatID);
                                    this.allData[indexAlat].detailAlat[indexDetailAlat] = newData;

                                    this.detailalatEdit.loading = false;
                                    this.editActionDetailAlat = false;
                                }).catch(error => {
                                    console.error("Foto gagal ditambahkan", error);
                                    this.detailalatEdit.loading = false;
                                })
                        }

                        const newData = response.data.data
                        const indexAlat = this.allData.findIndex(alat => alat.AlatID === newData.AlatID);
                        const indexDetailAlat = this.allData[indexAlat].detailAlat.findIndex(detailalat => detailalat.DetailAlatID === newData.DetailAlatID);
                        this.allData[indexAlat].detailAlat[indexDetailAlat] = newData;

                        this.detailalatEdit.loading = false;
                        this.editActionDetailAlat = false;
                    } else {
                        console.error("Error updating detail alat:", response.data.message);
                        this.detailalatEdit.loading = false;
                    }
                }).catch(error => {
                    console.error("Error updating detail alat:", error);
                    this.detailalatEdit.loading = false;
                });
        },
        tambahAlat(alatTambah) {
            this.alatTambah.loading = true;
            console.log(alatTambah)
            const alatYangDicari = this.allData.find(alat => alat.KodeAlat === alatTambah.kodeAlat);
            if (alatTambah.namaAlat === null || alatTambah.statusAlat === null || alatTambah.kodeAlat === null || alatTambah.wajibSurat === null) {
                alert('Terdapat data yang belum diisi!')
                this.alatTambah.loading = false;
                return
            } else if (alatTambah.namaAlat === '' || alatTambah.statusAlat === '' || alatTambah.kodeAlat === '' || alatTambah.wajibSurat === '') {
                alert('Terdapat data yang belum diisi!')
                this.alatTambah.loading = false;
                return
            } else if (alatYangDicari) {
                alert('Kode tersebut sudah ada! Berikan kode yang berbeda')
                this.alatTambah.loading = false;
                return
            }

            /* if (alatTambah.wajibSurat === 'Perlu Surat'){
                alatTambah.wajibSurat = true
            } else if (alatTambah.wajibSurat === 'Tidak Perlu Surat'){
                alatTambah.wajibSurat = false
            } */

            axios.post(`http://127.0.0.1:8000/api/alat`, alatTambah)
                .then(response => {
                    console.log("Data berhasil masuk ke tabel Alat", response.data)

                    const newData = response.data.data
                    let indexAlat = 0;
                    while (indexAlat < this.allData.length &&
                        this.allData[indexAlat].Nama.toLowerCase() < newData.Nama.toLowerCase()) {
                        indexAlat++;
                    }
                    this.allData.splice(indexAlat, 0, newData);


                    this.alatTambah.loading = false;
                    this.dialogTambahAlat = false
                    this.alatTambah.namaAlat = null,
                        this.alatTambah.kodeAlat = null,
                        this.alatTambah.statusAlat = null,
                        this.alatTambah.jumlahKetersediaan = 0,
                        this.alatTambah.loading = false,
                        this.alatTambah.alatID = null,
                        this.alatTambah.wajibSurat = null
                })
                .catch(Error => {
                    console.error("Data tidak berhasil dimasukkan ke tabel Alat", Error);
                    this.alatTambah.loading = false;
                    this.alatTambah.namaAlat = null,
                        this.alatTambah.kodeAlat = null,
                        this.alatTambah.statusAlat = null,
                        this.alatTambah.jumlahKetersediaan = 0,
                        this.alatTambah.loading = false,
                        this.alatTambah.alatID = null,
                        this.alatTambah.wajibSurat = null
                });
        },
        tambahDetailAlat(detailalatTambah) {
            this.detailalatTambah.loading = true;
            const alatYangDicari = this.allData.some(alat =>
                alat.detailAlat.some(detailAlat => detailAlat.KodeDetailAlat === detailalatTambah.kodeDetailAlat)
            );
            console.log(alatYangDicari);
            if (this.detailalatTambah.namaDetailAlat === null || this.detailalatTambah.statusKebergunaan === null || this.detailalatTambah.statusPeminjaman === null || this.detailalatTambah.foto === null || this.detailalatTambah.kodeDetailAlat === null) {
                alert('Terdapat data yang belum diisi');
                this.detailalatTambah.loading = false;
                return
            } else if (alatYangDicari) {
                alert('Kode detail alat tersebut sudah ada! Berikan kode yang berbeda')
                this.detailalatTambah.loading = false;
                return
            }
            //console.log(detailalatTambah)
            const formData = new FormData();

            if (detailalatTambah.foto !== null) {
                const file = document.getElementById('tambahFotoAlat');
                for (let i = 0; i < file.files.length; i++) {
                    const imageRegex1 = /\.png$/i;
                    const imageRegex2 = /\.img$/i;
                    const imageRegex3 = /\.jpg$/i;
                    if (imageRegex1.test(file.files[i].name) || imageRegex2.test(file.files[i].name) || imageRegex3.test(file.files[i].name)) {
                        formData.append('foto[]', file.files[i]);
                    } else {
                        alert('File harus berupa gambar!')
                        this.detailalatTambah.loading = false;
                        return
                    }
                }
                console.log('ada')
            } else {
                console.log('tidak ada')
            }

            axios.post(`http://127.0.0.1:8000/api/detail`, detailalatTambah)
                .then(response => {
                    console.log("Data berhasil masuk ke tabel Detail Alat", response.data);
                    const detailalat = response.data.DetailAlatID
                    if (detailalatTambah.foto !== null) {
                        axios.post(`http://127.0.0.1:8000/api/detail/tambahFoto/${detailalat}`, formData)
                            .then(res => {
                                console.log("Foto ditambahkan successfully:", res.data);
                                const newData = res.data.dataTambah
                                let indexDetailAlat = 0;
                                const indexAlat = this.allData.findIndex(alat => alat.AlatID === detailalatTambah.AlatID);
                                while (indexDetailAlat < this.allData[indexAlat].detailAlat.length &&
                                    this.allData[indexAlat].detailAlat[indexDetailAlat].NamaDetailAlat.toLowerCase() < newData.NamaDetailAlat.toLowerCase()) {
                                    indexDetailAlat++;
                                }
                                this.allData[indexAlat].detailAlat.splice(indexDetailAlat, 0, newData);

                                this.detailalatTambah.loading = false;
                                this.dialogTambahDetailAlat = false
                                this.detailalatTambah.namaDetailAlat = null,
                                    this.detailalatTambah.kodeDetailAlat = null,
                                    this.detailalatTambah.statusKebergunaan = null,
                                    this.detailalatTambah.statusPeminjaman = null,
                                    this.detailalatTambah.foto = null,
                                    this.detailalatTambah.loading = false,
                                    this.detailalatTambah.detailalatID = null,
                                    formData.delete('foto[]')
                            }).catch(error => {
                                console.error("Foto gagal ditambahkan", error);
                                this.detailalatTambah.loading = false;
                            })
                    }
                })
                .catch(Error => {
                    console.error("Data tidak berhasil dimasukkan ke tabel Detail Alat", Error);
                    this.detailalatTambah.loading = false;
                    this.detailalatTambah.namaDetailAlat = null,
                        this.detailalatTambah.kodeDetailAlat = null,
                        this.detailalatTambah.statusKebergunaan = null,
                        this.detailalatTambah.statusPeminjaman = null,
                        this.detailalatTambah.foto = null,
                        this.detailalatTambah.loading = false,
                        this.detailalatTambah.detailalatID = null,
                        formData.delete('foto[]')
                });
        },
        async createChart() {
            try {
                this.loadinggrafik = true;
                await axios.get("http://127.0.0.1:8000/api/alattotalPerbulan").
                    then(response => {
                        const dataAlat = response.data;
                        const canvas = document.getElementById('chart');
                        const dataset = [];
                        let label;
                        let data;

                        for (let i = 0; i < dataAlat.length; i++) {
                            label = dataAlat[i].label;
                            data = [
                                dataAlat[i].dataperbulan["01"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["02"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["03"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["04"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["05"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["06"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["07"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["08"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["09"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["10"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["11"].jumlah_peminjaman,
                                dataAlat[i].dataperbulan["12"].jumlah_peminjaman,
                            ];

                            const record = {
                                label,
                                data
                            }

                            dataset.push(record);
                        }

                        console.log(dataset);

                        new Chart(canvas, {
                            type: "line",
                            data: {
                                labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
                                datasets: dataset
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        stacked: true,
                                        grid: {
                                            display: false,
                                        },
                                    },
                                    y: {
                                        stacked: true,
                                        grid: {
                                            display: false,
                                        },
                                    },
                                },
                                plugins: {
                                    legend: {
                                        position: "bottom",
                                        fullSize: true,
                                    },
                                },
                            },

                        })
                        this.loadinggrafik = false;
                    }).catch(error => {
                        console.error("Error gagal mengambil data alat perbulan", error);
                        this.loadinggrafik = false;
                    });
            } catch {
                this.loadinggrafik = false;
                console.error()
            }
        },
        morePicture(foto) {
            this.showImageDialog = true;
            const fotoArray = foto.split(":").filter(pict => pict);
            this.gambarTampil = fotoArray;
            console.log(fotoArray);
        },
        updateCurrentPageAlat(val) {
            this.currentPageAlat = val;
        },
        updateCurrentPageDetailAlat(val) {
            this.currentPageDetailAlat = val;
        },
        paginatedDetailAlat(currentPageDetailAlat) {
            const startIndex = (currentPageDetailAlat - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.itemforDetailAlat.slice(startIndex, endIndex);
        },
    },
    mounted() {
        Promise.all([
            this.fetchAlat()
        ])
            .then(() => {
                this.overlay = false;
            })
            .catch((error) => {
                console.error(error);
            });
    },
    computed: {
        filteredTools() {
            return this.allData.filter(tool => {
                return tool.Nama.toLowerCase().includes(this.searchAlat.toLowerCase());
            });
        },
        paginatedTools() {
            const startIndex = (this.currentPageAlat - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.filteredTools.slice(startIndex,
                endIndex);
        },
    },

}
</script>

<style lang="scss" scoped></style>
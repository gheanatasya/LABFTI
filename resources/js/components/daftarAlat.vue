<template>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1"></headerAdmin>

    <div style="font-family: 'Lexend-Medium'; font-size: 25px; text-align: center; margin-top: 30px;"> Daftar Alat LAB
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
        <v-table>
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
            <tbody>
                <tr v-for="(alat, index) in filteredTools" :key="index"
                    style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                    <td style="width: 20px; text-align: center;"> {{ index + 1 }} </td>

                    <td style="width: 150px;"> {{ alat.Nama }} </td>

                    <td style="width: 100px; text-align: center;"> {{ alat.KodeAlat }} </td>

                    <td style="width: 100px; text-align: center;"> {{ alat.JumlahAlat }} </td>

                    <td style="width: 100px; text-align: center;"> {{ alat.JumlahRusak }} </td>

                    <td style="width: 100px; text-align: center;"> {{ alat.StatusAlat }} </td>

                    <td style="width: 100px; font-size: 25px; text-align: center;">
                        <v-icon style="color: rgb(2, 39, 10, 1);"
                            @click="editDataAlat(alat.Nama, alat.KodeAlat, alat.StatusAlat)">mdi-pencil-circle</v-icon>
                        <v-icon style="color: rgb(206, 0, 0, 0.91);"
                            @click="confirmDeleteAlat(alat.KodeAlat, alat.Nama)">mdi-delete-circle</v-icon>
                        <v-icon style="color:  rgb(0, 0, 0, 0.5);"
                            @click="moreData(alat.detailAlat, alat.KodeAlat)">mdi-dots-horizontal-circle</v-icon>
                    </td>
                </tr>
            </tbody>
        </v-table>

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
                        <tbody>
                            <tr v-for="(detail, index) in this.itemforDetailAlat" :key="index"
                                style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                                <td style="width: 20px; text-align: center;"> {{ index + 1 }} </td>
                                <td style="width: 150px;"> {{ detail.NamaDetailAlat }} </td>
                                <td style="width: 150px; text-align: center;"> {{ detail.KodeDetailAlat }} </td>
                                <td style="width: 200px; text-align: center;"> {{ detail.StatusKebergunaan }} </td>
                                <td style="width: 200px; text-align: center;"> {{ detail.StatusPeminjaman }} </td>
                                <td style="width: 500px;"> </td>
                                <td style="width: 150px; font-size: 25px;">
                                    <v-icon style="color: rgb(2, 39, 10, 1);"
                                        @click="editDataDetailAlat(detail.NamaDetailAlat, detail.KodeDetailAlat, detail.StatusKebergunaan, detail.StatusPeminjaman, detail.Foto)">mdi-pencil-circle</v-icon>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
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
                    <v-text-field label="Kode Alat" v-model="this.alatEdit.kodeAlat" variant="outlined" readonly
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="Nama Alat" v-model="this.alatEdit.namaAlat" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-select v-model="this.alatEdit.statusAlat" :items="['Tersedia', 'Tidak Tersedia']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Status">
                    </v-select>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="editActionAlat = false">Batal</v-btn>
                    <v-btn @click="updateAlat(alatEdit.namaAlat, alatEdit.kodeAlat, alatEdit.statusAlat)"
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

                    <v-file-input label="Foto" variant="outlined" v-model="this.detailalatEdit.foto"
                        style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="editActionDetailAlat = false">Batal</v-btn>
                    <v-btn
                        @click="updateDetailAlat(detailalatEdit.namaDetailAlat, detailalatEdit.kodeDetailAlat, detailalatEdit.statusKebergunaan, detailalatEdit.statusPeminjaman, detailalatEdit.foto)"
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
                    <v-btn
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;"
                        @click="deleteAlat(itemforHapusAlat.KodeAlat)">Hapus</v-btn>
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
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogTambahAlat = false">Batal</v-btn>
                    <v-btn @click="tambahAlat(this.alatTambah)"
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
                        style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogTambahDetailAlat = false">Batal</v-btn>
                    <v-btn @click="tambahDetailAlat(detailalatTambah)"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Tambah</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- grafik peminjaman perbulan -->
        <v-dialog style="justify-content:center;" v-model="grafikDialog" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="grafikDialog = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Grafik Peminjaman Alat
                </v-card-title>
                <v-card-text style="text-align: center;">
                    <v-btn @click="createChart">Lihat Grafik</v-btn>
                    <canvas id="chart" max-width="100" height="150"></canvas>
                </v-card-text>
            </v-card>
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
                statusAlat: null
            },
            alatTambah: {
                namaAlat: null,
                kodeAlat: null,
                statusAlat: null,
                jumlahKetersediaan: 0
            },
            detailalatTambah: {
                namaDetailAlat: null,
                kodeAlat: null,
                kodeDetailAlat: null,
                statusKebergunaan: null,
                statusPeminjaman: null,
                foto: null
            },
            editActionDetailAlat: false,
            detailalatEdit: null,
            dialogHapusAlat: false,
            itemforHapusAlat: [],
            gagalDeleteAlat: false,
            dialogTambahAlat: false,
            dialogTambahDetailAlat: false,
            allData: [],
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
        moreData(detailAlat, kodeAlat) {
            this.expanded = !this.expanded;
            this.itemforDetailAlat = detailAlat;
            this.detailalatTambah.kodeAlat = kodeAlat;
            //console.log(this.itemforDetailAlat);
        },
        editDataAlat(namaAlat, kodeAlat, statusAlat) {
            this.editActionAlat = !this.editActionAlat;
            this.alatEdit = {
                namaAlat,
                kodeAlat,
                statusAlat
            }
            console.log(this.alatEdit)
        },
        editDataDetailAlat(namaDetailAlat, kodeDetailAlat, statusKebergunaan, statusPeminjaman, foto) {
            this.editActionDetailAlat = !this.editActionDetailAlat;
            this.detailalatEdit = {
                namaDetailAlat,
                kodeDetailAlat,
                statusKebergunaan,
                statusPeminjaman,
                foto
            }
            //console.log(this.detailalatEdit)
        },
        confirmDeleteAlat(KodeAlat, Nama) {
            this.dialogHapusAlat = !this.dialogHapusAlat;
            this.itemforHapusAlat = {
                KodeAlat,
                Nama
            }
        },
        deleteAlat(KodeAlat) {
            axios.delete(`http://127.0.0.1:8000/api/alat/${KodeAlat}`)
                .then(response => {
                    if (response.data != 'Gagal') {
                        console.log("Alat deleted successfully:", response.data);
                        this.dialogHapusAlat = false;
                    } else {
                        this.gagalDeleteAlat = true;
                        this.dialogHapusAlat = false;
                    }
                }).catch(error => {
                    console.error("Error deleting Alat:", error);
                    this.gagalDeleteAlat = true;
                    this.dialogHapusAlat = false;
                });
        },
        updateAlat(namaAlat, kodeAlat, statusAlat) {
            const updateData = {
                namaAlat,
                statusAlat
            }

            console.log(updateData);
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(res => {
                    axios.put(`http://127.0.0.1:8000/api/alat/${kodeAlat}`, updateData, {
                        withCredentials: true,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).
                        then(response => {
                            if (response.status === 200) {
                                console.log("Alat updated successfully:", response.data);
                                this.editActionAlat = false;
                            } else {
                                console.error("Error updating Alat:", response.data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Error updating Alat:", error);
                        });
                })
        },
        updateDetailAlat(namaDetailAlat, kodeDetailAlat, statusKebergunaan, statusPeminjaman, foto) {
            const updateData = {
                namaDetailAlat,
                statusKebergunaan,
                statusPeminjaman,
                foto
            }
            console.log(updateData);

            axios.put(`http://127.0.0.1:8000/api/detail/${kodeDetailAlat}`, updateData, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (response.status === 200) {
                        console.log("Detail alat updated successfully:", response.data);
                        this.editActionDetailAlat = false;
                    } else {
                        console.error("Error updating detail alat:", response.data.message);
                    }
                }).catch(error => {
                    console.error("Error updating detail alat:", error);
                });
        },
        tambahAlat(alatTambah) {
            axios.post(`http://127.0.0.1:8000/api/alat`, alatTambah)
                .then(response => {
                    console.log("Data berhasil masuk ke tabel Alat", response.data)
                    this.dialogTambahAlat = false
                })
                .catch(Error => {
                    console.error("Data tidak berhasil dimasukkan ke tabel Alat", Error);
                });
        },
        tambahDetailAlat(detailalatTambah) {
            //console.log(detailalatTambah)
            axios.post(`http://127.0.0.1:8000/api/detail`, detailalatTambah)
                .then(response => {
                    console.log("Data berhasil masuk ke tabel Detail Alat", response.data)
                    this.dialogTambahDetailAlat = false
                })
                .catch(Error => {
                    console.error("Data tidak berhasil dimasukkan ke tabel Detail Alat", Error);
                });
        },
        createChart() {
            const canvas = document.getElementById('chart');

            new Chart(canvas, {
                type: "bar",
                data: {
                    labels: ["Karbon Terserap", "Emisi Karbon"],
                    datasets: [
                        {
                            label: "Tanah",
                            data: [
                                88,
                                88,
                            ],
                            backgroundColor: ["#134280"],
                        },
                        {
                            label: "Tanaman",
                            data: [
                                88,
                                88,
                            ],
                            backgroundColor: ["#426799"],
                        },
                        {
                            label: "Lingkungan",
                            data: ["", 88],
                            backgroundColor: ["#718db2"],
                        },
                    ],
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
        }
    },
    mounted() {
        this.fetchAlat()
    },
    computed: {
        filteredTools() {
            return this.allData.filter(tool => {
                return tool.Nama.toLowerCase().includes(this.searchAlat.toLowerCase());
            });
        }
    },

}
</script>

<style lang="scss" scoped></style>
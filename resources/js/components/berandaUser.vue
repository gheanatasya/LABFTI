<template>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1; position: fixed; width: 100%;"></headerAdmin>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1; position: fixed; width: 100%;">
    </headerSuperAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
        style="z-index: 1; position: fixed; width: 100%;"></headerDekanat>
    <headerUser v-if="User_role === 'Mahasiswa' || User_role === 'Dosen' || User_role === 'Staff'" style="z-index: 1; position: fixed; width: 100%;">
    </headerUser>

    <div style="padding-top: 90px">
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

        <p style="font-size: 35px; font-family: Lexend-Medium; text-align: center;">Halo,
            {{ this.user.Nama }} ! </p>

        <p style="font-family: Lexend-Bold; margin-left: 40px; margin-top: 10px; font-size: 20px;"> Peminjaman
            Ruangan Yang Sedang Anda Lakukan : </p>

        <v-container>
            <v-card class="tabelPinjamRuangan">
                <v-table style="overflow: hidden; height: 350px;">
                    <thead style="font-family: Lexend-Regular; font-size: 15px;">
                        <tr>
                            <th class="text-center" style="background-color: #BBDEFB">No</th>
                            <th class="text-center" style="background-color: #BBDEFB">Ruangan</th>
                            <th class="text-center" style="background-color: #BBDEFB">Waktu Penggunaan</th>
                            <th class="text-center" style="background-color: #BBDEFB">Keterangan</th>
                            <th class="text-center" style="background-color: #BBDEFB">Status</th>
                            <th class="text-center" style="background-color: #BBDEFB">Action</th>
                        </tr>
                    </thead>
                    <tbody v-if="this.ruanganbridge.length > 0">
                        <tr v-for="(item, index) in paginatedRuangan(currentPageRuangan)" :key="item.peminjamanid"
                            style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                            <td style="width: 20px;"> {{ (currentPageRuangan - 1) * itemsPerPage + index + 1 }} </td>

                            <td style="width: 50px;"> {{ item.namaruangan }} </td>

                            <td style="width: 50px;"> {{ new Date(item.tanggalawal).toLocaleTimeString('un-US', 
                                { year: 'numeric', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric'}) }} </td>

                            <td style="width: 500px;"> {{ item.keterangan }} </td>

                            <td style="text-align: center;">
                                <v-chip v-if="item.status === 'Diterima'"
                                    style="background-color: #0D47A1; color: white;"
                                    @click="openInformationRoom(item.histori)">{{ item.status }}</v-chip>

                                <v-chip v-if="item.status === 'Ditolak'"
                                    style="background-color: rgb(234, 8, 8, 0.91); color: white;"
                                    @click="openInformationRoom(item.histori)">{{ item.status }}</v-chip>

                                <v-chip v-if="item.status === 'Diproses'"
                                    style="background-color: rgb(0, 0, 0, 0.5); color: white;"
                                    @click="openInformationRoom(item.histori)">{{ item.status }}</v-chip>
                            </td>

                            <td style="width: 110px; font-size: 25px;"> <v-icon v-if="item.status === 'Diterima'"
                                    @click="downloadPDF(this.UserID, item.peminjamanid, item.peminjamanruanganid, 0)"
                                    style="color: #0D47A1">mdi-download-circle</v-icon>

                                <v-icon v-else style="color: rgb(0, 0, 0, 0.5)">mdi-download-circle</v-icon>

                                <v-icon
                                    @click="confirmDelete(item.peminjamanruanganid, item.peminjamanid, item.namaruangan)"
                                    style="color: rgb(206, 0, 0, 0.91);">mdi-delete-circle</v-icon>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <td></td>
                        <td></td>
                        <div class="py-1 text-center" style="content: center; margin-top: 60px; margin-left: 150px; margin-right: -50px;">
                            <v-icon class="mb-6" color="primary" icon="mdi-alert-circle-outline" size="40"></v-icon>
                            <div class="text-h7 font-weight-bold">Kamu belum melakukan peminjaman ruangan.</div>
                        </div>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>
                </v-table>

                <v-pagination v-model="currentPageRuangan" :length="Math.ceil(ruanganbridge.length / itemsPerPage)"
                    @change="updateCurrentPageRuangan"></v-pagination>
            </v-card>
        </v-container>

        <v-dialog v-model="dialogVisibleRoom" persistent max-width="290">
            <v-card style="border-radius: 20px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="dialogVisibleRoom = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-text style="margin-top: -50px;">
                    <div v-if="(this.activitylog.length > 0)">
                        <p v-for="(histori, index) in this.activitylog" :key="index"
                            style="font-family: Lexend-Regular;">
                            {{ index + 1 }}. {{ histori.Namastatus }} oleh {{ histori.Acc_by }} pada tanggal {{
                                histori.Tanggal_accnew }}
                            <italic>({{ histori.Catatan }})</italic>
                        </p>
                    </div>
                    <div v-else>
                        <p style="font-family: Lexend-Regular; margin-right: 10px; text-align: center">Peminjaman sedang
                            diproses</p>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog style="justify-content:center;" v-model="dialogVisible" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Konfirmasi
                    Pembatalan</v-card-title>
                <v-card-text style="text-align: center;">Yakin ingin membatalkan peminjaman ruangan <strong>{{
                    itemToDelete.namaruangan }}</strong>?</v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: #0D47A1; color: white; border-radius: 20px; width: 100px; text-transform: none;"
                        @click="dialogVisible = false">Tidak</v-btn>
                    <v-btn :loading="this.itemToDelete.loading"
                        style="border: 3px solid #0D47A1;  box-shadow: none; background-color: none; width: 100px; color: #0D47A1; border-radius: 20px; text-transform: none;"
                        @click="deletePeminjamanRuangan(itemToDelete.peminjamanruanganid, itemToDelete.peminjamanid)">Ya</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <p style="font-family: Lexend-Bold; margin-left: 40px; margin-top: 20px; font-size: 20px;"> Peminjaman
            Alat Yang Sedang Anda Lakukan : </p>

        <v-container>
            <v-card class="tabelPinjamAlat">
                <v-table style="font-family: Lexend-Regular; height: 350px;" fixed-header>
                    <thead>
                        <tr>
                            <th class="text-center" style="background-color: #BBDEFB">
                                No
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB">
                                Alat
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB">
                                Waktu Penggunaan
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB">
                                Jumlah Pinjam
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB">
                                Keterangan
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB">
                                Status
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="this.alatbridge.length > 0">
                        <tr v-for="(item, index) in paginatedAlat(currentPageAlat)" :key="item.peminjamanid"
                            style="font-family:'Lexend-Regular'; font-size: 15px; ">
                            <td style="width: 20px;"> {{ (currentPageAlat - 1) * itemsPerPage + index + 1 }} </td>

                            <td style="width: 50px; "> {{ item.namaalat }} </td>

                            <td style="width: 50px; "> {{ new Date(item.tanggalawal).toLocaleTimeString('un-US', 
                                { year: 'numeric', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric'}) }} </td>

                            <td style="width: 50px; "> {{ item.jumlahPinjam }} </td>

                            <td style="width: 500px; "> {{ item.keterangan }} </td>

                            <td style="text-align: center;">
                                <v-chip v-if="item.status === 'Diterima'"
                                    style="background-color: #0D47A1; color: white;"
                                    @click="openInformationTool(item.histori)">{{ item.status }}</v-chip>
                                <v-chip v-if="item.status === 'Ditolak'"
                                    style="background-color: rgb(234, 8, 8, 0.91); color: white;"
                                    @click="openInformationTool(item.histori)">{{ item.status }}</v-chip>
                                <v-chip v-if="item.status === 'Diproses'"
                                    style="background-color: rgb(0, 0, 0, 0.5); color: white;"
                                    @click="openInformationTool(item.histori)">{{ item.status }}</v-chip>
                            </td>

                            <td style="width: 110px; font-size: 25px;"> <v-icon v-if="item.status === 'Diterima'"
                                    @click="downloadPDF(this.UserID, item.peminjamanid, 0, item.peminjamanalatid)"
                                    style="color: #0D47A1">mdi-download-circle</v-icon>

                                <v-icon v-else style="color: rgb(0, 0, 0, 0.5)">mdi-download-circle</v-icon>

                                <v-icon
                                    @click="confirmDeleteAlat(item.peminjamanalatid, item.peminjamanid, item.namaalat)"
                                    style="color: rgb(206, 0, 0, 0.91);">mdi-delete-circle</v-icon>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <td></td>
                        <td></td>
                        <td></td>
                        <div class="py-1 text-center" style="content: center; margin-top: 60px; margin-left: 30px;">
                            <v-icon class="mb-6" color="primary" icon="mdi-alert-circle-outline" size="40"></v-icon>
                            <div class="text-h7 font-weight-bold">Kamu belum melakukan peminjaman alat.</div>
                        </div>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>
                </v-table>

                <v-pagination v-model="currentPageAlat" :length="Math.ceil(alatbridge.length / itemsPerPage)"
                    @change="updateCurrentPageAlat"></v-pagination>
            </v-card>
        </v-container>
        <v-dialog v-model="dialogVisibleTool" persistent max-width="290">
            <v-card style="border-radius: 20px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="dialogVisibleTool = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-text style="margin-top: -50px;">
                    <div v-if="(this.activitylogAlat.length > 0)">
                        <p v-for="(histori, index) in this.activitylogAlat" :key="index"
                            style="font-family: Lexend-Regular;">
                            {{ index + 1 }}. {{ histori.Namastatus }} oleh {{ histori.Acc_by }} pada tanggal {{
                                histori.Tanggal_accnew }}
                            <italic>({{ histori.Catatan }})</italic>
                        </p>
                    </div>
                    <div v-else>
                        <p style="font-family: Lexend-Regular; margin-right: 10px; text-align: center">Peminjaman sedang
                            diproses</p>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog style="justify-content:center;" v-model="dialogVisibleAlat" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Konfirmasi
                    Pembatalan</v-card-title>
                <v-card-text style="text-align: center;">Yakin ingin membatalkan peminjaman alat <strong>{{
                    itemToDeleteAlat.namaalat }}</strong>?</v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: #01579B; color: white; border-radius: 20px; width: 100px; text-transform: none;"
                        @click="dialogVisibleAlat = false">Tidak</v-btn>
                    <v-btn :loading="itemToDeleteAlat.loading"
                        style="border: 3px solid #01579B;  box-shadow: none; background-color: none; width: 100px; color: #01579B; border-radius: 20px; text-transform: none;"
                        @click="deletePeminjamanAlat(itemToDeleteAlat.peminjamanalatid, itemToDeleteAlat.peminjamanid)">Ya</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <footerPage></footerPage>
    </div>

</template>

<script>
import axios from 'axios'
import headerUser from '../components/headerUser.vue'
import footerPage from '../components/footerPage.vue'
import headerAdmin from '../components/headerAdmin.vue'
import headerDekanat from '../components/headerDekanat.vue'
import headerSuperAdmin from '../components/headerSuperAdmin.vue'

export default {
    name: 'berandaUser',
    components: {
        headerUser,
        footerPage,
        headerAdmin,
        headerDekanat,
        headerSuperAdmin
    },
    data() {
        return {
            User_role: localStorage.getItem('User_role'),
            dialogVisibleRoom: false,
            dialogVisibleTool: false,
            itemToDelete: [],
            itemToDeleteAlat: [],
            dialogVisible: false,
            dialogVisibleAlat: false,
            currentPageRuangan: 1,
            currentPageAlat: 1,
            itemsPerPage: 5,
            no: '',
            peminjaman: [],
            ruanganbridge: [],
            ruangan: [],
            alatbridge: [],
            alat: [],
            waktu: [],
            keterangan: [],
            status: [

            ],
            action: [
                { icon: 'mdi-download-circle' },
                { icon: 'mdi-close-circle' },
            ],
            data: undefined,
            user: [],
            alat: [],
            relasi: false,
            UserID: localStorage.getItem('UserID'),
            activitylog: [],
            activitylogAlat: [],
            daftarrelasi: [],
            overlay: true
        }
    },
    mounted() {
        Promise.all([
            this.getName(),
            this.getPeminjamanRuangan(),
            this.getPeminjamanAlat()
        ])
            .then(() => {
                this.overlay = false;
            })
            .catch((error) => {
                console.error(error);
            });
    },
    methods: {
        openInformationTool(histori) {
            this.dialogVisibleTool = true;
            const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
            const modifiedHistori = histori.map(histori => {
                return {
                    ...histori,
                    Tanggal_accnew: new Date(histori.Tanggal_acc).toLocaleDateString('id', options)
                }
            })
            this.activitylogAlat = modifiedHistori;
        },
        openInformationRoom(histori) {
            this.dialogVisibleRoom = true;
            const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
            const modifiedHistori = histori.map(histori => {
                return {
                    ...histori,
                    Tanggal_accnew: new Date(histori.Tanggal_acc).toLocaleDateString('id', options)
                }
            })
            this.activitylog = modifiedHistori;

        },
        async getName() {
            const UserID = localStorage.getItem('UserID');
            const totalbatal = localStorage.getItem('Total_batal');
            //console.log(totalbatal);
            console.log(this.overlay);
            await axios.get(`http://127.0.0.1:8000/api/peminjam/byUserID/${UserID}`)
                .then(response => {
                    this.user = response.data;
                })
                .catch(error => {
                    console.error('Error fetching data Nama pada tabel Peminjam', error);
                });
        },
        deletePeminjamanRuangan(peminjamanruanganid, PeminjamanID) {
            this.itemToDelete.loading = true;
            console.log(peminjamanruanganid);
            const tanggalhariini = new Date();
            console.log(tanggalhariini);
            const count = this.ruanganbridge.filter(item => item.peminjamanid === PeminjamanID).length;
            axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/checkRelation/${PeminjamanID}`)
                .then(check => {
                    console.log(check.data)
                    this.relasi = check.data.relasi;
                    this.daftarrelasi = check.data.daftarrelasi;
                    if (this.relasi) {
                        axios.delete(`http://127.0.0.1:8000/api/peminjamanRuangan/${peminjamanruanganid}`)
                            .then(response => {
                                console.log("PeminjamanRuangan deleted successfully:", response.data);
                                this.ruanganbridge = this.ruanganbridge.filter(item => item.peminjamanruanganid !== peminjamanruanganid);

                                for (let i = 0; i < this.daftarrelasi.length; i++) {
                                    const peminjamanalatid = this.daftarrelasi[i];
                                    axios.delete(`http://127.0.0.1:8000/api/peminjamanAlat/${peminjamanalatid}`)
                                        .then(response => {
                                            console.log("PeminjamanAlat deleted successfully:", response.data);
                                            this.alatbridge = this.alatbridge.filter(item => item.peminjamanalatid !== peminjamanalatid);
                                        }).catch(error => {
                                            console.error("Error deleting PeminjamanAlat:", error);
                                            this.itemToDelete.loading = false;
                                        });
                                }

                                axios.delete(`http://127.0.0.1:8000/api/peminjaman/${PeminjamanID}`)
                                    .then(response => {
                                        console.log("Peminjaman deleted successfully:", response.data);
                                        this.itemToDelete.loading = false;
                                        this.dialogVisible = false;
                                    })
                                    .catch(error => {
                                        console.error("Error deleting Peminjaman:", error);
                                        this.itemToDelete.loading = false;
                                    });

                                this.dialogVisible = false;
                            }).catch(error => {
                                console.error("Error deleting PeminjamanRuangan:", error);
                                this.itemToDelete.loading = false;
                            });
                    } else {
                        if (count <= 1) {
                            axios.delete(`http://127.0.0.1:8000/api/peminjamanRuangan/${peminjamanruanganid}`)
                                .then(response => {
                                    console.log("PeminjamanRuangan deleted successfully:", response.data);
                                    this.ruanganbridge = this.ruanganbridge.filter(item => item.peminjamanruanganid !== peminjamanruanganid);
                                    axios.delete(`http://127.0.0.1:8000/api/peminjaman/${PeminjamanID}`)
                                        .then(response => {
                                            console.log("Peminjaman deleted successfully:", response.data);
                                            this.itemToDelete.loading = false;
                                            this.dialogVisible = false;
                                        })
                                        .catch(error => {
                                            console.error("Error deleting Peminjaman:", error);
                                            this.itemToDelete.loading = false;
                                        });
                                }).catch(error => {
                                    console.error("Error deleting PeminjamanRuangan:", error);
                                    this.itemToDelete.loading = false;
                                });
                        } else {
                            axios.delete(`http://127.0.0.1:8000/api/peminjamanRuangan/${peminjamanruanganid}`)
                                .then(response => {
                                    console.log("PeminjamanRuangan deleted successfully:", response.data);
                                    this.ruanganbridge = this.ruanganbridge.filter(item => item.peminjamanruanganid !== peminjamanruanganid);
                                    this.itemToDelete.loading = false;
                                    this.dialogVisible = false;
                                }).catch(error => {
                                    console.error("Error deleting PeminjamanRuangan:", error);
                                    this.itemToDelete.loading = false;
                                    this.itemToDelete.loading = false;
                                });
                        }
                    }
                })
        },
        deletePeminjamanAlat(peminjamanalatid, PeminjamanID) {
            this.itemToDeleteAlat.loading = true;
            console.log(peminjamanalatid);
            const count = this.alatbridge.filter(item => item.peminjamanid === PeminjamanID).length;
            axios.get(`http://127.0.0.1:8000/api/peminjamanAlat/checkRelation/${PeminjamanID}`)
                .then(check => {
                    console.log(check.data)
                    this.relasi = check.data.relasi;
                    if (this.relasi) {
                        axios.delete(`http://127.0.0.1:8000/api/peminjamanAlat/${peminjamanalatid}`)
                            .then(response => {
                                console.log("PeminjamanAlat deleted successfully:", response.data);
                                this.alatbridge = this.alatbridge.filter(item => item.peminjamanalatid !== peminjamanalatid);
                                this.itemToDeleteAlat.loading = false;
                                this.dialogVisibleAlat = false;
                            }).catch(error => {
                                console.error("Error deleting PeminjamanAlat:", error);
                                this.itemToDeleteAlat.loading = false;
                            });
                    } else {
                        if (count <= 1) {
                            axios.delete(`http://127.0.0.1:8000/api/peminjamanAlat/${peminjamanalatid}`)
                                .then(response => {
                                    console.log("PeminjamanAlat deleted successfully:", response.data);
                                    this.alatbridge = this.alatbridge.filter(item => item.peminjamanalatid !== peminjamanalatid);
                                    axios.delete(`http://127.0.0.1:8000/api/peminjaman/${PeminjamanID}`)
                                        .then(response => {
                                            console.log("Peminjaman deleted successfully:", response.data);
                                            this.itemToDeleteAlat.loading = false;
                                            this.dialogVisibleAlat = false;
                                        })
                                        .catch(error => {
                                            console.error("Error deleting Peminjaman:", error);
                                            this.itemToDeleteAlat.loading = false;
                                        });
                                }).catch(error => {
                                    console.error("Error deleting PeminjamanAlat:", error);
                                    this.itemToDeleteAlat.loading = false;
                                });
                        } else {
                            axios.delete(`http://127.0.0.1:8000/api/peminjamanAlat/${peminjamanalatid}`)
                                .then(response => {
                                    console.log("PeminjamanAlat deleted successfully:", response.data);
                                    this.alatbridge = this.alatbridge.filter(item => item.peminjamanalatid !== peminjamanalatid);
                                    this.itemToDeleteAlat.loading = false;
                                    this.dialogVisibleAlat = false;
                                }).catch(error => {
                                    console.error("Error deleting PeminjamanAlat:", error);
                                    this.itemToDeleteAlat.loading = false;
                                });
                        }
                    }
                })
        },
        downloadPDF(UserID, $desiredPeminjamanID, $peminjamanruanganid, $peminjamanalatid) {
            window.open(`/generate-pdf/${UserID}/${$desiredPeminjamanID}/${$peminjamanruanganid}/${$peminjamanalatid}`, '_blank');
        },
        async getPeminjamanRuangan() {
            const UserID = localStorage.getItem('UserID');
            await axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/getPeminjamanRuangan/${UserID}`)
                .then(response => {
                    this.ruanganbridge = response.data;
                    console.log(this.ruanganbridge);
                })
        },
        async getPeminjamanAlat() {
            const UserID = localStorage.getItem('UserID');
            await axios.get(`http://127.0.0.1:8000/api/peminjamanAlat/getPeminjamanAlat/${UserID}`)
                .then(response => {
                    this.alatbridge = response.data;
                    console.log(this.alatbridge);
                })
        },
        paginatedRuangan(currentPageRuangan) {
            const startIndex = (currentPageRuangan - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.ruanganbridge.slice(startIndex, endIndex);
        },
        updateCurrentPageRuangan(val) {
            this.currentPageRuangan = val;
        },
        updateCurrentPageAlat(val) {
            this.currentPageAlat = val;
        },
        paginatedAlat(currentPageAlat) {
            const startIndex = (currentPageAlat - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.alatbridge.slice(startIndex, endIndex);
        },
        confirmDelete(peminjamanruanganid, peminjamanid, namaruangan) {
            this.dialogVisible = true;
            this.itemToDelete = {
                peminjamanruanganid,
                peminjamanid,
                namaruangan,
                loading: false
            };
        },
        confirmDeleteAlat(peminjamanalatid, peminjamanid, namaalat) {
            this.dialogVisibleAlat = true;
            this.itemToDeleteAlat = {
                peminjamanalatid,
                peminjamanid,
                namaalat,
                loading: false
            };
        }
    }
}
</script>

<style lang="scss" scoped></style>
<template>
    <headerUser style="z-index: 1"></headerUser>

    <div style="padding-top: 90px">
        <p style="font-size: 35px; font-family: Lexend-Medium; text-align: center; margin-top: 20px;">Halo,
            {{ this.user.Nama }} ! </p>

        <p style="font-family: Lexend-Bold; margin-left: 40px; margin-top: 30px; font-size: 20px;"> Peminjaman
            Ruangan Yang Sedang Anda Lakukan : </p>

        <v-container>
            <v-card class="tabelPinjamRuangan">
                <v-table style="overflow: hidden;">
                    <thead style="font-family: Lexend-Regular; font-size: 15px;">
                        <tr>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">No</th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Ruangan</th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Waktu Penggunaan</th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Keterangan</th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Status</th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in paginatedRuangan(currentPageRuangan)" :key="item.peminjamanid"
                            style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                            <td style="width: 20px;"> {{ (currentPageRuangan - 1) * itemsPerPage + index + 1 }} </td>

                            <td style="width: 50px;"> {{ item.namaruangan }} </td>

                            <td style="width: 50px;"> {{ item.tanggalawal }} , {{ item.waktupakai }} </td>

                            <td style="width: 500px;"> {{ item.keterangan }} </td>

                            <td style="text-align: center;">
                                <v-chip style="background-color: rgb(234, 8, 8, 0.91); color: white;"
                                    @click="openInformationRoom">Halo</v-chip>
                            </td>

                            <td style="width: 110px; font-size: 25px;"> <v-icon
                                    @click="downloadPDF(this.UserID, item.peminjamanid)"
                                    style="color: rgb(2, 39, 10, 1)">mdi-download-circle</v-icon>
                                <v-icon
                                    @click="confirmDelete(item.peminjamanruanganid, item.peminjamanid, item.namaruangan)"
                                    style="color: rgb(206, 0, 0, 0.91);">mdi-delete-circle</v-icon>
                            </td>
                        </tr>
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
                    <p>1. Disetujui oleh Kepala Lab</p>
                    <p>1. Disetujui oleh Kepala Lab</p>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog style="justify-content:center;" v-model="dialogVisible" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Konfirmasi
                    Penghapusan</v-card-title>
                <v-card-text style="text-align: center;">Yakin ingin menghapus peminjaman ruangan <strong>{{
                        itemToDelete.namaruangan }}</strong>?</v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogVisible = false">Batal</v-btn>
                    <v-btn
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;"
                        @click="deletePeminjamanRuangan(itemToDelete.peminjamanruanganid, itemToDelete.peminjamanid)">Hapus</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <p style="font-family: Lexend-Bold; margin-left: 40px; margin-top: 40px; font-size: 20px;"> Peminjaman
            Alat Yang Sedang Anda Lakukan : </p>

        <v-container>
            <v-card class="tabelPinjamAlat">
                <v-table style="font-family: Lexend-Regular;" fixed-header>
                    <thead>
                        <tr>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">
                                No
                            </th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">
                                Alat
                            </th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">
                                Waktu Penggunaan
                            </th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">
                                Keterangan
                            </th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">
                                Status
                            </th>
                            <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in paginatedAlat(currentPageAlat)" :key="item.peminjamanid"
                            style="font-family:'Lexend-Regular'; font-size: 15px; ">
                            <td style="width: 20px;"> {{ (currentPageAlat - 1) * itemsPerPage + index + 1 }} </td>

                            <td style="width: 50px; "> {{ item.namaalat }} </td>

                            <td style="width: 50px; "> {{ item.tanggalawal }} , {{ item.waktupakai }} </td>

                            <td style="width: 500px; "> {{ item.keterangan }} </td>

                            <td style="text-align: center;">
                                <v-chip style="background-color: rgb(234, 8, 8, 0.91); color: white;"
                                    @click="openInformationTool">Halo</v-chip>
                            </td>

                            <td style="width: 110px; font-size: 25px;"> <v-icon
                                    @click="downloadPDF(this.UserID, item.peminjamanid)"
                                    style="color: rgb(2, 39, 10, 1)">mdi-download-circle</v-icon>
                                <v-icon
                                    @click="confirmDeleteAlat(item.peminjamanalatid, item.peminjamanid, item.namaalat)"
                                    style="color: rgb(206, 0, 0, 0.91);">mdi-delete-circle</v-icon>
                            </td>
                        </tr>
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
                    <p>1. Disetujui oleh Kepala Lab</p>
                    <p>1. Disetujui oleh Kepala Lab</p>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog style="justify-content:center;" v-model="dialogVisibleAlat" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Konfirmasi
                    Penghapusan</v-card-title>
                <v-card-text style="text-align: center;">Yakin ingin menghapus peminjaman alat <strong>{{
                        itemToDeleteAlat.namaalat }}</strong>?</v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogVisibleAlat = false">Batal</v-btn>
                    <v-btn
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;"
                        @click="deletePeminjamanAlat(itemToDeleteAlat.peminjamanalatid, itemToDeleteAlat.peminjamanid)">Hapus</v-btn>
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

export default {
    name: 'berandaUser',
    components: {
        headerUser,
        footerPage
    },
    data() {
        return {
            dialogVisibleRoom: false,
            dialogVisibleTool: false,
            itemToDelete: null,
            itemToDeleteAlat: null,
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
        }
    },
    mounted() {
        this.getName(),
            this.getPeminjamanRuangan(),
            this.getPeminjamanAlat()
    },
    methods: {
        openInformationTool() {
            this.dialogVisibleTool = true;
        },
        openInformationRoom() {
            this.dialogVisibleRoom = true;
        },
        getName() {
            const UserID = localStorage.getItem('UserID');
            axios.get(`http://127.0.0.1:8000/api/peminjam/byUserID/${UserID}`)
                .then(response => {
                    this.user = response.data;
                })
                .catch(error => {
                    console.error('Error fetching data Nama pada tabel Peminjam', error);
                });
        },
        deletePeminjamanRuangan(peminjamanruanganid, PeminjamanID) {
            console.log(peminjamanruanganid);
            const count = this.ruanganbridge.filter(item => item.peminjamanid === PeminjamanID).length;
            axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/checkRelation/${PeminjamanID}`)
                .then(check => {
                    console.log(check.data)
                    this.relasi = check.data.relasi;

                    if (this.relasi) {
                        axios.delete(`http://127.0.0.1:8000/api/peminjamanRuangan/${peminjamanruanganid}`)
                            .then(response => {
                                console.log("PeminjamanRuangan deleted successfully:", response.data);
                                this.ruanganbridge = this.ruanganbridge.filter(item => item.peminjamanruanganid !== peminjamanruanganid);
                                this.dialogVisible = false;
                            }).catch(error => {
                                console.error("Error deleting PeminjamanRuangan:", error);
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
                                            this.dialogVisible = false;
                                        })
                                        .catch(error => {
                                            console.error("Error deleting Peminjaman:", error);
                                        });
                                }).catch(error => {
                                    console.error("Error deleting PeminjamanRuangan:", error);
                                });
                        } else {
                            axios.delete(`http://127.0.0.1:8000/api/peminjamanRuangan/${peminjamanruanganid}`)
                                .then(response => {
                                    console.log("PeminjamanRuangan deleted successfully:", response.data);
                                    this.ruanganbridge = this.ruanganbridge.filter(item => item.peminjamanruanganid !== peminjamanruanganid);
                                    this.dialogVisible = false;
                                }).catch(error => {
                                    console.error("Error deleting PeminjamanRuangan:", error);
                                });
                        }
                    }

                })

        },
        deletePeminjamanAlat(peminjamanalatid, PeminjamanID) {
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
                                this.dialogVisibleAlat = false;
                            }).catch(error => {
                                console.error("Error deleting PeminjamanAlat:", error);
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
                                            this.dialogVisibleAlat = false;
                                        })
                                        .catch(error => {
                                            console.error("Error deleting Peminjaman:", error);
                                        });
                                }).catch(error => {
                                    console.error("Error deleting PeminjamanAlat:", error);
                                });
                        } else {
                            axios.delete(`http://127.0.0.1:8000/api/peminjamanAlat/${peminjamanalatid}`)
                                .then(response => {
                                    console.log("PeminjamanAlat deleted successfully:", response.data);
                                    this.alatbridge = this.alatbridge.filter(item => item.peminjamanalatid !== peminjamanalatid);
                                    this.dialogVisibleAlat = false;
                                }).catch(error => {
                                    console.error("Error deleting PeminjamanAlat:", error);
                                });
                        }
                    }
                })
        },
        downloadPDF(UserID, $desiredPeminjamanID) {
            window.open(`/generate-pdf/${UserID}/${$desiredPeminjamanID}`, '_blank');
        },
        getPeminjamanRuangan() {
            const UserID = localStorage.getItem('UserID');
            axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/getPeminjamanRuangan/${UserID}`)
                .then(response => {
                    this.ruanganbridge = response.data;
                    console.log(this.ruanganbridge);
                })
        },
        getPeminjamanAlat() {
            const UserID = localStorage.getItem('UserID');
            axios.get(`http://127.0.0.1:8000/api/peminjamanAlat/getPeminjamanAlat/${UserID}`)
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
                namaruangan
            };
        },
        confirmDeleteAlat(peminjamanalatid, peminjamanid, namaalat) {
            this.dialogVisibleAlat = true;
            this.itemToDeleteAlat = {
                peminjamanalatid,
                peminjamanid,
                namaalat
            };
        }
    }
}
</script>

<style lang="scss" scoped></style>
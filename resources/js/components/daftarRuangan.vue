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
                    <v-progress-linear color="#0D47A1" height="6" indeterminate rounded></v-progress-linear>
                </v-col>
            </v-row>
        </v-container>
    </v-overlay>

    <div style="font-family: 'Lexend-Medium'; font-size: 25px; text-align: center; padding-top: 100px;"> Daftar Ruangan
        LAB
        FTI UKDW </div>
    <div style="margin-top: 30px;">
        <v-row>
            <v-col>
                <v-text-field v-model="this.searchRuangan" append-inner-icon="mdi-magnify" density="compact"
                    label="Cari ruangan" variant="solo" hide-details single-line
                    style="width: 500px; margin-left: 50px; font-family: Lexend-Regular;"></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>
            <v-col>
                <v-btn @click="this.grafikDialog = true"
                    style="text-transform: none; font-family: Lexend-Regular; background-color: #0D47A1; color:white;">
                    <v-icon>mdi-chart-line</v-icon>
                    Grafik Peminjaman</v-btn>
            </v-col>
            <v-col>
                <v-btn style="text-transform: none; font-family: Lexend-Regular; background-color: #0D47A1; color:white;
                    margin-right: 50px;" @click="this.dialogTambahRuangan = true"><v-icon>mdi-plus</v-icon>Tambah
                    Ruangan
                </v-btn>
            </v-col>
        </v-row>
    </div>

    <div style="margin-top: 20px; margin-left: 50px; margin-right: 50px;">
        <v-card>
            <v-table style="height: 400px;">
                <thead style="font-family: Lexend-Regular; font-size: 15px;">
                    <tr>
                        <th class="text-center" style="background-color: #BBDEFB">No</th>
                        <th class="text-center" style="background-color: #BBDEFB">Nama Ruangan</th>
                        <th class="text-center" style="background-color: #BBDEFB">Kode Ruangan</th>
                        <th class="text-center" style="background-color: #BBDEFB">Lokasi</th>
                        <th class="text-center" style="background-color: #BBDEFB">Kapasitas</th>
                        <th class="text-center" style="background-color: #BBDEFB">Kategori</th>
                        <th class="text-center" style="background-color: #BBDEFB">Fasilitas</th>
                        <th class="text-center" style="background-color: #BBDEFB">Status</th>
                        <th class="text-center" style="background-color: #BBDEFB">Foto</th>
                        <th class="text-center" style="background-color: #BBDEFB">Action</th>
                    </tr>
                </thead>
                <tbody v-if="this.filteredRooms.length > 0">
                    <tr v-for="(ruangan, index) in paginatedRuangan" :key="index"
                        style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                        <td style="width: 20px; text-align: center;"> {{ (currentPageRuangan - 1) * itemsPerPage + index
                            + 1 }} </td>

                        <td style="width: 150px;"> {{ ruangan.Nama_ruangan }} </td>

                        <td style="width: 100px; text-align: center;"> {{ ruangan.RuanganID }} </td>

                        <td style="width: 100px; text-align: center;"> {{ ruangan.Lokasi }} </td>

                        <td style="width: 100px; text-align: center;"> {{ ruangan.Kapasitas }} </td>

                        <td style="width: 100px; text-align: center;"> {{ ruangan.Kategori }} </td>

                        <td style="width: 100px; text-align: center;">
                            <p v-for="(facilit, index) in ruangan.fasilitas" :key="index"
                                style="font-family: Lexend-Regular;">
                                {{ index + 1 }}. {{ facilit }}
                            </p>
                        </td>

                        <td style="width: 100px; text-align: center;"> {{ ruangan.Status }} </td>

                        <td style="width: 80px; text-align: center;">
                            <v-btn @click="morePicture(ruangan.Foto)" style="color: #0D47A1; margin-left: 40px; background: none;
                                                text-decoration: underline; box-shadow: none; 
                                                ">L<p style="text-transform: lowercase;">ihat lebih banyak
                                    gambar>></p></v-btn>
                        </td>

                        <td style="width: 100px; font-size: 25px; text-align: center;">
                            <v-icon @click="editRuangan(ruangan)"
                                style="color: #0D47A1;">mdi-pencil-circle</v-icon>
                            <v-icon @click="konfirmasiHapusRuangan(ruangan.RuanganID, ruangan.Nama_ruangan)"
                                style="color: rgb(206, 0, 0, 0.91);">mdi-delete-circle</v-icon>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <div class="py-1 text-center" style="content: center; margin-top: 80px; margin-left: 50px;">
                        <v-icon class="mb-6" color="primary" icon="mdi-alert-circle-outline" size="40"></v-icon>
                        <div class="text-h7 font-weight-bold">Maaf, tidak ada data yang bisa ditampilkan.</div>
                    </div>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tbody>
            </v-table>

            <v-pagination v-model="currentPageRuangan" :length="Math.ceil(filteredRooms.length / itemsPerPage)"
                @change="updateCurrentPageRuangan"></v-pagination>
        </v-card>

        <!-- edit ruangan -->
        <v-dialog style="justify-content:center; margin-top: -50px;" v-model="editActionRuangan" persistent
            padding-left="80px" max-width="800" max-height="650">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Edit Ruangan</v-card-title>
                <v-card-text style="margin-left: 40px;">
                    <v-text-field label="Kode Ruangan" v-model="this.ruanganEdit.RuanganID" variant="outlined" readonly
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="Nama Ruangan" v-model="this.ruanganEdit.Nama_ruangan" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-select v-model="this.ruanganEdit.Lokasi"
                        :items="['Lab FTI 2', 'Lab FTI 3', 'Lab FTI 4', 'Fakultas']" persistent-hint variant="outlined"
                        style="margin-right: 100px; margin-left:40px;" label="Lokasi">
                    </v-select>

                    <v-select v-model="this.ruanganEdit.Kapasitas"
                        :items="['1-4 Orang', '5-10 Orang', '11-20 Orang', '21-40 Orang']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Kapasitas">
                    </v-select>

                    <v-select v-model="this.ruanganEdit.Kategori"
                        :items="['Ruang Diskusi(Rapat)', 'Ruang Bebas', 'Ruang Perkuliahan']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Kategori">
                    </v-select>

                    <v-textarea label="Fasilitas" v-model="this.ruanganEdit.fasilitas" variant="outlined" type=""
                        style="margin-right: 100px; margin-left:40px;"></v-textarea>

                    <v-select v-model="this.ruanganEdit.Status" :items="['Tersedia', 'Tidak Tersedia']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Status">
                    </v-select>

                    <v-file-input label="Foto" variant="outlined" v-model="this.ruanganEdit.foto" multiple
                        id="editFotoRuangan" style="margin-right: 100px; margin-left:0px;"></v-file-input>

                    <p @click="editFoto()"
                        style="font-family: Lexend-Regular; color: #0D47A1; font-size: 14px; text-transform: none; justify-content: left; margin-left: 40px; text-decoration: underline; margin-bottom: 15px;">
                        Edit Foto Yang Sudah Ada</p>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: #0D47A1; color: white; border-radius: 20px; width: 100px;"
                        @click="editActionRuangan = false">Batal</v-btn>
                    <v-btn :loading="this.ruanganEdit.loading"
                        @click="updateRuangan(ruanganEdit.RuanganID, ruanganEdit.Nama_ruangan, ruanganEdit.Lokasi, ruanganEdit.Kapasitas, ruanganEdit.Kategori, ruanganEdit.fasilitas, ruanganEdit.foto, ruanganEdit.Status)"
                        style="border: 3px solid #0D47A1;  box-shadow: none; background-color: none; width: 100px; color: #0D47A1; border-radius: 20px;">Simpan</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- konfirmasi hapus ruangan -->
        <v-dialog style="justify-content:center;" v-model="dialogHapusRuangan" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Konfirmasi
                    Penghapusan</v-card-title>
                <v-card-text style="text-align: center;">Yakin ingin menghapus ruangan <strong>{{
                    ruanganHapus.Nama_ruangan }}</strong>?</v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: #0D47A1; color: white; border-radius: 20px; width: 100px;"
                        @click="dialogHapusRuangan = false">Batal</v-btn>
                    <v-btn :loading="this.ruanganHapus.loading"
                        style="border: 3px solid #0D47A1;  box-shadow: none; background-color: none; width: 100px; color: #0D47A1; border-radius: 20px;"
                        @click="deleteRuangan(ruanganHapus.RuanganID)">Hapus</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- gagal hapus ruangan -->
        <v-dialog style="justify-content:center;" v-model="gagalDeleteRuangan" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="gagalDeleteRuangan = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Gagal Melakukan
                    Penghapusan</v-card-title>
                <v-card-text style="text-align: center;">Gagal menghapus ruangan <strong>{{
                    ruanganHapus.Nama_ruangan }}</strong></v-card-text>
            </v-card>
        </v-dialog>

        <!-- tambah ruangan -->
        <v-dialog style="justify-content:center; margin-top: -50px;" v-model="dialogTambahRuangan" persistent
            padding-left="80px" max-width="800">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Tambah Ruangan</v-card-title>
                <v-card-text style="text-align: center;">
                    <!-- <v-text-field label="Kode Ruangan" v-model="this.ruanganTambah.RuanganID" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field> -->

                    <v-text-field label="Nama Ruangan" v-model="this.ruanganTambah.Nama_ruangan" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-select v-model="this.ruanganTambah.Lokasi"
                        :items="['Lab FTI 2', 'Lab FTI 3', 'Lab FTI 4', 'Fakultas']" persistent-hint variant="outlined"
                        style="margin-right: 100px; margin-left:40px;" label="Lokasi">
                    </v-select>

                    <v-select v-model="this.ruanganTambah.Kapasitas"
                        :items="['1-4 Orang', '5-10 Orang', '11-20 Orang', '21-40 Orang']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Kapasitas">
                    </v-select>

                    <v-select v-model="this.ruanganTambah.Kategori"
                        :items="['Ruang Diskusi(Rapat)', 'Ruang Bebas', 'Ruang Perkuliahan']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Kategori">
                    </v-select>

                    <v-textarea label="Fasilitas" v-model="this.ruanganTambah.fasilitas" variant="outlined"
                        placeholder="TV, Papan Tulis, ..." style="margin-right: 100px; margin-left:40px;"></v-textarea>

                    <v-select v-model="this.ruanganTambah.Status" :items="['Tersedia', 'Tidak Tersedia']"
                        persistent-hint variant="outlined" style="margin-right: 100px; margin-left:40px;"
                        label="Status">
                    </v-select>

                    <v-file-input label="Foto" variant="outlined" v-model="this.ruanganTambah.foto" multiple
                        id="tambahFotoRuangan" style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: #0D47A1; color: white; border-radius: 20px; width: 100px;"
                        @click="this.dialogTambahRuangan = false">Batal</v-btn>
                    <v-btn @click="tambahRuangan(ruanganTambah)" :loading="this.ruanganTambah.loading"
                        style="border: 3px solid #0D47A1;  box-shadow: none; background-color: none; width: 100px; color: #0D47A1; border-radius: 20px;">Tambah</v-btn>
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
                    Grafik Peminjaman Ruangan
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

        <!-- tampilkan semua foto -->
        <v-dialog v-model="dialogEditFoto" persistent max-width="800">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="dialogEditFoto = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center; margin-top: -50px;">Foto Detail
                    Alat</v-card-title>
                <v-card-text style="text-align: center; margin-left: 50px; margin-right: 50px;">
                    <v-row cols="10" v-if="this.ruanganEdit.fotoTampil.length > 0">
                        <v-col v-for="(gambar, index) in this.ruanganEdit.fotoTampil" :key="index" cols="4">
                            <v-img :src="'../storage/' + gambar" max-width="300" height="200"
                                style="text-align: center"></v-img>
                            <div>
                                <v-icon style="color: rgb(206, 0, 0, 0.91); font-size: 30px;"
                                    @click="removeGambar(gambar)">mdi-delete-circle</v-icon>
                            </div>
                        </v-col>
                    </v-row>

                    <v-row cols="10" v-else>
                        <v-col cols="4">
                            <v-img src="../picture/no-image.png" max-width="300" height="200"
                                style="text-align: center"></v-img>
                        </v-col>
                    </v-row>
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
import Chart from "chart.js/auto"

export default {
    name: 'daftarRuangan',
    components: {
        footerPage,
        headerSuperAdmin,
        headerAdmin
    },
    data() {
        return {
            User_role: localStorage.getItem('User_role'),
            searchRuangan: '',
            dialogTambahRuangan: false,
            editActionRuangan: false,
            gagalDeleteRuangan: false,
            grafikDialog: false,
            allRoom: [],
            ruanganHapus: null,
            dialogHapusRuangan: false,
            ruanganEdit: {
                RuanganID: null,
                Kapasitas: null,
                Lokasi: null,
                Kategori: null,
                fasilitas: null,
                foto: null,
                Nama_ruangan: null,
                Status: null,
                loading: false,
                fotoTampil: []
            },
            ruanganTambah: {
                RuanganID: null,
                Kapasitas: null,
                Lokasi: null,
                Kategori: null,
                fasilitas: null,
                foto: null,
                Nama_ruangan: null,
                Status: null,
                loading: false
            },
            gambarTampil: [],
            showImageDialog: false,
            overlay: true,
            loadinggrafik: false,
            currentPageRuangan: 1,
            itemsPerPage: 6,
            dialogEditFoto: false,
        }
    },
    methods: {
        async getAllDataofRoom() {
            try {
                await axios.get("http://127.0.0.1:8000/api/ruangan/")
                    .then(response => {
                        this.allRoom = response.data.map(room => {
                            if (room.fasilitas) {
                                const facilitiesArray = room.fasilitas.split(", ").filter(facilit => facilit);
                                room.fasilitas = facilitiesArray;
                            }
                            return room;
                        });

                        console.log(this.allRoom);
                    })
                    .catch(error => {
                        console.error("Error gagal mengambil data semua ruangan", error);
                    });
            } catch {
                console.error()
            }
        },
        editRuangan(ruangan) {
            this.editActionRuangan = true;
            this.ruanganEdit.RuanganID = ruangan.RuanganID;
            this.ruanganEdit.Kapasitas = ruangan.Kapasitas;
            this.ruanganEdit.Lokasi = ruangan.Lokasi;
            this.ruanganEdit.Kategori = ruangan.Kategori;
            this.ruanganEdit.Nama_ruangan = ruangan.Nama_ruangan;
            this.ruanganEdit.fasilitas = ruangan.fasilitas;
            this.ruanganEdit.foto = null;
            this.ruanganEdit.Status = ruangan.Status;

            if (ruangan.Foto !== null) {
                this.ruanganEdit.fotoTampil = [...ruangan.Foto];
            } else {
                this.ruanganEdit.fotoTampil = []
            }
            console.log(this.ruanganEdit)
        },
        updateRuangan(RuanganID, Nama_ruangan, Lokasi, Kapasitas, Kategori, fasilitas, foto, Status) {
            this.ruanganEdit.loading = true;
            if (Nama_ruangan === '' || Lokasi === '' || Kapasitas === '' || Kategori === '' || fasilitas === '' || Status === '') {
                alert('Terdapat data yang belum diisi');
                this.ruanganEdit.loading = false
                return
            }

            const updatebaru = fasilitas.toString();
            const formData = new FormData();
            if (this.ruanganEdit.fotoTampil.length > 0) {
                for (let i = 0; i < this.ruanganEdit.fotoTampil.length; i++) {
                    formData.append('fotoLama[]', this.ruanganEdit.fotoTampil[i]);
                }
            }

            if (foto !== null) {
                const file = document.getElementById('editFotoRuangan');
                for (let i = 0; i < file.files.length; i++) {
                    const imageRegex1 = /\.png$/i;
                    const imageRegex2 = /\.img$/i;
                    const imageRegex3 = /\.jpg$/i;
                    if (imageRegex1.test(file.files[i].name) || imageRegex2.test(file.files[i].name) || imageRegex3.test(file.files[i].name)) {
                        formData.append('foto[]', file.files[i]);
                    } else {
                        alert('File harus berupa gambar!');
                        this.ruanganEdit.loading = false
                        return
                    }
                }
                console.log('ada')
            } else {
                console.log('tidak ada')
            }


            //console.log(foto);
            //console.log(foto.name, foto.size, foto.type)
            const updateData = {
                RuanganID,
                Nama_ruangan,
                Lokasi,
                Kapasitas,
                Kategori,
                updatebaru,
                Status
            }

            console.log(updateData);

            axios.put(`http://127.0.0.1:8000/api/ruangan/${RuanganID}`, updateData, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (response.status === 200) {
                        console.log("Ruangan updated successfully:", response.data);

                        if (foto !== null || this.ruanganEdit.fotoTampil.length > 0) {
                        axios.post(`http://127.0.0.1:8000/api/ruangan/editFoto/${RuanganID}`, formData)
                            .then(res => {
                                console.log("Foto ditambahkan successfully:", res.data);

                                const dataGanti = res.data.dataTambah
                                const fasilitasArrayGanti = dataGanti.fasilitas.split(", ").filter(facilit => facilit);
                                dataGanti.fasilitas = fasilitasArrayGanti;
                                console.log(dataGanti)
                                const index = this.allRoom.findIndex(room => room.RuanganID === dataGanti.RuanganID);
                                if (index !== -1) {
                                    this.allRoom[index] = dataGanti;
                                }
                                this.ruanganEdit.loading = false;
                                this.editActionRuangan = false;
                                return
                            }).catch(error => {
                                console.error("Foto gagal ditambahkan", error);
                                this.ruanganEdit.loading = false;
                            })
                        }

                        const dataGanti = response.data.data
                        const fasilitasArrayGanti = dataGanti.fasilitas.split(", ").filter(facilit => facilit);
                        dataGanti.fasilitas = fasilitasArrayGanti;
                        const index = this.allRoom.findIndex(room => room.RuanganID === dataGanti.RuanganID);
                        if (index !== -1) {
                            this.allRoom[index] = dataGanti;
                        }
                        this.ruanganEdit.loading = false;
                        this.editActionRuangan = false;
                        return
                    } else {
                        console.error("Error updating Ruangan:", response.data.message);
                        this.ruanganEdit.loading = false;
                    }
                }).catch(error => {
                    console.error("Error updating Ruangan:", error);
                    this.ruanganEdit.loading = false;
                });
        },
        konfirmasiHapusRuangan(RuanganID, Nama_ruangan) {
            this.dialogHapusRuangan = true;
            this.ruanganHapus = {
                RuanganID,
                Nama_ruangan,
                loading: false
            }
        },
        deleteRuangan(RuanganID) {
            this.ruanganHapus.loading = true;
            axios.delete(`http://127.0.0.1:8000/api/ruangan/${RuanganID}`)
                .then(response => {
                    console.log("Ruangan deleted successfully:", response.data);
                    const dataHapus = response.data.dataHapus
                    const fasilitasArrayHapus = dataHapus.fasilitas.split(", ").filter(facilit => facilit);
                    dataHapus.fasilitas = fasilitasArrayHapus;
                    const index = this.allRoom.findIndex(room => room.RuanganID === dataHapus.RuanganID);
                    if (index !== -1) {
                        this.allRoom[index] = dataHapus;
                    }

                    this.ruanganHapus.loading = false;
                    this.dialogHapusRuangan = false;
                }).catch(error => {
                    console.error("Error deleting Ruangan:", error);
                    this.gagalDeleteRuangan = true;
                    this.ruanganHapus.loading = false;
                    this.dialogHapusRuangan = false;
                });
        },
        async createChart() {
            try {
                this.loadinggrafik = true;
                await axios.get("http://127.0.0.1:8000/api/ruangantotalPerbulan").
                    then(response => {
                        const dataRuangan = response.data;
                        const canvas = document.getElementById('chart');
                        const dataset = [];
                        let label;
                        let data;

                        for (let i = 0; i < dataRuangan.length; i++) {
                            label = dataRuangan[i].label;
                            data = [
                                dataRuangan[i].dataperbulan["01"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["02"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["03"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["04"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["05"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["06"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["07"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["08"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["09"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["10"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["11"].jumlah_peminjaman,
                                dataRuangan[i].dataperbulan["12"].jumlah_peminjaman,
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
                    }).catch(error => {
                        console.error("Error gagal mengambil data alat perbulan", error);
                        this.loadinggrafik = false;
                    });
                this.loadinggrafik = false;
            } catch {
                console.error()
                this.loadinggrafik = false;
            }
        },
        tambahRuangan(ruanganTambah) {
            this.ruanganTambah.loading = true;
            console.log(ruanganTambah)
            const regex = /^[a-zA-Z,\s]+$/;

            if (this.ruanganTambah.Kapasitas === null || this.ruanganTambah.Kapasitas === null || this.ruanganTambah.Lokasi === null || this.ruanganTambah.Kategori === null || this.ruanganTambah.Nama_ruangan === null || this.ruanganTambah.fasilitas === null || this.ruanganTambah.Status === null || this.ruanganTambah.foto === null) {
                alert('Terdapat data yang belum diisi!');
                this.ruanganTambah.loading = false;
                return
            } else if (!regex.test(this.ruanganTambah.fasilitas)) {
                alert('Format fasilitas tidak sesuai placeholder!')
                this.ruanganTambah.loading = false;
                return
            }

            const facilitiesString = ruanganTambah.fasilitas;
            const facilitiesArray = facilitiesString.split(", ").filter(facility => facility);

            const formData = new FormData();

            if (ruanganTambah.foto !== null) {
                const file = document.getElementById('tambahFotoRuangan');
                for (let i = 0; i < file.files.length; i++) {
                    const imageRegex1 = /\.png$/i;
                    const imageRegex2 = /\.img$/i;
                    const imageRegex3 = /\.jpg$/i;
                    if (imageRegex1.test(file.files[i].name) || imageRegex2.test(file.files[i].name) || imageRegex3.test(file.files[i].name)) {
                        formData.append('foto[]', file.files[i]);
                    } else {
                        alert('File harus berupa gambar!')
                        this.ruanganTambah.loading = false;
                        return
                    }
                }
                console.log('ada')
            } else {
                console.log('tidak ada')
            }

            const tambahData = {
                RuanganID: ruanganTambah.RuanganID,
                Kapasitas: ruanganTambah.Kapasitas,
                Kategori: ruanganTambah.Kategori,
                Lokasi: ruanganTambah.Lokasi,
                Nama_ruangan: ruanganTambah.Nama_ruangan,
                fasilitas: ruanganTambah.fasilitas,
                Status: ruanganTambah.Status
            }

            console.log(tambahData)

            axios.post(`http://127.0.0.1:8000/api/ruangan`, tambahData)
                .then(response => {
                    console.log("Data berhasil masuk ke tabel Ruangan", response.data)
                    const ruangan = response.data.ruanganid;
                    console.log(ruangan)
                    if (ruanganTambah.foto !== null) {
                        axios.post(`http://127.0.0.1:8000/api/ruangan/tambahFoto/${ruangan}`, formData)
                            .then(res => {
                                console.log("Foto ditambahkan successfully:", res.data);
                                this.ruanganTambah.loading = false;
                                this.dialogTambahRuangan = false
                                const newData = res.data.dataTambah
                                const facilitiesString = newData.fasilitas.split(", ").filter(facility => facility);
                                newData.fasilitas = facilitiesString

                                let index = 0;
                                while (index < this.allRoom.length &&
                                    this.allRoom[index].Nama_ruangan.toLowerCase() < newData.Nama_ruangan.toLowerCase()) {
                                    index++;
                                }

                                this.allRoom.splice(index, 0, newData);

                                this.ruanganTambah.Kapasitas = null,
                                    this.ruanganTambah.Kategori = null,
                                    this.ruanganTambah.Lokasi = null,
                                    this.ruanganTambah.Nama_ruangan = null,
                                    this.ruanganTambah.fasilitas = null,
                                    this.ruanganTambah.Status = null,
                                    this.ruanganTambah.foto = null
                                formData.delete('foto[]')
                            }).catch(error => {
                                console.error("Foto gagal ditambahkan", error);
                                this.ruanganTambah.loading = false;
                            })
                    }
                })
                .catch(Error => {
                    console.error("Data tidak berhasil dimasukkan ke tabel Ruangan", Error);
                    this.ruanganTambah.loading = false;
                    this.ruanganTambah.Kapasitas = null,
                        this.ruanganTambah.Kategori = null,
                        this.ruanganTambah.Lokasi = null,
                        this.ruanganTambah.Nama_ruangan = null,
                        this.ruanganTambah.fasilitas = null,
                        this.ruanganTambah.Status = null
                    this.ruanganTambah.foto = null
                });
        },
        morePicture(Foto) {
            this.showImageDialog = true;
            /* const fotoString = Foto.join(':');
            console.log(fotoString);
            const fotoArray = Foto.split(":").filter(pict => pict); */
            this.gambarTampil = Foto;
            //console.log(fotoArray);
        },
        updateCurrentPageRuangan(val) {
            this.currentPageRuangan = val
        },
        editFoto() {
            this.dialogEditFoto = true
        },
        removeGambar(gambar) {
            const index = this.ruanganEdit.fotoTampil.indexOf(gambar);
            if (index > -1) {
                this.ruanganEdit.fotoTampil.splice(index, 1);
            }
            console.log(this.ruanganEdit.fotoTampil)
        }
    },
    mounted() {
        Promise.all([
            this.getAllDataofRoom()
        ])
            .then(() => {
                this.overlay = false;
            })
            .catch((error) => {
                console.error(error);
            });
    },
    computed: {
        filteredRooms() {
            return this.allRoom.filter(room => {
                return room.Nama_ruangan.toLowerCase().includes(this.searchRuangan.toLowerCase());
            });
        },
        paginatedRuangan() {
            const startIndex = (this.currentPageRuangan - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.filteredRooms.slice(startIndex, endIndex);
        }
    },
}
</script>

<style lang="scss" scoped></style>
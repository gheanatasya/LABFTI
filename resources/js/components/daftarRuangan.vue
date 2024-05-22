<template>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1"></headerAdmin>

    <div style="font-family: 'Lexend-Medium'; font-size: 25px; text-align: center; margin-top: 30px;"> Daftar Ruangan
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
                <v-btn @click="goToDetail"
                    style="text-transform: none; font-family: Lexend-Regular; background-color: rgb(2,39, 10, 0.9); color:white;">
                    <v-icon>mdi-download</v-icon>Download
                    Histori Peminjaman</v-btn>
            </v-col>
            <v-col>
                <v-btn style="text-transform: none; font-family: Lexend-Regular; background-color: rgb(2,39, 10, 0.9); color:white;
                    margin-right: 50px;" @click="this.dialogTambahRuangan = true"><v-icon>mdi-plus</v-icon>Tambah
                    Ruangan
                </v-btn>
            </v-col>
        </v-row>
    </div>

    <div style="margin-top: 20px; margin-left: 50px; margin-right: 50px;">
        <v-table>
            <thead style="font-family: Lexend-Regular; font-size: 15px;">
                <tr>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">No</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Nama Ruangan</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Kode Ruangan</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Lokasi</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Kapasitas</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Kategori</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Fasilitas</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Status</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Foto</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(ruangan, index) in allRoom" :key="index"
                    style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                    <td style="width: 20px; text-align: center;"> {{ index + 1 }} </td>

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

                    <td style="width: 100px; text-align: center;"> {{ ruangan.Foto }} </td>

                    <td style="width: 100px; font-size: 25px; text-align: center;">
                        <v-icon @click="editRuangan(ruangan)"
                            style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>
                        <v-icon @click="konfirmasiHapusRuangan(ruangan.RuanganID, ruangan.Nama_ruangan)"
                            style="color: rgb(206, 0, 0, 0.91);">mdi-delete-circle</v-icon>
                    </td>
                </tr>
            </tbody>
        </v-table>

        <!-- edit ruangan -->
        <v-dialog style="justify-content:center; margin-top: -50px;" v-model="editActionRuangan" persistent
            padding-left="80px" max-width="800">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Edit Ruangan</v-card-title>
                <v-card-text style="text-align: center;">
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
                        :items="['Ruang Diskusi/Rapat', 'Ruang Bebas', 'Ruang Perkuliahan']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Kategori">
                    </v-select>

                    <v-textarea label="Fasilitas" v-model="this.ruanganEdit.fasilitas" variant="outlined" type=""
                        style="margin-right: 100px; margin-left:40px;"></v-textarea>

                    <v-file-input label="Foto" variant="outlined" v-model="this.ruanganEdit.foto" multiple
                        style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="editActionRuangan = false">Batal</v-btn>
                    <v-btn @click="updateRuangan()"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Simpan</v-btn>
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
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogHapusRuangan = false">Batal</v-btn>
                    <v-btn
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;"
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
                    <v-text-field label="Kode Ruangan" v-model="this.ruanganTambah.RuanganID" variant="outlined" readonly
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

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
                        :items="['Ruang Diskusi/Rapat', 'Ruang Bebas', 'Ruang Perkuliahan']" persistent-hint
                        variant="outlined" style="margin-right: 100px; margin-left:40px;" label="Kategori">
                    </v-select>

                    <v-textarea label="Fasilitas" v-model="this.ruanganTambah.fasilitas" variant="outlined" type=""
                        style="margin-right: 100px; margin-left:40px;"></v-textarea>

                    <v-file-input label="Foto" variant="outlined" v-model="this.ruanganTambah.foto" multiple
                        style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="this.dialogTambahRuangan = false">Batal</v-btn>
                    <v-btn @click="tambahRuangan()"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Simpan</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </div>

    <footerPage></footerPage>
</template>

<script>
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerAdmin from '../components/headerAdmin.vue'
import footerPage from '../components/footerPage.vue'

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
            searchRuangan: null,
            dialogTambahRuangan: false,
            editActionRuangan: false,
            gagalDeleteRuangan: false,
            allRoom: [],
            ruanganHapus: undefined,
            dialogHapusRuangan: false,
            ruanganEdit: {
                RuanganID: null,
                Kapasitas: null,
                Lokasi: null,
                Kategori: null,
                fasilitas: null,
                Foto: null,
                Nama_ruangan: null
            },
            ruanganTambah: {
                RuanganID: null,
                Kapasitas: null,
                Lokasi: null,
                Kategori: null,
                fasilitas: null,
                Foto: null,
                Nama_ruangan: null
            }
        }
    },
    methods: {
        async getAllDataofRoom() {
            try {
                await axios.get("http://127.0.0.1:8000/api/ruangan/")
                    .then(response => {
                        this.allRoom = response.data.map(room => {
                            if (room.fasilitas) {
                                const cleanedString = room.fasilitas.slice(1, -1);
                                const facilitiesArray = cleanedString.split(/"(.*?)",|,/).filter(facilit => facilit);
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
            this.ruanganEdit.Foto = ruangan.Foto;
        },
        konfirmasiHapusRuangan(RuanganID, Nama_ruangan) {
            this.dialogHapusRuangan = true;
            this.ruanganHapus = {
                RuanganID,
                Nama_ruangan
            }
        },
        deleteRuangan(RuanganID) {
            axios.delete(`http://127.0.0.1:8000/api/ruangan/${RuanganID}`)
                .then(response => {
                    console.log("Ruangan deleted successfully:", response.data);
                    this.dialogHapusRuangan = false;
                }).catch(error => {
                    console.error("Error deleting Ruangan:", error);
                    this.gagalDeleteRuangan = true;
                    this.dialogHapusRuangan = false;
                });
        }
    },
    mounted() {
        this.getAllDataofRoom()
    },
}
</script>

<style lang="scss" scoped></style>
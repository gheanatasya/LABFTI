<template>
    <headerSuperAdmin style="z-index: 1;"></headerSuperAdmin>

    <div style="font-family: 'Lexend-Medium'; font-size: 25px; text-align: center; margin-top: 30px;"> Daftar Petugas
        LAB
        FTI UKDW </div>

    <div style="margin-top: 30px;">
        <v-row>
            <v-col>
                <v-text-field v-model="this.searchPetugas" append-inner-icon="mdi-magnify" density="compact"
                    label="Cari petugas" variant="solo" hide-details single-line
                    style="width: 500px; margin-left: 50px; font-family: Lexend-Regular;"></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>
            <v-spacer></v-spacer>

            <v-col>
                <v-btn style="text-transform: none; font-family: Lexend-Regular; background-color: rgb(2,39, 10, 0.9); color:white;
                    margin-right: 50px;" @click="this.tambahActionPetugas = true"><v-icon>mdi-plus</v-icon>Tambah
                    Petugas
                </v-btn>
            </v-col>
        </v-row>
    </div>

    <div style="margin-top: 20px; margin-left: 50px; margin-right: 50px;">
        <v-table>
            <thead style="font-family: Lexend-Regular; font-size: 15px;">
                <tr>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">No</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Nama Petugas</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">NIM</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Email</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Program Studi</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Tgl Bekerja</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Tgl Berhenti</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Foto</th>
                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(petugas, index) in filteredPetugas" :key="index"
                    style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                    <td style="width: 20px; text-align: center;"> {{ index + 1 }} </td>

                    <td style="width: 150px;"> {{ petugas.Nama }} </td>

                    <td style="width: 100px; text-align: center;"> {{ petugas.NIM }} </td>

                    <td style="width: 100px; text-align: center;"> {{ petugas.Email }} </td>

                    <td style="width: 100px; text-align: center;"> {{ petugas.Prodi }} </td>

                    <td style="width: 100px; text-align: center;"> {{ petugas.Tgl_Bekerja }} </td>

                    <td style="width: 100px; text-align: center;"> {{ petugas.Tgl_Berhenti }} </td>

                    <td style="width: 100px; text-align: center;"> {{ petugas.Foto }} </td>

                    <td style="width: 100px; font-size: 25px; text-align: center;">
                        <v-icon style="color: rgb(2, 39, 10, 1);"
                            @click="konfirmasieditDataPetugas(petugas.Nama, petugas.NIM, petugas.Email, petugas.Prodi, petugas.Tgl_Bekerja, petugas.Tgl_Berhenti, petugas.Foto, petugas.UserID)">mdi-pencil-circle</v-icon>
                        <v-icon @click="konfirmasiHapusPetugas(petugas.UserID, petugas.Nama)"
                            style="color: rgb(206, 0, 0, 0.91);">mdi-delete-circle</v-icon>
                    </td>
                </tr>
            </tbody>
        </v-table>

        <!-- edit petugas -->
        <v-dialog style="justify-content:center; margin-top: -50px;" v-model="editActionPetugas" persistent
            padding-left="80px" max-width="800">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Edit Petugas</v-card-title>
                <v-card-text style="text-align: center;">
                    <v-text-field label="Nama Petugas" v-model="this.petugasEdit.Nama" variant="outlined" readonly
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="NIM" v-model="this.petugasEdit.NIM" variant="outlined" readonly
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="Email" v-model="this.petugasEdit.Email" variant="outlined" readonly
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field v-model="this.petugasEdit.Prodi" readonly variant="outlined"
                        style="margin-right: 100px; margin-left:40px;" label="Program Studi">
                    </v-text-field>

                    <v-text-field label="Tgl Bekerja" v-model="this.petugasEdit.Tgl_Bekerja" variant="outlined"
                        type="date" style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="Tgl Berhenti" v-model="this.petugasEdit.Tgl_Berhenti" variant="outlined"
                        type="date" style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-file-input label="Foto" variant="outlined" v-model="this.petugasEdit.Foto"
                        style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="editActionPetugas = false">Batal</v-btn>
                    <v-btn
                        @click="updatePetugas(petugasEdit.Nama, petugasEdit.NIM, petugasEdit.Email, petugasEdit.Prodi, petugasEdit.Tgl_Bekerja, petugasEdit.Tgl_Berhenti, petugasEdit.Foto, petugasEdit.UserID)"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Simpan</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- konfirmasi hapus petugas -->
        <v-dialog style="justify-content:center;" v-model="dialogHapusPetugas" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Konfirmasi
                    Penghapusan</v-card-title>
                <v-card-text style="text-align: center;">Yakin ingin menghapus {{
                    petugasHapus.Nama }} ?</v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="dialogHapusPetugas = false">Batal</v-btn>
                    <v-btn
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;"
                        @click="deletePetugas(petugasHapus.UserID)">Hapus</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- gagal hapus petugas -->
        <v-dialog style="justify-content:center;" v-model="gagalDeletePetugas" persistent max-width="290">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 400px;">
                <v-card-actions class="d-flex justify-end">
                    <v-icon @click="gagalDeletePetugas = false">mdi-close-circle</v-icon>
                </v-card-actions>
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">Gagal Melakukan
                    Penghapusan</v-card-title>
                <v-card-text style="text-align: center;">Gagal menghapus petugas </v-card-text>
            </v-card>
        </v-dialog>

        <!-- tambah petugas -->
        <v-dialog style="justify-content:center; margin-top: -50px;" v-model="tambahActionPetugas" persistent
            padding-left="80px" max-width="800">
            <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                    Tambah Petugas</v-card-title>
                <v-card-text style="text-align: center;">
                    <v-text-field label="Email" v-model="this.petugasTambah.Email" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="NIM" v-model="this.petugasTambah.NIM" variant="outlined"
                        style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-text-field label="Tgl Bekerja" v-model="this.petugasTambah.Tgl_Bekerja" variant="outlined"
                        type="date" style="margin-right: 100px; margin-left:40px;"></v-text-field>

                    <v-file-input label="Foto" variant="outlined" v-model="this.petugasTambah.Foto"
                        style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="tambahActionPetugas = false">Batal</v-btn>
                    <v-btn @click="tambahPetugas(petugasTambah)"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Simpan</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </div>

    <footerPage></footerPage>
</template>

<script>
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import footerPage from '../components/footerPage.vue'

export default {
    name: "daftarPetugas",
    components: {
        headerSuperAdmin,
        footerPage
    },
    data() {
        return {
            petugasTambah: null,
            searchPetugas: '',
            allPetugas: [],
            editActionPetugas: false,
            petugasEdit: null,
            dialogHapusPetugas: false,
            petugasHapus: null,
            gagalDeletePetugas: false,
            tambahActionPetugas: false,
            petugasTambah: {
                Email: null,
                NIM: null,
                Tgl_Bekerja: null,
                Foto: null,
                Tgl_Berhenti: null
            }
        }
    },
    methods: {
        async getAllPetugas() {
            try {
                await axios.get("http://127.0.0.1:8000/api/petugas/")
                    .then(response => {
                        this.allPetugas = response.data;
                        console.log(this.allPetugas);
                    })
                    .catch(error => {
                        console.error("Error gagal mengambil data semua petugas", error);
                    });
            } catch {
                console.error()
            }
        },
        konfirmasieditDataPetugas(Nama, NIM, Email, Prodi, Tgl_Bekerja, Tgl_Berhenti, Foto, UserID) {
            this.editActionPetugas = true;
            this.petugasEdit = {
                Nama,
                NIM,
                Email,
                Prodi,
                Tgl_Bekerja,
                Tgl_Berhenti,
                Foto,
                UserID
            }
        },
        updatePetugas(Nama, NIM, Email, Prodi, Tgl_Bekerja, Tgl_Berhenti, Foto, UserID) {
            const updateData = {
                Nama,
                NIM,
                Email,
                Prodi,
                Tgl_Bekerja,
                Tgl_Berhenti,
                Foto,
                UserID
            }

            console.log(updateData)

            axios.put(`http://127.0.0.1:8000/api/petugas/${UserID}`, updateData, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).
                then(response => {
                    if (response.status === 200) {
                        console.log("Petugas updated successfully:", response.data);
                        this.editActionPetugas = false;
                    } else {
                        console.error("Error updating Petugas:", response.data.message);
                    }
                })
                .catch(error => {
                    console.error("Error updating Petugas:", error);
                });
        },
        konfirmasiHapusPetugas(UserID, Nama) {
            this.dialogHapusPetugas = true
            this.petugasHapus = {
                UserID,
                Nama
            };
        },
        deletePetugas(UserID) {
            console.log(UserID);

            axios.delete(`http://127.0.0.1:8000/api/petugas/${UserID}`)
                .then(response => {
                    console.log("Petugas deleted successfully:", response.data);
                    this.dialogHapusPetugas = false;
                }).catch(error => {
                    console.error("Error deleting Ruangan:", error);
                    this.gagalDeletePetugas = true;
                    this.dialogHapusPetugas = false;
                });
        },
        tambahPetugas(petugasTambah) {
            console.log(petugasTambah)

            axios.post(`http://127.0.0.1:8000/api/petugas/`, petugasTambah)
                .then(response => {
                    console.log("Petugas ditambahkan successfully:", response.data);
                    this.tambahActionPetugas = false;
                }).catch(error => {
                    console.error("Data gagal ditambahkan", error);
                });
        }
    },
    mounted() {
        this.getAllPetugas()
    },
    computed: {
        filteredPetugas() {
            return this.allPetugas.filter(petugas => {
                return petugas.Nama.toLowerCase().includes(this.searchPetugas.toLowerCase());
            });
        }
    },
}
</script>

<style lang="scss" scoped></style>
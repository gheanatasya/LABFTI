<template>
    <headerSuperAdmin style="z-index: 1; position: fixed; width: 100%;"></headerSuperAdmin>

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

    <div style="font-family: 'Lexend-Medium'; font-size: 25px; text-align: center; padding-top: 100px;"> Daftar Petugas
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
        <v-card>
            <v-table style="height: 400px;">
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
                <tbody v-if="this.filteredPetugas.length > 0">
                    <tr v-for="(petugas, index) in filteredPetugas" :key="index"
                        style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                        <td style="width: 20px; text-align: center;"> {{ index + 1 }} </td>

                        <td style="width: 150px;"> {{ petugas.Nama }} </td>

                        <td style="width: 100px; text-align: center;"> {{ petugas.NIM }} </td>

                        <td style="width: 100px; text-align: center;"> {{ petugas.Email }} </td>

                        <td style="width: 100px; text-align: center;"> {{ petugas.Prodi }} </td>

                        <td style="width: 100px; text-align: center;"> {{ new Date(petugas.Tgl_Bekerja).toLocaleDateString('id-ID') }} </td>

                        <td v-if="petugas.Tgl_Berhenti !== null" style="width: 100px; text-align: center;"> {{ new Date(petugas.Tgl_Berhenti).toLocaleDateString('id-ID') }} </td>
                        <td v-else style="width: 100px; text-align: center;"> {{ petugas.Tgl_Berhenti }} </td>

                        <td style="width: 100px; text-align: center;">
                            <a :href="'../storage/' + petugas.Foto" target="_blank">
                                <v-img :src="'../storage/' + petugas.Foto" style="width: 200px; height: 200px;"
                                    cover></v-img>
                            </a>
                        </td>

                        <td style="width: 100px; font-size: 25px; text-align: center;">
                            <v-icon style="color: rgb(2, 39, 10, 1);"
                                @click="konfirmasieditDataPetugas(petugas.Nama, petugas.NIM, petugas.Email, petugas.Prodi, petugas.Tgl_Bekerja, petugas.Tgl_Berhenti, petugas.Foto, petugas.UserID)">mdi-pencil-circle</v-icon>
                            <v-icon @click="konfirmasiHapusPetugas(petugas.UserID, petugas.Nama)"
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
                </tbody>
            </v-table>
        </v-card>

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

                    <v-file-input label="Foto" variant="outlined" v-model="this.petugasEdit.Foto" id="editFotoPetugas"
                        style="margin-right: 100px; margin-left:0px;"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="editActionPetugas = false">Batal</v-btn>
                    <v-btn :loading="this.loadingEdit"
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
                    <v-btn :loading="this.loadingHapus"
                        style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;"
                        @click="deletePetugas(petugasHapus.UserID), this.loadingHapus = true">Hapus</v-btn>
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

                    <v-file-input label="Foto" variant="outlined" v-model="this.petugasTambah.Foto" accept="file/img"
                        style="margin-right: 100px; margin-left:0px;" id="fotoPetugasTambah"></v-file-input>
                </v-card-text>
                <v-card-actions style="justify-content:center;">
                    <v-btn
                        style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                        @click="tambahActionPetugas = false">Batal</v-btn>
                    <v-btn @click="tambahPetugas(petugasTambah)" :loading="this.loadingTambah"
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
            searchPetugas: '',
            allPetugas: [],
            editActionPetugas: false,
            petugasEdit: {
                Nama: null,
                NIM: null,
                Email: null,
                Prodi: null,
                Tgl_Bekerja: null,
                Tgl_Berhenti: null,
                Foto: null,
                UserID: null,
            },
            dialogHapusPetugas: false,
            petugasHapus: {
                UserID: null,
                Nama: null,
            },
            gagalDeletePetugas: false,
            tambahActionPetugas: false,
            petugasTambah: {
                Email: null,
                NIM: null,
                Tgl_Bekerja: null,
                Foto: null,
                Tgl_Berhenti: null,
            },
            overlay: true,
            loadingTambah: false,
            loadingHapus: false,
            loadingEdit: false
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
                UserID,
                loading: false
            }
        },
        updatePetugas(Nama, NIM, Email, Prodi, Tgl_Bekerja, Tgl_Berhenti, Foto, UserID) {
            this.loadingEdit = true;
            console.log(Foto)
            if (Nama === '' || NIM === '' || Email === '' || Prodi === '' || Tgl_Bekerja === '') {
                this.loadingEdit = false;
                alert('Terdapat data yang belum diisi!');
                return
            }

            const imageRegex1 = /\.png$/i;
            const imageRegex2 = /\.img$/i;
            const imageRegex3 = /\.jpg$/i;
            const formData = new FormData();

            if (Foto.name && Foto.size && Foto.type) {
                const file = document.getElementById('editFotoPetugas');
                formData.append('foto', file.files[0]);
                formData.append('userid', UserID);
                //console.log('ada')
            }

            if (imageRegex1.test(Foto.name) || imageRegex2.test(Foto.name) || imageRegex3.test(Foto.name)) {
                console.log('file aman')
            } else {
                alert('File harus berupa gambar!');
                this.loadingEdit = false
                return
            }

            const updateData = {
                Nama,
                NIM,
                Email,
                Prodi,
                Tgl_Bekerja,
                Tgl_Berhenti,
                UserID
            }

            //console.log(updateData)

            axios.put(`http://127.0.0.1:8000/api/petugas/${UserID}`, updateData, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).
                then(response => {
                    if (response.status === 200) {
                        console.log("Petugas updated successfully:", response.data);
                        if (Foto.name && Foto.size && Foto.type) {
                            axios.post(`http://127.0.0.1:8000/api/petugas/tambahFoto/`, formData)
                                .then(res => {
                                    console.log("Petugas picture updated successfully:", res.data);
                                    const dataGanti = res.data.data
                                    const index = this.allPetugas.findIndex(petugas => petugas.UserID === dataGanti.UserID);
                                    if (index !== -1) {
                                        this.allPetugas[index] = dataGanti;
                                    }
                                    this.loadingEdit = false;
                                    this.editActionPetugas = false;
                                })
                                .catch(err => {
                                    console.error("Error updating Petugas:", err);
                                    this.loadingEdit = false;
                                });

                            return
                        }

                        const dataGanti = response.data.data
                        const index = this.allPetugas.findIndex(petugas => petugas.UserID === dataGanti.UserID);
                        if (index !== -1) {
                            this.allPetugas[index] = dataGanti;
                        }

                        this.editActionPetugas = false;
                        this.loadingEdit = false;
                    } else {
                        console.error("Error updating Petugas:", response.data.message);
                        this.loadingEdit = false;
                    }
                })
                .catch(error => {
                    console.error("Error updating Petugas:", error);
                    this.loadingEdit = false;
                });
        },
        konfirmasiHapusPetugas(UserID, Nama) {
            this.dialogHapusPetugas = true
            this.petugasHapus = {
                UserID,
                Nama,
                loading: false
            };
        },
        deletePetugas(UserID) {
            console.log(UserID);
            this.loadingHapus = true;
            axios.delete(`http://127.0.0.1:8000/api/petugas/${UserID}`)
                .then(response => {
                    console.log("Petugas deleted successfully:", response.data);
                    const dataGanti = response.data.UserID
                    const index = this.allPetugas.findIndex(petugas => petugas.UserID === dataGanti);
                    if (index !== -1) {
                        this.allPetugas.splice(index, 1);
                    }
                    this.loadingHapus = false;
                    this.dialogHapusPetugas = false;
                }).catch(error => {
                    console.error("Error deleting Ruangan:", error);
                    this.loadingHapus = false;
                    this.gagalDeletePetugas = true;
                    this.dialogHapusPetugas = false;
                });
        },
        tambahPetugas(petugasTambah) {
            this.loadingTambah = true;
            console.log(petugasTambah)
            console.log(petugasTambah.Foto)
            console.log(petugasTambah.Foto.name)
            const imageRegex1 = /\.png$/i;
            const imageRegex2 = /\.img$/i;
            const imageRegex3 = /\.jpg$/i;


            if (petugasTambah.Email !== null && petugasTambah.Email !== undefined && petugasTambah.Email !== '') {
                const domain = petugasTambah.Email.split('@')[1];
                if (domain === 'ti.ukdw.ac.id' || domain === 'si.ukdw.ac.id') {
                    console.log('aman')
                } else {
                    alert('Gunakan email yang valid!');
                    this.loadingTambah = false
                    return
                }
            } else if (petugasTambah.Email) {
                alert('Email harus diisi!');
                this.loadingTambah = false;
                return
            }

            if (petugasTambah.Email === null || petugasTambah.NIM === null || petugasTambah.Tgl_Bekerja === null || petugasTambah.Foto === null) {
                alert('Terdapat data yang belum diisi!');
                this.loadingTambah = false;
                return
            } else if (isNaN(petugasTambah.NIM)) {
                alert('NIM harus berupa angka!');
                this.loadingTambah = false;
                return
            }

            if (imageRegex1.test(petugasTambah.Foto.name) || imageRegex2.test(petugasTambah.Foto.name) || imageRegex3.test(petugasTambah.Foto.name)) {
                console.log('file aman')
            } else {
                alert('File harus berupa format gambar!');
                this.loadingTambah = false
                return
            }


            const formData = new FormData();
            const file = document.getElementById('fotoPetugasTambah');;
            formData.append('foto', file.files[0]);

            axios.post(`http://127.0.0.1:8000/api/petugas/`, petugasTambah)
                .then(response => {
                    console.log(response.data)
                    if (response.data.status !== false) {
                        formData.append('userid', response.data.UserID);
                        console.log("Petugas ditambahkan successfully:", response.data);
                        axios.post(`http://127.0.0.1:8000/api/petugas/tambahFoto/`, formData)
                            .then(res => {
                                console.log("Foto ditambahkan successfully:", res.data);
                                const newData = res.data.data
                                let index = 0;
                                while (index < this.allPetugas.length &&
                                    this.allPetugas[index].NIM < newData.NIM) {
                                    index++;
                                }
                                this.allPetugas.splice(index, 0, newData);

                                this.loadingTambah = false;
                                this.tambahActionPetugas = false;
                                this.petugasTambah.Email = null
                                this.petugasTambah.NIM = null
                                this.petugasTambah.Tgl_Bekerja = null
                                this.petugasTambah.Foto = null
                                this.petugasTambah.Tgl_Berhenti = null
                            }).catch(error => {
                                console.error("Foto gagal ditambahkan", error);
                                this.loadingTambah = false;
                            })
                    } else {
                        console.error("Data gagal ditambahkan", error);
                        alert('User dengan email dan NIM tersebut tidak ditemukan!');
                        this.petugasTambah.Email = null
                        this.petugasTambah.NIM = null
                        this.petugasTambah.Tgl_Bekerja = null
                        this.petugasTambah.Foto = null
                        this.petugasTambah.Tgl_Berhenti = null
                        this.loadingTambah = false;
                        return
                    }
                }).catch(error => {
                    console.error("Data gagal ditambahkan", error);
                    alert('User dengan email dan NIM tersebut tidak ditemukan!');
                    this.petugasTambah.Email = null
                    this.petugasTambah.NIM = null
                    this.petugasTambah.Tgl_Bekerja = null
                    this.petugasTambah.Foto = null
                    this.petugasTambah.Tgl_Berhenti = null
                    this.loadingTambah = false;
                });
        }
    },
    mounted() {
        Promise.all([
            this.getAllPetugas()
        ])
            .then(() => {
                this.overlay = false;
            })
            .catch((error) => {
                console.error(error);
            });
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
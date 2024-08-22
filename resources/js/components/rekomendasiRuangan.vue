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

    <v-dialog v-model="showRekomendasi" max-width="700" persistent>
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 700px; height: 480px;">
            <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                Butuh ruangan yang bagaimana?</v-card-title>

            <v-card-text style="text-align: center;">
                <v-text-field type="datetime-local" label="Tanggal Pakai Awal" v-model="tanggalAwal" clearable
                    variant="outlined"
                    style="background-color: rgb(3, 138, 33, 0.3); color: black; height: 56px; margin-bottom: 25px;"></v-text-field>

                <v-text-field type="datetime-local" label="Tanggal Selesai" v-model="tanggalSelesai" clearable
                    variant="outlined"
                    style="background-color: rgb(3, 138, 33, 0.3); color: black; height: 56px; margin-bottom: 25px;"></v-text-field>

                <v-select v-model="selectedKapasitas" :items="kapasitas" label="Kapasitas" variant="outlined" clearable
                    style="background-color: rgb(3, 138, 33, 0.3); color: black; height: 56px; margin-bottom: 25px;">
                </v-select>

                <v-select v-model="selectedLokasi" :items="lokasi" label="Lokasi" variant="outlined" clearable
                    style="background-color: rgb(3, 138, 33, 0.3); color: black; height: 56px; margin-bottom: 25px;">
                </v-select>

                <v-select v-model="selectedKategori" :items="kategori" label="Kategori" variant="outlined" clearable
                    style="background-color: rgb(3, 138, 33, 0.3); color: black; height: 56px; margin-bottom: 25px;">
                </v-select>

                <v-select v-model="selectedFasilitas" :items="fasilitas" label="Fasilitas" variant="outlined" clearable
                    style="background-color: rgb(3, 138, 33, 0.3); color: black; height: 56px; margin-bottom: 25px;">
                </v-select>
            </v-card-text>

            <v-card-actions style="justify-content:center;">
                <v-btn style="position: absolute; top: 0; left: 0; margin-top: 17px;" @click="navigateBack"><v-icon
                        style="font-size: 30px;">mdi-arrow-left</v-icon></v-btn>
                <v-btn :loading="this.loading"
                    @click="jadwalPeminjaman(tanggalAwal, tanggalSelesai, selectedKapasitas, selectedKategori, selectedLokasi), this.loading = true"
                    style="bottom: 20px; right: 0px; background-color: rgb(2,39, 10, 0.9); color: white;
                border-radius: 20px; width: 150px;">Terapkan</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="roomAfterSelected" max-width="850" persistent>
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; width: 880px; height: 450px;">
            <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                Terdapat {{ this.fixRooms.length }} rekomendasi ruangan yang sesuai</v-card-title>
            <v-card-text style="text-align: center;">
                <v-row cols="10">
                    <v-col v-for="(room, index) in this.fixRooms" :key="index" cols="5" style="margin-left: 0px;">
                        <v-card
                            style="width: 300px; background-color: rgb(3, 138, 33, 0.3); border-radius: 20px; margin-left: 25px;">
                            <v-img :src="this.picture" style="width: 40%; height: 100%; margin-left: 90px;"
                                cover></v-img>
                            <v-card-text style="font-size: 18px;">
                                <p>{{ room.Nama_ruangan }}</p>
                                <p>{{ room.Lokasi }}</p>
                            </v-card-text>
                            <v-card-actions>
                                <div style="position: absolute; bottom: 0; right: 60px; margin-bottom: 0px;">
                                    <v-btn style="background-color: rgb(2,39, 10, 0.9); color: white; border-radius: 20px; margin-left: 90px; width: 130px; height: 25px; margin-bottom: -10px;
                            font-size: 10px;" @click="pinjamRuang">Pinjam Ruangan</v-btn>
                                    <br>
                                    <v-btn @click="navigateToRuangan" style="color: rgb(2,39, 10, 0.9); margin-left: 90px; background: none;
                                text-decoration: underline; box-shadow: none; font-size: 12px;
                                ">L<p style="text-transform: lowercase;">ihat detail ruangan>>
                                        </p></v-btn>
                                </div>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>

            </v-card-text>
            <v-card-actions style="justify-content:center;">
                <v-btn style="position: absolute; top: 0; left: 0; margin-top: 17px;" @click="navigateBack"><v-icon
                        style="font-size: 30px;">mdi-arrow-left</v-icon></v-btn>
                <v-btn @click="navigateToPeminjaman"
                    style="position: absolute; top: 0; right: 0; margin-top: 17px;"><v-icon
                        style="font-size: 30px;">mdi-close-circle</v-icon></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import headerUser from '../components/headerUser.vue'
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerDekanat from '../components/headerDekanat.vue'
import headerAdmin from '../components/headerAdmin.vue'
import axios from 'axios'

export default {
    name: 'rekomendasiRuangan',
    components: {
        headerUser,
        headerAdmin,
        headerDekanat,
        headerSuperAdmin
    },
    data() {
        return {
            showRekomendasi: true,
            lokasi: ['Lab FTI 2', 'Lab FTI 3', 'Fakultas', 'Lab FTI 4'],
            kapasitas: ['1-4 Orang', '5-10 Orang', '11-20 Orang', '21-40 Orang'],
            kategori: ['Ruang Diskusi/Rapat', 'Ruang Perkuliahan', 'Ruang Bebas'],
            fasilitas: ['TV', 'Proyektor', 'TV dan Proyektor'],
            selectedFasilitas: '',
            selectedKapasitas: '',
            selectedKategori: '',
            selectedLokasi: '',
            allRoomsData: [],
            roomAfterSelected: false,
            picture: './picture/regis-login.jpeg',
            filteredRoom: [],
            loading: false,
            User_role: localStorage.getItem('User_role'),
            fixRooms: [],
            tanggalAwal: '',
            tanggalSelesai: '',
        }
    },
    methods: {
        navigateBack() {
            this.roomAfterSelected = false;
            this.showRekomendasi = true;
            this.$router.push('/peminjamanRuangan')
        },
        navigateToPeminjaman() {
            this.roomAfterSelected = false;
            this.$router.push('/peminjamanRuangan')
        },
        navigateToRuangan() {
            this.roomAfterSelected = false;
            this.$router.push('/ruangan')
        },
 /*        async getAllDataofRoom() {
            try {
                await axios.get("http://127.0.0.1:8000/api/ruangan/")
                    .then(response => {
                        this.allRoomsData = response.data.map(room => {
                            if (room.fasilitas) {
                                const facilitiesArray = room.fasilitas.split(", ").filter(facilit => facilit);
                                room.fasilitas = facilitiesArray;
                            }
                            return room;
                        });
                        console.log(this.allRoomsData);
                    })
                    .catch(error => {
                        console.error("Error gagal mengambil data total ruangan", error);
                    });
            } catch {
                console.error()
            }
        }, */
        async jadwalPeminjaman() {
            try {
                if (this.User_role !== null) {
                    await axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjamanforRekomendasiDosen/${this.tanggalAwal}/${this.tanggalSelesai}/${this.selectedKapasitas}/${this.selectedKategori}/${this.selectedLokasi}`)
                        .then(response => {
                            this.fixRooms = response.data.fixRoom;
                            console.log(this.fixRooms);
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error("Error gagal mengambil data jadwal peminjaman ruangan", error);
                            this.loading = false;
                        });
                }
                this.showRekomendasi = false;
                this.roomAfterSelected = true;
            } catch {
                console.error()
                this.loading = false;
            }
        },
        filteredData() {
            let filteredData = this.allRoomsData;

            //filter berdasarkan lokasi
            if (this.selectedLokasi) {
                filteredData = filteredData.filter((room) => room.Lokasi === this.selectedLokasi);
            }

            //filter berdasarkan kategori
            if (this.selectedKategori) {
                filteredData = filteredData.filter((room) => room.Kategori === this.selectedKategori);
            }

            //filter berdasarkan kapasitas
            if (this.selectedKapasitas) {
                filteredData = filteredData.filter((room) => room.Kapasitas === this.selectedKapasitas);
            }

            console.log(filteredData);
            return filteredData;
        },
        navigateToFilteredRooms(filteredRooms) {
            this.loading = true;
            this.showRekomendasi = false;
            this.roomAfterSelected = true;
            this.filteredRoom = filteredRooms;
            console.log(this.filteredRoom);
        },
        pinjamRuang() {
            this.$router.push('/peminjamanRuangan');
        },
    },
    mounted() {
        this.getAllDataofRoom()
    },
}
</script>

<style lang="scss" scoped></style>
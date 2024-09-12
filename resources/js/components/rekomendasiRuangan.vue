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
                    style="background-color: #BBDEFB; color: black; height: 56px; margin-bottom: 25px;"></v-text-field>

                <v-text-field type="datetime-local" label="Tanggal Selesai" v-model="tanggalSelesai" clearable
                    variant="outlined"
                    style="background-color: #BBDEFB; color: black; height: 56px; margin-bottom: 25px;"></v-text-field>

                <v-select v-model="selectedKapasitas" :items="kapasitas" label="Kapasitas" variant="outlined" clearable
                    style="background-color: #BBDEFB; color: black; height: 56px; margin-bottom: 25px;">
                </v-select>

                <v-select v-model="selectedLokasi" :items="lokasi" label="Lokasi" variant="outlined" clearable
                    style="background-color: #BBDEFB; color: black; height: 56px; margin-bottom: 25px;">
                </v-select>

                <v-select v-model="selectedKategori" :items="kategori" label="Kategori" variant="outlined" clearable
                    style="background-color: #BBDEFB; color: black; height: 56px; margin-bottom: 25px;">
                </v-select>

            </v-card-text>

            <v-card-actions style="justify-content:center;">
                <v-btn style="position: absolute; top: 0; left: 0; margin-top: 17px;" @click="navigateBackPeminjaman"><v-icon
                        style="font-size: 30px;">mdi-arrow-left</v-icon></v-btn>
                <v-btn :loading="this.loading"
                    @click="jadwalPeminjaman(tanggalAwal, tanggalSelesai, selectedKapasitas, selectedKategori, selectedLokasi)"
                    style="bottom: 20px; right: 0px; background-color: #0D47A1; color: white;
                border-radius: 20px; width: 150px;">Terapkan</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="roomAfterSelected" max-width="850" persistent>
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; width: 880px; height: 450px;">
            <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                Terdapat {{ Object.keys(fixRooms).length }} rekomendasi ruangan yang sesuai</v-card-title>
            <v-card-text style="text-align: center;">
                <v-row cols="10">
                    <v-col v-for="(room, index) in this.fixRooms" :key="index" cols="5" style="margin-left: 0px;">
                        <v-card
                            style="width: 300px; background-color: #BBDEFB; border-radius: 20px; margin-left: 25px;">
                            <v-img :src="this.picture" style="width: 40%; height: 100%; margin-left: 90px;"
                                cover></v-img>
                            <v-card-text style="font-size: 18px;">
                                <p>{{ room.Nama_ruangan }}</p>
                                <p>{{ room.Lokasi }}</p>
                            </v-card-text>
                            <v-card-actions>
                                <div style="position: absolute; bottom: 0; right: 60px; margin-bottom: 0px;">
                                    <v-btn style="background-color: #0D47A1; color: white; border-radius: 20px; margin-left: 90px; width: 130px; height: 25px; margin-bottom: -10px;
                            font-size: 10px;" @click="pinjamRuang(room.Nama_ruangan)">Pinjam Ruangan</v-btn>
                                    <br>
                                    <v-btn @click="navigateToRuangan" style="color: #0D47A1; margin-left: 90px; background: none;
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
            kategori: ['Ruang Diskusi(Rapat)', 'Ruang Perkuliahan', 'Ruang Bebas'],
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
        },
        navigateBackPeminjaman() {
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
        async jadwalPeminjaman() {
            this.loading = true
            if (this.tanggalAwal > this.tanggalSelesai){
                alert('Tanggal awal peminjaman melebihi tanggal selesai peminjaman!');
                this.loading = false
                return
            } else if (this.tanggalAwal === '' || this.tanggalSelesai === '' || this.selectedKapasitas === null || this.selectedKategori === null || this.selectedLokasi === null){
                alert('Terdapat data yang kosong!')
                this.loading = false
                return
            } 

            try {
                if (this.User_role === 'Mahasiswa' || this.User_role === 'Petugas') {
                    await axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjamanforRekomendasi/${this.tanggalAwal}/${this.tanggalSelesai}/${this.selectedKapasitas}/${this.selectedKategori}/${this.selectedLokasi}`)
                        .then(response => {
                            console.log(this.tanggalAwal, this.tanggalSelesai, this.selectedKapasitas, this.selectedKategori, this.selectedLokasi);
                            this.fixRooms = response.data.fixRoom;
                            console.log(this.fixRooms);
                            this.loading = false;
                            this.showRekomendasi = false;
                            this.roomAfterSelected = true;
                        })
                        .catch(error => {
                            console.error("Error gagal mengambil data jadwal peminjaman ruangan", error);
                            this.loading = false;
                        });
                } else {
                    await axios.get(`http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjamanforRekomendasiDosen/${this.tanggalAwal}/${this.tanggalSelesai}/${this.selectedKapasitas}/${this.selectedKategori}/${this.selectedLokasi}`)
                        .then(response => {
                            this.fixRooms = response.data.fixRoom;
                            console.log(this.fixRooms);
                            this.loading = false;
                            this.showRekomendasi = false;
                            this.roomAfterSelected = true;
                        })
                        .catch(error => {
                            console.error("Error gagal mengambil data jadwal peminjaman ruangan", error);
                            this.loading = false;
                        });
                }
            } catch {
                console.error()
                this.loading = false;
            }
        },
        pinjamRuang(Nama_ruangan) {
            this.$router.push({
                name: 'peminjamanRuanganRekomendasi',
                query: {
                    tanggalAwal: this.tanggalAwal,
                    tanggalSelesai: this.tanggalSelesai,
                    Nama_ruangan: Nama_ruangan
                }
            });
        },
    }
}
</script>

<style lang="scss" scoped></style>
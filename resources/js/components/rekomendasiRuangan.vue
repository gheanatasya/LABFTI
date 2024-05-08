<template>
    <headerUser style="z-index: 1"></headerUser>

    <v-dialog v-model="showRekomendasi" max-width="700" persistent>
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 700px; height: 480px;">
            <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                Butuh ruangan yang bagaimana?</v-card-title>

            <v-card-text style="text-align: center;">
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
                <v-btn @click="navigateToFilteredRooms(filteredData())" style="position: absolute; bottom: 30px; right: 35px; background-color: rgb(2,39, 10, 0.9); color: white;
                border-radius: 20px; width: 150px;">Terapkan</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="roomAfterSelected" max-width="850" persistent>
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; width: 880px; height: 450px;">
            <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                Terdapat {{ filteredRoom.length }} rekomendasi ruangan yang sesuai</v-card-title>
            <v-card-text style="text-align: center;">
                <v-row cols="10">
                    <v-col v-for="(room, index) in filteredRoom" :key="index" cols="5" style="margin-left: 0px;">
                        <v-card style="width: 300px; background-color: rgb(3, 138, 33, 0.3); border-radius: 20px; margin-left: 25px;">
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

export default {
    name: 'rekomendasiRuangan',
    components: {
        headerUser,
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
            filteredRoom: []
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
        async getAllDataofRoom() {
            try {
                await axios.get("http://127.0.0.1:8000/api/ruangan/")
                    .then(response => {
                        this.allRoomsData = response.data.map(room => {
                            if (room.fasilitas) {
                                const cleanedString = room.fasilitas.slice(1, -1);
                                const facilitiesArray = cleanedString.split(/"(.*?)",|,/).filter(facilit => facilit);
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

            //filter berdasarkan fasilitas
            /* if (this.selectedFasilitas) { */
            /*     filteredData = filteredData.filter((room) => room.fasilitas.some(facilitas => facilitas === this.selectedFasilitas)); */
            /* } */

            console.log(filteredData);
            return filteredData;
        },
        navigateToFilteredRooms(filteredRooms) {
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
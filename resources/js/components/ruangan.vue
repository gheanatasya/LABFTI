<template>
    <headerUser></headerUser>

    <div style="margin-top: 100px;">
        <p style="font-family: 'Lexend-Medium'; font-size: 25px; margin-top: -20px; margin-left: 30px">Ruangan</p>

        <div id="filter" style="margin-top: 30px; margin-right: 60px;">
            <v-row style="font-family: 'Lexend-Regular';">
                <v-spacer></v-spacer>

                <v-col>
                    <p style="margin-left: 200px; font-size: 20px; margin-top: 10px;">
                        <v-icon>mdi-filter-variant</v-icon>Filter
                    </p>
                </v-col>

                <v-col style="margin-left: -15px;">
                    <v-select v-model="this.selectedLokasi" :items="this.lokasi" label="Lokasi" variant="outlined"
                        clearable>
                    </v-select>
                </v-col>

                <v-col>
                    <v-select v-model="this.selectedKapasitas" :items="this.kapasitas" label="Kapasitas"
                        variant="outlined" clearable>
                    </v-select>
                </v-col>

                <v-col>
                    <v-select v-model="this.selectedKategori" :items="this.kategori" label="Kategori" variant="outlined"
                        clearable>
                    </v-select>
                </v-col>
            </v-row>
        </div>

        <div id="cardRuangan">
            <v-row>
                <v-col cols="11">
                    <v-row>
                        <v-col v-for="(room, index) in allRoomsData" :key="index" cols="4">
                            <v-card style="font-family: 'Lexend-Regular'; height: 100%; margin-left: 60px; margin-bottom: 100px; margin-right: -70px;
                            background-color: rgb(30, 30, 30, 0.15); border-radius: 0px;">
                                <div
                                    style="display: flex; align-items: center; grid-column: span 2; width: 100%; height: 100%;">
                                    <div style="font-family: Lexend-Regular'; width: 70%; margin-left: 20px; margin-top: -80px;"
                                        id="detailRuangan">
                                        <p style="text-align: center; font-size: 18px; font-family: 'Lexend-Medium';">{{
                                            room.Nama_ruangan }}</p>
                                        <p style="font-family: Lexend-Regular';">Lokasi : {{ room.Lokasi }}</p>
                                        <p style="font-family: Lexend-Regular';">Kapasitas : {{ room.Kapasitas }}</p>
                                        <p style="font-family: Lexend-Regular';">Kategori : {{ room.Kategori }}</p>
                                        <p style="font-family: Lexend-Regular';">Fasilitas : {{ room.Fasilitas }}</p>

                                        <div style="position: absolute; bottom: 0; left: 0; margin-bottom: 10px;">
                                            <v-btn style="background-color: rgb(2,39, 10, 0.9); color: white; border-radius: 20px; margin-left: 90px;
                                            font-size: 12px;">Pinjam Ruangan</v-btn>
                                            <br>
                                            <v-btn @click="showImageDialog = true"
                                                style="color: rgb(2,39, 10, 0.9); margin-left: 90px; background: none;
                                                text-decoration: underline; box-shadow: none; 
                                                ">L<p style="text-transform: lowercase;">ihat lebih banyak
                                                gambar>></p></v-btn>
                                        </div>
                                    </div>
                                    <v-img src="../picture/regis-login.jpeg" style="width: 30%; height: 100%;"></v-img>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <v-dialog v-model="showImageDialog">
                <v-card>
                    <v-card-title>Gambar Tambahan</v-card-title>
                    <v-card-text>
                        <img src="https://picsum.photos/200" alt="Gambar tambahan">
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="showImageDialog = false">Tutup</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </div>

    <footerPage></footerPage>
</template>

<script>
import axios from 'axios'
import headerUser from '../components/headerUser.vue'
import footerPage from '../components/footerPage.vue'

export default {
    name: "ruangan",
    components: {
        headerUser,
        footerPage
    },
    data() {
        return {
            selectedLokasi: '',
            selectedKapasitas: '',
            selectedKategori: '',
            lokasi: ['Lab FTI 2', 'Lab FTI 3', 'Fakultas', 'Lab FTI 4'],
            kapasitas: ['1-4 Orang', '5-10 Orang', '11-20 Orang', '21-40 Orang'],
            kategori: ['Ruang Diskusi/Rapat', 'Ruang Perkuliahan', 'Ruang Bebas'],
            allRoomsData: [],
            showImageDialog: false,
        }
    },
    methods: {
        getAllDataofRoom() {
            axios.get("http://127.0.0.1:8000/api/ruangan/")
                .then(response => {
                    this.allRoomsData = response.data;
                    console.log(this.allRoomsData);
                })
                .catch(error => {
                    console.error("Error gagal mengambil data total ruangan", error);
                });
        }
    },
    mounted() {
        this.getAllDataofRoom()
    }
}
</script>

<style lang="scss" scoped></style>
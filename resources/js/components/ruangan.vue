<template>
    <headerUser v-if="User_role === 'Mahasiswa' || User_role === 'Dosen' || User_role === 'Staff'" style="z-index: 1; position: fixed; width: 100%;">
    </headerUser>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1; position: fixed; width: 100%;">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1; position: fixed; width: 100%;"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
        style="z-index: 1; position: fixed; width: 100%;"></headerDekanat>

    <div style="margin-top: 100px;">
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
            <v-row v-if="filteredData.length > 0">
                <v-col cols="15">
                    <v-row>
                        <v-col v-for="(room, index) in filteredData" :key="index" cols="5">
                            <v-card style="font-family: 'Lexend-Regular'; height: 80%; margin-left: 100px; margin-bottom: 100px; margin-right: -100px;
                            background-color: rgb(30, 30, 30, 0.15); border-radius: 0px;">
                                <div
                                    style="display: flex; align-items: center; grid-column: span 2; width: 100%; height: 100%;">
                                    <div style="font-family: Lexend-Regular'; width: 60%; margin-left: 20px; margin-top: -80px;"
                                        id="detailRuangan">
                                        <p style="text-align: center; font-size: 18px; font-family: 'Lexend-Medium';">{{
                                            room.Nama_ruangan }}</p>
                                        <p style="font-family: Lexend-Regular';">Lokasi : {{ room.Lokasi }}</p>
                                        <p style="font-family: Lexend-Regular';">Kapasitas : {{ room.Kapasitas }}</p>
                                        <p style="font-family: Lexend-Regular';">Kategori : {{ room.Kategori }}</p>

                                        <div>
                                            Fasilitas:
                                            <p v-for="(facilit, index) in room.fasilitas" :key="index"
                                                style="font-family: Lexend-Regular;">
                                                {{ index + 1 }}. {{ facilit }}
                                            </p>
                                        </div>

                                        <div style="position: absolute; bottom: 0; left: 0; margin-bottom: 10px;">
                                            <v-btn style="background-color: rgb(2,39, 10, 0.9); color: white; border-radius: 20px; margin-left: 90px;
                                            font-size: 12px;" @click="pinjamRuang">Pinjam Ruangan</v-btn>
                                            <br>
                                            <v-btn @click="morePicture(room.Nama_ruangan, room.Lokasi, room.Foto)"
                                                style="color: rgb(2,39, 10, 0.9); margin-left: 90px; background: none;
                                                text-decoration: underline; box-shadow: none; 
                                                ">L<p style="text-transform: lowercase;">ihat lebih banyak
                                                    gambar>></p></v-btn>
                                        </div>
                                    </div>
                                    <v-img v-if="room.Foto != null" :src="'../storage/' + room.Foto[0]"
                                        style="width: 40%; height: 100%;" cover></v-img>
                                    <v-img v-else src="../storage/ruangan/no-image.png"
                                        style="width: 40%; height: 100%;" cover></v-img>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>

            <div v-else style="font-family: 'Lexend-Regular'; text-align: center; margin-top: 50px;">
                <v-icon icon="mdi-magnify" style="font-size: 100px; color: grey"></v-icon>
                <p style="color: grey">Maaf, tidak ada data yang sesuai dengan pencarian.</p>
            </div>

            <v-dialog v-model="showImageDialog">
                <v-container fluid>
                    <v-carousel height="600" show-arrows="hover" cycle hide-delimiter-background>
                        <v-carousel-item v-for="(picture, index) in pictures" :key="index"
                            :src="'../storage/' + picture">
                        </v-carousel-item>
                    </v-carousel>
                    <v-btn icon small style="position: absolute; top: 20px; right:20px;"
                        @click="showImageDialog = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-container>
            </v-dialog>

        </div>
    </div>

    <footerPage></footerPage>
</template>

<script>
import axios from 'axios'
import headerUser from '../components/headerUser.vue'
import footerPage from '../components/footerPage.vue'
import headerAdmin from './headerAdmin.vue'
import headerDekanat from './headerDekanat.vue'
import headerSuperAdmin from './headerSuperAdmin.vue'

export default {
    name: "ruangan",
    components: {
        headerUser,
        footerPage,
        headerAdmin,
        headerDekanat,
        headerSuperAdmin
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
            itemToShow: null,
            pictures: [],
            dataLoaded: false,
            User_role: localStorage.getItem('User_role'),
            overlay: true
        }
    },
    methods: {
        async getAllDataofRoom() {
            try {
                await axios.get("http://127.0.0.1:8000/api/ruangan/")
                    .then(response => {
                        this.allRoomsData = response.data.map(room => {
                            if (room.fasilitas) {
                                //const cleanedString = room.fasilitas.slice(1, -1);
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
        },
        morePicture(Nama_ruangan, Lokasi, Foto) {
            this.showImageDialog = true;
            this.itemToShow = {
                Nama_ruangan,
                Lokasi,
            };
            if (Foto !== null) {
                this.pictures = Foto;
            }
        },
        pinjamRuang() {
            this.$router.push('/peminjamanRuangan');
        },
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

            return filteredData;
        }
    }
}
</script>

<style lang="scss" scoped></style>
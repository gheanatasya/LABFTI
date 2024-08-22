<template>
    <headerUser v-if="User_role === 'Mahasiswa' || User_role === 'Dosen' || User_role === 'Staff'" style="z-index: 1; position: fixed; width: 100%;"></headerUser>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1; position: fixed; width: 100%;"></headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1; position: fixed; width: 100%;"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan'|| User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'" style="z-index: 1; position: fixed; width: 100%;"></headerDekanat>

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

        <p style="font-family: 'Lexend-Medium'; font-size: 25px; margin-top: -20px; margin-left: 30px">Alat</p>

        <div id="filter" style="margin-top: 30px; margin-right: 60px; margin-bottom: 40px;">
            <v-text-field v-model="this.searchAlat" append-inner-icon="mdi-magnify" density="compact" label="Cari alat"
                variant="solo" hide-details single-line style="width: 500px; margin-left: 500px;"></v-text-field>
        </div>

        <div id="cardAlat">
            <v-row v-if="filteredTools.length > 0">
                <v-col cols="12">
                    <v-row style="margin-left: -60px; margin-right: 150px;">
                        <v-col v-for="(tool, index) in filteredTools" :key="index" cols="4">
                            <v-card style="font-family: 'Lexend-Regular'; height: 80%; margin-left: 100px; margin-bottom: 100px; margin-right: -100px;
                            background-color: rgb(30, 30, 30, 0.15); border-radius: 0px;">
                                <div
                                    style="display: flex; align-items: center; grid-column: span 2; width: 100%; height: 100%;">
                                    <div style="font-family: Lexend-Regular'; width: 50%; margin-left: 20px; margin-top: -80px;"
                                        id="detailRuangan">
                                        <p
                                            style="text-align: center; font-size: 20px; font-family: 'Lexend-Medium'; margin-top: 40px;">
                                            {{ tool.Nama }}</p>

                                        <div style="position: absolute; bottom: 0; left: 0; margin-bottom: 10px;">
                                            <v-btn style="background-color: rgb(2,39, 10, 0.9); color: white; border-radius: 20px; margin-left: 50px;
                                            font-size: 12px;" @click="pinjamAlat">Pinjam Alat</v-btn>
                                            <br>
                                            <v-btn @click="morePicture(tool.Nama, tool.Foto)" style="color: rgb(2,39, 10, 0.9); margin-left: 0px; background: none;
                                                text-decoration: underline; box-shadow: none; font-size: 12px;
                                                ">L<p style="text-transform: lowercase;">ihat lebih banyak
                                                    gambar>></p></v-btn>
                                        </div>
                                    </div>
                                    <v-img v-if="tool.Foto.length > 0" :src="'../storage/' + tool.Foto[0]"
                                        style="width: 40%; height: 100%;" cover></v-img>
                                    <v-img v-else src="../storage/ruangan/no-image.png" style="width: 40%; height: 100%;"
                                        cover></v-img>
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
                        <v-carousel-item v-for="(picture, index) in pictures" :key="index" :src="'../storage/' + picture">
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
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerDekanat from '../components/headerDekanat.vue'
import headerAdmin from '../components/headerAdmin.vue'

export default {
    name: "alat",
    components: {
        headerUser,
        footerPage,
        headerSuperAdmin,
        headerDekanat,
        headerAdmin
    },
    data() {
        return {
            allToolsData: [],
            showImageDialog: false,
            itemToShow: null,
            pictures: [],
            dataLoaded: false,
            searchAlat: '',
            User_role: localStorage.getItem('User_role'),
            overlay: true
        }
    },
    methods: {
        async getAllDataofTools() {
            try {
                await axios.get("http://127.0.0.1:8000/api/alat/")
                    .then(response => {
                        this.allToolsData = response.data
                        console.log(this.allToolsData);
                    })
                    .catch(error => {
                        console.error("Error gagal mengambil data alat", error);
                    });
            } catch {
                console.error()
            }
        },
        morePicture(Nama, Foto) {
            this.showImageDialog = true;
            this.itemToShow = {
                Nama
            };
            if (Foto !== null) {
                this.pictures = Foto;
            }
        },
        pinjamAlat() {
            this.$router.push('/peminjamanAlat');
        },
    },
    mounted() {
        Promise.all([
            this.getAllDataofTools()
        ])
            .then(() => {
                this.overlay = false;
            })
            .catch((error) => {
                console.error(error);
            });
    },
    computed: {
        filteredTools() {
            return this.allToolsData.filter(tool => {
                return tool.Nama.toLowerCase().includes(this.searchAlat.toLowerCase());
            });
        }
    },
}
</script>

<style lang="scss" scoped></style>
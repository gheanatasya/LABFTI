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
                <v-btn style="position: absolute; bottom: 30px; right: 35px; background-color: rgb(2,39, 10, 0.9); color: white;
                border-radius: 20px; width: 150px;">Terapkan</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="roomAfterSelected">
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 700px; height: 480px;">
            <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                Terdapat .. rekomendasi ruangan yang sesuai</v-card-title>
            <v-card-text style="text-align: center;">
                <v-card v-for="(room, index) in filteredData" :key="index">

                </v-card>
            </v-card-text>
            <v-card-actions style="justify-content:center;">
                <v-btn style="position: absolute; top: 0; left: 0; margin-top: 17px;" @click="navigateBack"><v-icon
                        style="font-size: 30px;">mdi-arrow-left</v-icon></v-btn>
                <v-btn style="position: absolute; bottom: 30px; right: 35px; background-color: rgb(2,39, 10, 0.9); color: white;
                border-radius: 20px; width: 150px;">Terapkan</v-btn>
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
        }
    },
    methods: {
        navigateBack() {
            this.showRekomendasi = false;
            this.$router.push('/peminjamanRuangan');
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
            if (this.selectedFasilitas) {
                filteredData = filteredData.filter((room) => room.fasilitas.some(facilitas => facilitas === this.selectedFasilitas));
            }

            return filteredData;
        },
    },
    mounted() {
        this.getAllDataofRoom()
    },
}
</script>

<style lang="scss" scoped></style>
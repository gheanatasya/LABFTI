<template>
    <headerUser style="z-index: 1;"></headerUser>

    <div style="margin-top: 80px; height: 80%">
        <router-link to="/berandaUser"
            style="width: 200px; font-size:17px; color: rgb(2,39, 10, 0.9); margin-left: 20px; margin-top: 70px; font-family: 'Lexend-Regular'"><v-icon
                style="font-size: 25px;">mdi-keyboard-backspace</v-icon> Beranda</router-link>

        <div style="display: flex;">
            <v-container style="font-family: Lexend-Regular; width: 50%; margin-left:-250px; margin-right: 30px;">
                <div>
                    <v-form ref="peminjamanForm" method="post">

                        <div
                            style="font-size: 25px; font-family: Lexend-Medium; margin-top: 20px; margin-left: 300px; margin-right: 200px; margin-bottom: -80px;width: 60%">
                            Peminjaman Alat FTI UKDW</div>
                        <div style="display: flex; align-items: center; grid-column: span 2; width: 110%;">
                            <v-text-field type="datetime-local" label="Tanggal Pakai Awal" v-model="this.tanggalAwal"
                                variant="outlined"
                                style="width: 280px; margin-left: 300px; margin-top: 100px; margin-right: 80px;"></v-text-field>
                            <v-text-field type="datetime-local" label="Tanggal Selesai" v-model="this.tanggalSelesai"
                                variant="outlined"
                                style="width: 300px; margin-left: -75px; margin-top: 100px;"></v-text-field>
                        </div>

                        <v-text-field type="datetime-local" label="Tanggal Pengembalian"
                            v-model="this.tanggalPengembalian" variant="outlined"
                            style="margin-left: 300px; width: 280px; margin-top: 8px;"></v-text-field>

                        <v-textarea v-model="this.keterangan" style="margin-left: 303px; margin-right: -80px;"
                            label="Keterangan" row-height="25" rows="5" variant="outlined" auto-grow
                            shaped></v-textarea>

                        <v-combobox v-model="this.alat" :items="this.items" label="Alat yang ingin dipinjam" multiple
                            clearable variant="outlined" style="margin-left: 303px; margin-right: -80px;">
                            <template v-slot:selection="{ item: selectedAlat, index }">
                                <v-chip v-if="index < 2">
                                    <span>{{ selectedAlat.title }}</span>
                                </v-chip>
                                <span v-if="index === 2" class="text-grey text-caption align-self-center">
                                    (+{{ this.alat.length - 2 }} others)
                                </span>
                            </template>
                        </v-combobox>

                        <v-checkbox label="Apabila terjadi kerusakan alat atau kehilangan alat 
                        maka bersedia untuk ganti rugi sesuai dengan persyaratan yang telah ditentukan." value="true"
                            style="margin-left: 295px; margin-right: -80px;"></v-checkbox>

                        <v-btn @click="saveItem" id="simpan" type="submit"
                            style="margin-left: 430px; margin-top: -5px; border-radius: 20px; font-size: 15px; width: 250px;"
                            color="primary">
                            Pinjam Alat </v-btn>
                    </v-form>
                </div>
            </v-container>

            <v-container style="font-family: Lexend-Regular; width: 45%; margin-left: 300px; margin-right: 20px;">
                <div style="border-radius: 10px; border: 1px solid; padding: 30px; margin-bottom: 750px;">
                    <p style="font-size: 20px; font-family: Lexend-Medium; margin-bottom: 20px;">Daftar Peralatan FTI
                        UKDW</p>
                    <v-card
                        style="border-radius: 10px; background-color: rgb(3, 138, 33, 0.3); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3)">
                        <v-table style="overflow: hidden;">
                            <thead style="font-family: Lexend-Regular; font-size: 15px;">
                                <tr>
                                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1); width: 20px;">No</th>
                                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Nama Alat
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(alat, index) in items" :key="index" style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                                    <td style="width: 20px; text-align: center;"> {{ index + 1 }}
                                    </td>

                                    <td style="width: 50px; "> {{ alat }} </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card>
                </div>

            </v-container>
        </div>

    </div>

    <footerPage></footerPage>
</template>

<script>
import headerUser from '../components/headerUser.vue'
import footerPage from '../components/footerPage.vue'

export default {
    name: "peminjamanAlat",
    components: {
        headerUser,
        footerPage
    },
    data() {
        return {
            tanggalAwal: '',
            tanggalSelesai: '',
            tanggalPengembalian: '',
            keterangan: '',
            alat: [],
            items: [],
            selectedAlat: '',
        }
    },
    methods: {
        fetchAlat() {
            axios.get("http://127.0.0.1:8000/api/detail/")
                .then(response => {
                    this.items = response.data.map(detail_alat => detail_alat.Nama_alat);
                    console.log(this.items);
                })
                .catch(error => {
                    console.error("Error gagal mengambil data Alat", error);
                });
        }
    },
    mounted(){
        this.fetchAlat()
    }
}
</script>

<style lang="scss" scoped></style>
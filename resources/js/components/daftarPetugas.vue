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
                    margin-right: 50px;" @click="this.dialogTambahPetugas = true"><v-icon>mdi-plus</v-icon>Tambah
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
                <tr v-for="(petugas, index) in allPetugas" :key="index"
                    style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                    <td style="width: 20px; text-align: center;"> {{ index + 1 }} </td>

                    <td style="width: 150px;">  </td>

                    <td style="width: 100px; text-align: center;">  </td>

                    <td style="width: 100px; text-align: center;">  </td>

                    <td style="width: 100px; text-align: center;">  </td>

                    <td style="width: 100px; text-align: center;">  </td>

                    <td style="width: 100px; text-align: center;">  </td>

                    <td style="width: 100px; text-align: center;">  </td>

                    <td style="width: 100px; font-size: 25px; text-align: center;">
                        <v-icon style="color: rgb(2, 39, 10, 1);"
                            >mdi-pencil-circle</v-icon>
                        <v-icon style="color: rgb(206, 0, 0, 0.91);"
                            >mdi-delete-circle</v-icon>
                        <v-icon style="color:  rgb(0, 0, 0, 0.5);"
                            >mdi-dots-horizontal-circle</v-icon>
                    </td>
                </tr>
            </tbody>
        </v-table>
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
            dialogTambahPetugas: false,
            searchPetugas: null,

        }
    },
    methods: {
        getAllPetugas(){
            
        }
    }
}
</script>

<style lang="scss" scoped></style>
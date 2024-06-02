<template>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
        style="z-index: 1"></headerDekanat>

    <div id="filter" style="margin-top: 30px; margin-right: 60px;">
        <v-row style="font-family: 'Lexend-Regular';">
            <v-spacer></v-spacer>

            <v-col>
                <p style="margin-left: 290px; font-size: 20px; margin-top: 10px; width: 80px;">
                    <v-icon>mdi-filter-variant</v-icon>Filter
                </p>
            </v-col>

            <v-col style="margin-left: -110px; margin-right: -200px;">
                <v-text-field type="datetime-local" label="Tanggal Pakai" v-model="this.filterDateRuangan"
                    variant="outlined" style="width: 280px;" clearable></v-text-field>
            </v-col>

            <v-col style="margin-right: -200px;">
                <v-select label="Action" variant="outlined" clearable style="width: 280px;"
                    v-model="this.filterActionRuangan">
                </v-select>
            </v-col>
        </v-row>
    </div>

    <div style="font-family: 'Lexend-Medium'; font-size: 20px; margin-left: 30px;">Daftar Peminjaman Ruangan</div>

    <div>
        <v-container>
            <v-table style="overflow: hidden; width: 1800px;">
                <thead style="font-family: Lexend-Regular; font-size: 15px;">
                    <tr>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">No</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Nama Ruangan</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Peminjam</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Tgl. Pinjam</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Tgl. Pakai</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Dokumen</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Keterangan</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Status</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Action</th>

                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(ruangan, index) in allPeminjamanRuangan" :key="index"
                        style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                        <td style="width: 20px;"> {{ index + 1 }} </td>

                        <td style="width: 50px;"> {{ ruangan.namaruangan }} </td>

                        <td style="width: 50px;"> {{ ruangan.namapeminjam }} </td>

                        <td style="width: 500px;"> {{ ruangan.tanggalpinjamFormat }} </td>

                        <td style="width: 500px;"> {{ ruangan.tanggalawalFormat }} - {{ ruangan.tanggalakhirFormat }}
                        </td>

                        <td style="width: 500px;"> </td>

                        <td style="width: 500px;"> {{ ruangan.keterangan }} </td>

                        <td style="text-align: center;">

                        </td>

                        <td style="width: 700px; font-size: 25px;">
                            <v-select v-if="User_role === 'Petugas'" label="Action"
                                :items="['Diproses', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']"
                                style="border-radius: 50px;" variant="solo" v-model="ruangan.currentStatus">
                            </v-select>

                            <v-select v-else label="Action" :items="['Diproses', 'Diterima', 'Ditolak']"
                                style="border-radius: 50px;" variant="solo" v-model="ruangan.currentStatus">
                            </v-select>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </v-container>
    </div>

    <div id="filter" style="margin-top: 30px; margin-right: 60px;">
        <v-row style="font-family: 'Lexend-Regular';">
            <v-spacer></v-spacer>

            <v-col>
                <p style="margin-left: 290px; font-size: 20px; margin-top: 10px; width: 80px;">
                    <v-icon>mdi-filter-variant</v-icon>Filter
                </p>
            </v-col>

            <v-col style="margin-left: -110px; margin-right: -200px;">
                <v-text-field type="datetime-local" label="Tanggal Pakai" v-model="this.filterDateAlat"
                    variant="outlined" style="width: 280px;" clearable></v-text-field>
            </v-col>

            <v-col style="margin-right: -200px;">
                <v-select label="Action" variant="outlined" clearable style="width: 280px;"
                    v-model="this.filterActionAlat">
                </v-select>
            </v-col>
        </v-row>
    </div>

    <div>
        <div style="font-family: 'Lexend-Medium'; font-size: 20px; margin-left: 30px;">Daftar Peminjaman Alat</div>

        <v-container>
            <v-table style="overflow: hidden; width: 1800px;">
                <thead style="font-family: Lexend-Regular; font-size: 15px;">
                    <tr>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">No</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Nama Alat</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Jumlah Pinjam</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Peminjam</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Tgl. Pinjam</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Tgl. Pakai</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Dokumen</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Keterangan</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Status</th>
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Action</th>

                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(alat, index) in allPeminjamanAlat" :key="index"
                        style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                        <td style="width: 20px;"> {{ index + 1 }} </td>

                        <td style="width: 50px;"> {{ alat.namaalat }} </td>

                        <td style="width: 50px;"> {{ alat.jumlahPinjam }} </td>

                        <td style="width: 50px;"> {{ alat.namapeminjam }} </td>

                        <td style="width: 500px;"> {{ alat.tanggalpinjamFormat }} </td>

                        <td style="width: 500px;"> {{ alat.tanggalawalFormat }} - {{ alat.tanggalakhirFormat }} </td>

                        <td style="width: 500px;"> </td>

                        <td style="width: 500px;"> {{ alat.keterangan }} </td>

                        <td style="text-align: center;">

                        </td>

                        <td style="width: 700px; font-size: 25px;">
                            <v-select v-if="User_role === 'Petugas'" label="Action"
                                :items="['Diproses', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']"
                                style="border-radius: 50px;" variant="solo" v-model="alat.currentStatus">
                            </v-select>

                            <v-select v-else label="Action" :items="['Diproses', 'Diterima', 'Ditolak']"
                                style="border-radius: 50px;" variant="solo" v-model="alat.currentStatus">
                            </v-select>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </v-container>
    </div>

    <footerPage></footerPage>
</template>

<script>
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerAdmin from '../components/headerAdmin.vue'
import headerDekanat from '../components/headerDekanat.vue'
import footerPage from '../components/footerPage.vue'

export default {
    name: "daftarPeminjaman",
    components: {
        footerPage,
        headerSuperAdmin,
        headerAdmin,
        headerDekanat,
    },
    data() {
        return {
            User_role: localStorage.getItem('User_role'),
            filterDateRuangan: '',
            filterDateAlat: '',
            filterActionRuangan: '',
            filterActionAlat: '',
            currentStatus: '',
            allPeminjamanRuangan: [],
            allPeminjamanAlat: [],
        }
    },
    methods: {
        getAllPeminjamanRuangan() {
            axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuangan")
                .then(response => {
                    const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                    const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                    const modifiedPeminjamanRuangan = response.data.map(peminjaman => {
                        return {
                            ...peminjaman,
                            currentStatus: '',
                            tanggalpinjamFormat: new Date(peminjaman.tanggalpinjam).toLocaleDateString('id', options),
                            tanggalawalFormat: new Date(peminjaman.tanggalawal).toLocaleDateString('id', options2),
                            tanggalakhirFormat: new Date(peminjaman.tanggalakhir).toLocaleDateString('id', options2)
                        }
                    });

                    this.allPeminjamanRuangan = modifiedPeminjamanRuangan;
                    console.log(this.allPeminjamanRuangan);
                })
                .catch(error => {
                    console.log(error)
                })
        },
        getAllPeminjamanAlat() {
            axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlat")
                .then(response => {
                    const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                    const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                    const modifiedPeminjamanAlat = response.data.map(peminjaman => {
                        return {
                            ...peminjaman,
                            currentStatus: '',
                            tanggalpinjamFormat: new Date(peminjaman.tanggalpinjam).toLocaleDateString('id', options),
                            tanggalawalFormat: new Date(peminjaman.tanggalawal).toLocaleDateString('id', options2),
                            tanggalakhirFormat: new Date(peminjaman.tanggalakhir).toLocaleDateString('id', options2)
                        }
                    });

                    this.allPeminjamanAlat = modifiedPeminjamanAlat;
                    console.log(this.allPeminjamanAlat);
                })
                .catch(error => {
                    console.log(error)
                })
        },
    },
    mounted() {
        this.getAllPeminjamanRuangan();
        this.getAllPeminjamanAlat();
    }
}
</script>

<style lang="scss" scoped></style>
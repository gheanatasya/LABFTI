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
                        <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Add On</th>
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

                        <td style="width: 50px;">
                            <p v-for="(addon, index) in ruangan.alat" :key="index" style="font-family: Lexend-Regular;">
                                {{ index + 1 }}. {{ addon.namaalat }} : {{ addon.jumlahPinjam }}
                            </p>
                        </td>

                        <td style="width: 50px;"> {{ ruangan.namapeminjam }} </td>

                        <td style="width: 500px;"> {{ ruangan.tanggalpinjamFormat }} </td>

                        <td style="width: 500px;"> {{ ruangan.tanggalawalFormat }} - {{ ruangan.tanggalakhirFormat }}
                        </td>

                        <td style="width: 500px;"> </td>

                        <td style="width: 500px;"> {{ ruangan.keterangan }} </td>

                        <td style="text-align: center;">
                            <p v-for="(histori, index) in ruangan.histori" :key="index"
                                style="font-family: Lexend-Regular;">
                                {{ index + 1 }}. {{ histori.Namastatus }} oleh {{ histori.Acc_by }} pada tanggal {{
                                    histori.Tanggal_acc }}
                                <span>({{ histori.Catatan }})</span>
                            </p>
                        </td>

                        <td style="width: 700px; font-size: 25px;">
                            <!-- eksternal -->
                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Dekan') && (ruangan.eksternal === true) && (ruangan.dekan === true || ruangan.dekan === false)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Kepala Lab') && (ruangan.eksternal === true) && (ruangan.dekan === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Koordinator Lab') && (ruangan.eksternal === true) && (ruangan.dekan === true) && (ruangan.kepala === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Petugas') && (ruangan.eksternal === true) && (ruangan.dekan === true) && (ruangan.kepala === true) && (ruangan.koordinator === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <!-- organisasi -->
                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Wakil Dekan 3') && (ruangan.organisasi === true) && (ruangan.wd3 === true || ruangan.wd3 === false)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Kepala Lab') && (ruangan.organisasi === true) && (ruangan.wd3 === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Koordinator Lab') && (ruangan.organisasi === true) && (ruangan.wd3 === true) && (ruangan.kepala === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Petugas') && (ruangan.organisasi === true) && (ruangan.wd3 === true) && (ruangan.kepala === true) && (ruangan.koordinator === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <!-- diluar fakultas -->
                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Wakil Dekan 2') && (ruangan.email != null) && (ruangan.wd2 === true || ruangan.wd2 === false)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Kepala Lab') && (ruangan.email != null) && (ruangan.wd2 === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Koordinator Lab') && (ruangan.email != null) && (ruangan.wd2 === true) && (ruangan.kepala === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Petugas') && (ruangan.email != null) && (ruangan.wd2 === true) && (ruangan.kepala === true) && (ruangan.koordinator === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <!-- personal -->
                            <v-icon @click="persetujuanRuangan(ruangan)"
                                v-if="(this.User_role === 'Petugas') && (ruangan.personal === true)"
                                style="color: rgb(2, 39, 10, 1);">mdi-pencil-circle</v-icon>

                            <v-icon v-else style="color: rgb(30, 30, 30, 0.7);">mdi-pencil-circle</v-icon>
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
                            <p v-for="(histori, index) in alat.histori" :key="index"
                                style="font-family: Lexend-Regular;">
                                {{ index + 1 }}. {{ histori.Namastatus }} oleh {{ histori.Acc_by }} pada tanggal {{
                                    histori.Tanggal_acc }}
                                <italic>({{ histori.Catatan }})</italic>
                            </p>
                        </td>

                        <td style="width: 700px; font-size: 25px;">
                            <v-icon style="color: rgb(2, 39, 10, 1);"
                                @click="persetujuanAlat(alat)">mdi-pencil-circle</v-icon>
                        </td>
                    </tr>
                </tbody>
            </v-table>

            <!-- edit persetujuan alat -->
            <v-dialog style="justify-content:center; margin-top: -50px;" v-model="editActionAlat" persistent
                padding-left="80px" max-width="800">
                <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                    <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                        Persetujuan Alat</v-card-title>
                    <v-card-text style="text-align: center; margin-left: 40px;">
                        <v-text-field label="Nama Alat" variant="outlined" v-model="this.alat.namaalat" readonly
                            style="margin-right: 100px; margin-left:40px;"></v-text-field>

                        <v-text-field label="Jumlah Pinjam" variant="outlined" v-model="this.alat.jumlahPinjam" readonly
                            style="margin-right: 100px; margin-left:40px;"></v-text-field>

                        <v-text-field label="Tanggal Penggunaan Awal" variant="outlined"
                            v-model="this.alat.tanggalawalFormat" readonly
                            style="margin-right: 100px; margin-left:40px;"></v-text-field>

                        <v-text-field label="Tanggal Penggunaan Akhir" variant="outlined"
                            v-model="this.alat.tanggalakhirFormat" readonly
                            style="margin-right: 100px; margin-left:40px;"></v-text-field>

                        <v-textarea v-model="this.alat.keterangan" style="margin-right: 100px; margin-left:40px;"
                            readonly label="Keterangan" row-height="25" rows="5" variant="outlined" auto-grow
                            shaped></v-textarea>

                        <v-select v-if="User_role === 'Petugas'" label="Action"
                            :items="['Diproses', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']"
                            style="margin-right: 100px; margin-left:40px; border-radius: 50px;" variant="outlined"
                            v-model="this.alat.currentStatus">
                        </v-select>

                        <v-select v-else label="Action" :items="['Diproses', 'Diterima', 'Ditolak']"
                            style="margin-right: 100px; margin-left:40px; border-radius: 50px;" variant="outlined"
                            v-model="this.alat.currentStatus">
                        </v-select>

                        <v-textarea v-model="this.alat.catatan" style="margin-right: 100px; margin-left:40px;"
                            label="Catatan" row-height="25" rows="5" variant="outlined" auto-grow shaped></v-textarea>
                    </v-card-text>
                    <v-card-actions style="justify-content:center;">
                        <v-btn
                            style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                            @click="editActionAlat = false">Batal</v-btn>
                        <v-btn @click="confirmAlat(alat)"
                            style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Simpan</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- edit persetujuan ruangan -->
            <v-dialog style="justify-content:center; margin-top: -50px;" v-model="editActionRuangan" persistent
                padding-left="80px" max-width="800">
                <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                    <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                        Persetujuan Ruangan</v-card-title>
                    <v-card-text style="text-align: center; margin-left: 40px;">
                        <v-text-field label="Nama Ruangan" variant="outlined" v-model="this.ruangan.namaruangan"
                            readonly style="margin-right: 100px; margin-left:40px;"></v-text-field>

                        <v-text-field label="Tanggal Penggunaan Awal" variant="outlined"
                            v-model="this.ruangan.tanggalawalFormat" readonly
                            style="margin-right: 100px; margin-left:40px;"></v-text-field>

                        <v-text-field label="Tanggal Penggunaan Akhir" variant="outlined"
                            v-model="this.ruangan.tanggalakhirFormat" readonly
                            style="margin-right: 100px; margin-left:40px;"></v-text-field>

                        <v-textarea v-model="this.ruangan.keterangan" style="margin-right: 100px; margin-left:40px;"
                            readonly label="Keterangan" row-height="25" rows="5" variant="outlined" auto-grow
                            shaped></v-textarea>

                        <v-select v-if="User_role === 'Petugas'" label="Action"
                            :items="['Diproses', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']"
                            style="margin-right: 100px; margin-left:40px; border-radius: 50px;" variant="outlined"
                            v-model="this.ruangan.currentStatus">
                        </v-select>

                        <v-select v-else label="Action" :items="['Diproses', 'Diterima', 'Ditolak']"
                            style="margin-right: 100px; margin-left:40px; border-radius: 50px;" variant="outlined"
                            v-model="this.ruangan.currentStatus">
                        </v-select>

                        <v-textarea v-model="this.ruangan.catatan" style="margin-right: 100px; margin-left:40px;"
                            label="Catatan" row-height="25" rows="5" variant="outlined" auto-grow shaped></v-textarea>
                    </v-card-text>
                    <v-card-actions style="justify-content:center;">
                        <v-btn
                            style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;"
                            @click="editActionRuangan = false">Batal</v-btn>
                        <v-btn @click="confirmRuangan(ruangan)"
                            style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;">Simpan</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
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
            editActionRuangan: false,
            editActionAlat: false,
            alat: {},
            ruangan: {}
        }
    },
    methods: {
        getAllPeminjamanRuangan() {
            if (this.User_role === 'Koordinator Lab' || this.User_role === 'Kepala Lab' || this.User_role === 'Petugas') {
                axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuangan")
                    .then(response => {
                        const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                        const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                        const modifiedPeminjamanRuangan = response.data.map(peminjaman => {
                            return {
                                ...peminjaman,
                                currentStatus: '',
                                catatan: '',
                                tanggalpinjamFormat: new Date(peminjaman.tanggalpinjam).toLocaleDateString('id', options),
                                tanggalawalFormat: new Date(peminjaman.tanggalawal).toLocaleDateString('id', options2),
                                tanggalakhirFormat: new Date(peminjaman.tanggalakhir).toLocaleDateString('id', options2)
                            }
                        });

                        this.allPeminjamanRuangan = modifiedPeminjamanRuangan;
                        console.log('before', this.allPeminjamanRuangan);
                    })
                    .catch(error => {
                        console.log(error)
                    })
            } else if (this.User_role === 'Dekan') {
                axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuanganDekan")
                    .then(response => {
                        const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                        const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                        const modifiedPeminjamanRuangan = response.data.map(peminjaman => {
                            return {
                                ...peminjaman,
                                currentStatus: '',
                                catatan: '',
                                tanggalpinjamFormat: new Date(peminjaman.tanggalpinjam).toLocaleDateString('id', options),
                                tanggalawalFormat: new Date(peminjaman.tanggalawal).toLocaleDateString('id', options2),
                                tanggalakhirFormat: new Date(peminjaman.tanggalakhir).toLocaleDateString('id', options2)
                            }
                        });

                        this.allPeminjamanRuangan = modifiedPeminjamanRuangan;
                        console.log('before', this.allPeminjamanRuangan);
                    })
                    .catch(error => {
                        console.log(error)
                    })
            } else if (this.User_role === 'WD2') {
                axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuanganWD2")
                    .then(response => {
                        const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                        const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                        const modifiedPeminjamanRuangan = response.data.map(peminjaman => {
                            return {
                                ...peminjaman,
                                currentStatus: '',
                                catatan: '',
                                tanggalpinjamFormat: new Date(peminjaman.tanggalpinjam).toLocaleDateString('id', options),
                                tanggalawalFormat: new Date(peminjaman.tanggalawal).toLocaleDateString('id', options2),
                                tanggalakhirFormat: new Date(peminjaman.tanggalakhir).toLocaleDateString('id', options2)
                            }
                        });

                        this.allPeminjamanRuangan = modifiedPeminjamanRuangan;
                        console.log('before', this.allPeminjamanRuangan);
                    })
                    .catch(error => {
                        console.log(error)
                    })
            } else if (this.User_role === 'WD3') {
                axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuanganWD3")
                    .then(response => {
                        const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                        const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                        const modifiedPeminjamanRuangan = response.data.map(peminjaman => {
                            return {
                                ...peminjaman,
                                currentStatus: '',
                                catatan: '',
                                tanggalpinjamFormat: new Date(peminjaman.tanggalpinjam).toLocaleDateString('id', options),
                                tanggalawalFormat: new Date(peminjaman.tanggalawal).toLocaleDateString('id', options2),
                                tanggalakhirFormat: new Date(peminjaman.tanggalakhir).toLocaleDateString('id', options2)
                            }
                        });

                        this.allPeminjamanRuangan = modifiedPeminjamanRuangan;
                        console.log('before', this.allPeminjamanRuangan);
                    })
                    .catch(error => {
                        console.log(error)
                    })
            }

            console.log(this.User_role)
        },
        getAllPeminjamanAlat() {
            if (this.User_role === 'Koordinator Lab' || this.User_role === 'Kepala Lab' || this.User_role === 'Petugas') {
                axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlat")
                    .then(response => {
                        const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                        const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                        const modifiedPeminjamanAlat = response.data.map(peminjaman => {
                            return {
                                ...peminjaman,
                                currentStatus: '',
                                catatan: '',
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
            } else if (this.User_role === 'WD2') {
                axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlatWD2")
                    .then(response => {
                        const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                        const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                        const modifiedPeminjamanAlat = response.data.map(peminjaman => {
                            return {
                                ...peminjaman,
                                currentStatus: '',
                                catatan: '',
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
            } else if (this.User_role === 'WD3') {
                axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlatWD3")
                    .then(response => {
                        const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                        const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                        const modifiedPeminjamanAlat = response.data.map(peminjaman => {
                            return {
                                ...peminjaman,
                                currentStatus: '',
                                catatan: '',
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
            } else if (this.User_role === 'Dekan') {
                axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlatDekan")
                    .then(response => {
                        const options = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' };
                        const options2 = { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
                        const modifiedPeminjamanAlat = response.data.map(peminjaman => {
                            return {
                                ...peminjaman,
                                currentStatus: '',
                                catatan: '',
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
            }
        },
        persetujuanAlat(alat) {
            this.editActionAlat = !this.editActionAlat;
            this.alat = alat;
            console.log(this.alat);
        },
        persetujuanRuangan(ruangan) {
            this.editActionRuangan = !this.editActionRuangan;
            this.ruangan = ruangan;
            console.log(this.ruangan);
        },
        confirmRuangan(ruangan) {
            const peminjamanid = ruangan.peminjamanruanganid;
            const userrole = localStorage.getItem('User_role');
            const namastatus = ruangan.currentStatus;
            const catatan = ruangan.catatan;

            console.log(peminjamanid, userrole, namastatus, catatan);

            axios.put(`http://127.0.0.1:8000/api/persetujuan/confirmBookingRuangan/${peminjamanid}/${userrole}/${namastatus}/${catatan}`, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).
                then(response => {
                    if (response.status === 200) {
                        console.log("Konfirmasi success:", response.data);
                        this.editActionRuangan = false;
                    } else {
                        console.error("Error konfirmasi failed:", response.data.message);
                    }
                })
                .catch(error => {
                    console.error("Error konfirmasi failed:", error);
                });
        },
        confirmAlat(alat) {
            const peminjamanid = alat.peminjamanalatid;
            const userrole = localStorage.getItem('User_role');
            const namastatus = alat.currentStatus;
            const catatan = alat.catatan;

            console.log(peminjamanid, userrole, namastatus, catatan);
            axios.put(`http://127.0.0.1:8000/api/persetujuan/confirmBookingAlat/${peminjamanid}/${userrole}/${namastatus}/${catatan}`, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).
                then(response => {
                    if (response.status === 200) {
                        console.log("Konfirmasi success:", response.data);
                        this.editActionAlat = false;
                    } else {
                        console.error("Error konfirmasi failed:", response.data.message);
                    }
                })
                .catch(error => {
                    console.error("Error konfirmasi failed:", error);
                });
        }
    },
    mounted() {
        this.getAllPeminjamanRuangan();
        this.getAllPeminjamanAlat();
    }
}
</script>

<style lang="scss" scoped></style>
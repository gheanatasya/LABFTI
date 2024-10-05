<template>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'"
        style="z-index: 1; position: fixed; width: 100%;">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1; position: fixed; width: 100%;"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
        style="z-index: 1; position: fixed; width: 100%;"></headerDekanat>

    <v-overlay v-model="overlay" style="background-color: white; z-index: 0">
        <v-container style="height: 660px; margin-left: 440px;">
            <v-row align-content="center" class="fill-height" justify="center">
                <v-col class="text-subtitle-1 text-center" cols="12" style="font-family: Lexend-Regular;">
                    Memuat halaman
                </v-col>
                <v-col cols="6">
                    <v-progress-linear color="#0D47A1" height="6" indeterminate rounded></v-progress-linear>
                </v-col>
            </v-row>
        </v-container>
    </v-overlay>

    <div id="filter" style="padding-top: 100px; margin-right: 60px;">
        <v-row style="font-family: 'Lexend-Regular';">
            <v-spacer></v-spacer>

            <v-col>
                <p style="margin-left: 290px; font-size: 20px; margin-top: 10px; width: 80px;">
                    <v-icon>mdi-filter-variant</v-icon>Filter
                </p>
            </v-col>

            <v-col style="margin-left: -110px; margin-right: -200px;">
                <v-select label="Nama Ruangan" variant="outlined" clearable style="width: 280px;" :items="this.allRoom"
                    v-model="this.filterNamaRuangan">
                </v-select>
            </v-col>

            <v-col style="margin-right: -200px;">
                <v-select v-if="User_role === 'Petugas'" label="Status Konfirmasi" variant="outlined" clearable
                    style="width: 280px;" :items="['Diproses', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']"
                    v-model="this.filterActionRuangan">
                </v-select>

                <v-select v-else label="Status Konfirmasi" :items="['Diproses', 'Diterima', 'Ditolak']" style="width: 280px;"
                    clearable variant="outlined" v-model="this.filterActionRuangan">
                </v-select>
            </v-col>
        </v-row>
    </div>

    <div style="font-family: 'Lexend-Medium'; font-size: 20px; margin-left: 30px;">Daftar Peminjaman Ruangan</div>

    <div style="width: 100%;">
        <v-container>
            <v-card style="margin-right: -120px; margin-left: -120px;">
                <v-table style="height: 500px; width: 2500px;" fixed-header>
                    <thead style="font-family: Lexend-Regular; font-size: 15px;">
                        <tr>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">No</th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Nama
                                Ruangan</th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Tambahan
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Peminjam
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Tgl.
                                Pinjam</th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Tgl. Pakai
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Dokumen
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Keterangan
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Status
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Aksi
                            </th>

                        </tr>
                    </thead>
                    <tbody v-if="filteredRooms.length > 0">
                        <tr v-for="(ruangan, index) in paginatedRooms" :key="index"
                            style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                            <td> {{ (currentPageRuangan - 1) * itemsPerPage + index + 1 }} </td>

                            <td style="width: 150px;"> {{ ruangan.namaruangan }} </td>

                            <td>
                                <p v-for="(addon, index) in ruangan.alat" :key="index"
                                    style="font-family: Lexend-Regular;">
                                    {{ addon.namaalat }}({{ addon.jumlahPinjam }})
                                </p>
                            </td>

                            <td style="width: 150px;"> {{ ruangan.namapeminjam }}({{ ruangan.nohp }}) </td>

                            <td style="width: 400px;"> {{ ruangan.tanggalpinjamFormat }} </td>

                            <td style="width: 500px;"> {{ ruangan.tanggalawalFormat }} - {{ ruangan.tanggalakhirFormat
                                }}
                            </td>

                            <td style="width: 400px;">
                                <a v-if="ruangan.path !== null" :href="'../storage/' + ruangan.path" target="_blank">{{
                                    ruangan.namadokumen }}</a>
                            </td>

                            <td style="width: 500px;"> {{ ruangan.keterangan }} </td>

                            <td style="text-align: left; width: 800px;">
                                <p v-for="(histori, index) in ruangan.histori" :key="index"
                                    style="font-family: Lexend-Regular;">
                                    {{ index + 1 }}. {{ histori.Namastatus }} oleh {{ histori.Acc_by }} pada tanggal {{
                                        new Date(histori.Tanggal_acc).toLocaleTimeString('id-ID',
                                        {
                                            year:
                                                'numeric', month:
                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                        }) }}
                                    <span v-if="histori.Catatan !== 'null'">({{ histori.Catatan }})</span>
                                </p>
                            </td>

                            <td style="text-align: center">
                                <!-- eksternal -->
                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Dekan') && (ruangan.eksternal === true) && (ruangan.dekan === true || ruangan.dekan === false || ruangan.dekan === null)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Kepala Lab') && (ruangan.eksternal === true) && (ruangan.dekan === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Koordinator Lab') && (ruangan.eksternal === true) && (ruangan.dekan === true) && (ruangan.kepala === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Petugas') && (ruangan.eksternal === true) && (ruangan.dekan === true) && (ruangan.kepala === true) && (ruangan.koordinator === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <!-- organisasi -->
                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Wakil Dekan 3') && (ruangan.organisasi === true) && (ruangan.wd3 === true || ruangan.wd3 === false || ruangan.wd3 === null)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Kepala Lab') && (ruangan.organisasi === true) && (ruangan.wd3 === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Koordinator Lab') && (ruangan.organisasi === true) && (ruangan.wd3 === true) && (ruangan.kepala === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Petugas') && (ruangan.organisasi === true) && (ruangan.wd3 === true) && (ruangan.kepala === true) && (ruangan.koordinator === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <!-- diluar fakultas -->
                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Wakil Dekan 2') && (ruangan.email != null) && (ruangan.wd2 === true || ruangan.wd2 === false || ruangan.wd2 === null)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Kepala Lab') && (ruangan.email != null) && (ruangan.wd2 === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Koordinator Lab') && (ruangan.email != null) && (ruangan.wd2 === true) && (ruangan.kepala === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Petugas') && (ruangan.email != null) && (ruangan.wd2 === true) && (ruangan.kepala === true) && (ruangan.koordinator === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <!-- personal -->
                                <v-chip @click="persetujuanRuangan(ruangan)"
                                    v-if="(this.User_role === 'Petugas') && (ruangan.personal === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <!-- <v-icon v-else style="color: rgb(30, 30, 30, 0.7);">mdi-pencil-circle</v-icon> -->
                            </td>
                        </tr>
                    </tbody>

                    <tbody v-else>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <div class="py-1 text-center" style="content: center; margin-top: 80px; margin-left: 50px;">
                            <v-icon class="mb-6" color="primary" icon="mdi-alert-circle-outline" size="40"></v-icon>
                            <div class="text-h7 font-weight-bold">Maaf, tidak ada data yang bisa ditampilkan.</div>
                        </div>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>
                </v-table>
                <v-pagination v-model="currentPageRuangan" :length="Math.ceil(filteredRooms.length / itemsPerPage)"
                    @change="updateCurrentPageRuangan"></v-pagination>
            </v-card>
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
                <v-select label="Nama Alat" variant="outlined" clearable style="width: 280px;" :items="this.allTool"
                    v-model="this.filterNamaAlat">
                </v-select>
            </v-col>

            <v-col style="margin-right: -200px;">
                <v-select v-if="User_role === 'Petugas'" label="Status Konfirmasi" variant="outlined" clearable
                    style="width: 280px;" :items="['Diproses', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']"
                    v-model="this.filterActionAlat">
                </v-select>

                <v-select v-else label="Status Konfirmasi" :items="['Diproses', 'Diterima', 'Ditolak']" style="width: 280px;"
                    clearable variant="outlined" v-model="this.filterActionAlat">
                </v-select>
            </v-col>
        </v-row>
    </div>

    <div>
        <div style="font-family: 'Lexend-Medium'; font-size: 20px; margin-left: 30px;">Daftar Peminjaman Alat</div>

        <v-container>
            <v-card style="margin-right: -120px; margin-left: -120px;">
                <v-table style="height: 500px; width: 1800px;" fixed-header>
                    <thead style="font-family: Lexend-Regular; font-size: 15px;">
                        <tr>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">No</th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Nama Alat
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Jumlah
                                Pinjam
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Peminjam
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Tgl.
                                Pinjam
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Tgl. Pakai
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Dokumen
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Keterangan
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Status
                            </th>
                            <th class="text-center" style="background-color: #BBDEFB; color: black;">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="this.filteredTools.length > 0">
                        <tr v-for="(alat, index) in paginatedTools" :key="index"
                            style=" font-family: 'Lexend-Regular; font-size: 15px;">
                            <td> {{ (currentPageAlat - 1) * itemsPerPage + index + 1 }} </td>

                            <td style="width: 50px;"> {{ alat.namaalat }} </td>

                            <td style="width: 50px; text-align: center;"> {{ alat.jumlahPinjam }} </td>

                            <td style="width: 50px;"> {{ alat.namapeminjam }} ({{ alat.nohp }})</td>

                            <td style="width: 500px;"> {{ alat.tanggalpinjamFormat }} </td>

                            <td style="width: 500px;"> {{ alat.tanggalawalFormat }} - {{ alat.tanggalakhirFormat }}
                            </td>

                            <td style="width: 500px;">
                                <a v-if="alat.path !== null" :href="'../storage/' + alat.path" target="_blank">{{
                                    alat.namadokumen }}</a>
                            </td>

                            <td style="width: 500px;"> {{ alat.keterangan }} </td>

                            <td style="text-align: left; width: 800px;">
                                <p v-for="(histori, index) in alat.histori" :key="index"
                                    style="font-family: Lexend-Regular;">
                                    {{ index + 1 }}. {{ histori.Namastatus }} oleh {{ histori.Acc_by }} pada tanggal {{
                                        new Date(histori.Tanggal_acc).toLocaleTimeString('id-ID',
                                        {
                                            year:
                                                'numeric', month:
                                                'long', day: 'numeric', hour: 'numeric', minute: 'numeric'
                                        }) }}
                                    <span v-if="histori.Catatan !== 'null'">({{ histori.Catatan }})</span>
                                </p>
                            </td>

                            <td style="text-align: center;">
                                <!-- eksternal -->
                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Dekanat') && (alat.eksternal === true) && (alat.dekan === true || alat.dekan === false || alat.dekan === null)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Kepala Lab') && (alat.eksternal === true) && (alat.dekan === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Koordinator Lab') && (alat.eksternal === true) && (alat.dekan === true) && (alat.kepala === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Petugas') && (alat.eksternal === true) && (alat.dekan === true) && (alat.kepala === true) && (ruangan.koordinator === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <!-- organisasi -->
                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Wakil Dekan 3') && (alat.organisasi === true) && (alat.wd3 === true || alat.wd3 === false || alat.wd3 === null)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Kepala Lab') && (alat.organisasi === true) && (alat.wd3 === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Koordinator Lab') && (alat.organisasi === true) && (alat.wd3 === true) && (alat.kepala === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Petugas') && (alat.organisasi === true) && (alat.wd3 === true) && (alat.kepala === true) && (ruangan.koordinator === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <!-- diluar fakultas -->
                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Wakil Dekan 2') && (alat.email != null) && (alat.wd2 === true || alat.wd2 === false || alat.wd2 === null)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Kepala Lab') && (alat.email != null) && (alat.wd2 === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Koordinator Lab') && (alat.email != null) && (alat.wd2 === true) && (alat.kepala === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Petugas') && (alat.email != null) && (alat.wd2 === true) && (alat.kepala === true) && (ruangan.koordinator === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <!-- personal -->
                                <v-chip @click="persetujuanAlat(alat)"
                                    v-if="(this.User_role === 'Petugas') && (alat.personal === true)"
                                    style="color: #0D47A1;">Berikan Konfirmasi</v-chip>

                                <!-- <v-icon v-else style="color: rgb(30, 30, 30, 0.7);">mdi-pencil-circle</v-icon> -->
                            </td>
                        </tr>
                    </tbody>

                    <tbody v-else>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <div class="py-1 text-center" style="content: center; margin-top: 80px; margin-left: 50px;">
                            <v-icon class="mb-6" color="primary" icon="mdi-alert-circle-outline" size="40"></v-icon>
                            <div class="text-h7 font-weight-bold">Maaf, tidak ada data yang bisa ditampilkan.</div>
                        </div>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>
                </v-table>
                <v-pagination v-model="currentPageAlat" :length="Math.ceil(filteredTools.length / itemsPerPage)"
                    @change="updateCurrentPageAlat"></v-pagination>
            </v-card>

            <!-- edit persetujuan alat -->
            <v-dialog style="justify-content:center; margin-top: 0px;" v-model="editActionAlat" persistent
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

                        <v-select v-if="User_role === 'Petugas'" label="Status Konfirmasi"
                            :items="['Diproses', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']"
                            style="margin-right: 100px; margin-left:40px; border-radius: 50px;" variant="outlined"
                            v-model="this.alat.currentStatus">
                        </v-select>

                        <v-select v-else label="Status Konfirmasi" :items="['Diproses', 'Diterima', 'Ditolak']"
                            style="margin-right: 100px; margin-left:40px; border-radius: 50px;" variant="outlined"
                            v-model="this.alat.currentStatus">
                        </v-select>

                        <v-textarea v-model="this.alat.catatan" style="margin-right: 100px; margin-left:40px;"
                            label="Catatan" row-height="25" rows="5" variant="outlined" auto-grow shaped></v-textarea>
                    </v-card-text>
                    <v-card-actions style="justify-content:center;">
                        <v-btn 
                        style="text-transform: none; border: 3px solid #0D47A1;  box-shadow: none; background-color: none; width: 100px; color: #0D47A1; border-radius: 20px;" @click="editActionAlat = false, this.loadingAlat = false">Batal</v-btn>
                        <v-btn @click="konfirmasiAlatLebih(alat)" :loading="this.loadingAlat"
                        style="text-transform: none; background-color: #0D47A1; color: white; border-radius: 20px; width: 100px;">Simpan</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- edit persetujuan ruangan -->
            <v-dialog style="justify-content:center; margin-top: 0px;" v-model="editActionRuangan" persistent
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

                        <v-select v-if="User_role === 'Petugas'" label="Status Konfirmasi"
                            :items="['Diproses', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']"
                            style="margin-right: 100px; margin-left:40px; border-radius: 50px;" variant="outlined"
                            v-model="this.ruangan.currentStatus">
                        </v-select>

                        <v-select v-else label="Status Konfirmasi" :items="['Diproses', 'Diterima', 'Ditolak']"
                            style="margin-right: 100px; margin-left:40px; border-radius: 50px;" variant="outlined"
                            v-model="this.ruangan.currentStatus">
                        </v-select>

                        <v-textarea v-model="this.ruangan.catatan" style="margin-right: 100px; margin-left:40px;"
                            label="Catatan" row-height="25" rows="5" variant="outlined" auto-grow shaped></v-textarea>
                    </v-card-text>
                    <v-card-actions style="justify-content:center;">
                        <v-btn 
                            @click="editActionRuangan = false, this.loadingRuangan = false" style="text-transform: none; border: 3px solid #0D47A1;  box-shadow: none; background-color: none; width: 100px; color: #0D47A1; border-radius: 20px;">Batal</v-btn>
                        <v-btn @click="confirmRuangan(ruangan)" :loading="this.loadingRuangan"
                        style="text-transform: none; background-color: #0D47A1; color: white; border-radius: 20px; width: 100px;">Simpan</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- konfirmasi addon alat -->
            <v-dialog style="justify-content:center; margin-top: 0px;" v-model="konfirmasiTools" persistent
                padding-left="80px" max-width="800">
                <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 800px;">
                    <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                        Peminjaman Lebih Dari Satu Alat</v-card-title>
                    <v-card-text style="text-align: center; margin-left: 40px;">
                        <p>Alat yang dipinjam lebih dari satu. Berikut adalah alat yang dipinjam :</p>
                        <p v-for="(alat, index) in this.sameTools" :key="index">
                            {{ index + 1 }}. {{ alat.namaalat }} ({{ alat.jumlahPinjam }})
                        </p>

                        <p>Apakah ingin mengonfirmasi semua peminjaman alat yang dilakukan?</p>
                    </v-card-text>
                    <v-card-actions style="justify-content:center;">
                        <v-btn
                            style="border: 3px solid rgb(2, 39, 10, 0.9);  box-shadow: none; background-color: none; width: 100px; color: rgb(2, 39, 10, 0.9); border-radius: 20px;"
                            @click="konfirmasiTools = false, confirmAlat(this.alat), this.loadingAlat2 = false">Tidak</v-btn>
                        <v-btn @click="confirmAlat2(this.alat)" :loading="this.loadingAlat2"
                            style="background-color: rgb(2, 39, 10, 0.9); color: white; border-radius: 20px; width: 100px;">Ya</v-btn>
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
            filterActionRuangan: null,
            filterActionAlat: null,
            filterNamaRuangan: null,
            filterNamaAlat: null,
            currentStatus: '',
            allPeminjamanRuangan: [],
            allPeminjamanAlat: [],
            editActionRuangan: false,
            editActionAlat: false,
            konfirmasiTools: false,
            alat: {},
            ruangan: {},
            allRoom: [],
            allTool: [],
            currentPageRuangan: 1,
            currentPageAlat: 1,
            itemsPerPage: 5,
            sameTools: {},
            overlay: true,
            loadingAlat: false,
            loadingRuangan: false,
            loadingAlat2: false,

        }
    },
    methods: {
        async getAllPeminjamanRuangan() {
            if (this.User_role === 'Koordinator Lab' || this.User_role === 'Kepala Lab' || this.User_role === 'Petugas') {
                await axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuangan")
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
            } else if (this.User_role === 'Dekanat') {
                await axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuanganDekan")
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
            } else if (this.User_role === 'Wakil Dekan 2') {
                await axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuanganWD2")
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
            } else if (this.User_role === 'Wakil Dekan 3') {
                await axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccRuanganWD3")
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
        async getAllPeminjamanAlat() {
            if (this.User_role === 'Koordinator Lab' || this.User_role === 'Kepala Lab' || this.User_role === 'Petugas') {
                await axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlat")
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
            } else if (this.User_role === 'Wakil Dekan 2') {
                await axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlatWD2")
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
            } else if (this.User_role === 'Wakil Dekan 3') {
                await axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlatWD3")
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
            } else if (this.User_role === 'Dekanat') {
                await axios.get("http://127.0.0.1:8000/api/getAllPeminjamanforAccAlatDekan")
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
            this.alat = {
                ...alat,
                loading: false
            };
            console.log(this.alat);
        },
        persetujuanRuangan(ruangan) {
            this.editActionRuangan = !this.editActionRuangan;
            this.ruangan = {
                ...ruangan,
                loading: false
            };
            console.log(this.ruangan);
        },
        konfirmasiAlatLebih(alat) {
            this.loadingAlat = true;
            const peminjamanid = alat.peminjamanid;
            axios.get(`http://127.0.0.1:8000/api/peminjamanAlat/checkAlat/${peminjamanid}`)
                .then(response => {
                    if (response.data.length === 0) {
                        this.confirmAlat(alat);
                    } else {
                        this.konfirmasiTools = true;
                        this.sameTools = response.data;
                        this.alat = alat;
                    }
                })
                .catch(error => {
                    console.log(error)
                    this.loadingAlat = true;
                })
            this.loadingAlat = true;
        },
        confirmRuangan(ruangan) {
            this.loadingRuangan = true;
            if (ruangan.currentStatus === '' || ruangan.currentStatus === null) {
                alert('Tidak ada status yang dipilih!');
                this.loadingRuangan = false;
                return
            }

            const peminjamanid = ruangan.peminjamanruanganid;
            const userrole = localStorage.getItem('User_role');
            const namastatus = ruangan.currentStatus;
            const catatan = ruangan.catatan ? ruangan.catatan : null;

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
                        this.loadingRuangan = false;
                        this.editActionRuangan = false;
                    } else {
                        console.error("Error konfirmasi failed:", response.data.message);
                        this.loadingRuangan = false;
                    }
                })
                .catch(error => {
                    console.error("Error konfirmasi failed:", error);
                    this.loadingRuangan = false;
                });
        },
        confirmAlat(alat) {
            this.loadingAlat = true;
            if (alat.currentStatus === '' || alat.currentStatus === null) {
                alert('Tidak ada status yang dipilih!');
                this.loadingAlat = false
                return
            }

            const peminjamanid = alat.peminjamanalatid;
            const userrole = localStorage.getItem('User_role');
            const namastatus = alat.currentStatus;
            const catatan = alat.catatan ? alat.catatan : null;

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
                        this.loadingAlat = false;
                        this.editActionAlat = false;
                    } else {
                        console.error("Error konfirmasi failed:", response.data.message);
                        this.loadingAlat = false;
                    }
                })
                .catch(error => {
                    console.error("Error konfirmasi failed:", error);
                    this.loadingAlat = false;
                });
        },
        confirmAlat2(alat) {
            this.loadingAlat2 = true;
            if (alat.currentStatus === '' || alat.currentStatus === null) {
                alert('Tidak ada status yang dipilih!');
                this.loadingAlat2 = false
                this.konfirmasiTools = false
                this.loadingAlat = false
                return
            }

            const pengecekan = this.allPeminjamanAlat.filter(data => data.peminjamanid === alat.peminjamanid);

            //for (let i = 0; i < pengecekan.length; i++) {
            const peminjamanid = alat.peminjamanalatid;
            const userrole = localStorage.getItem('User_role');
            const namastatus = alat.currentStatus;
            const catatan = alat.catatan ? alat.catatan : null;

            console.log(peminjamanid, userrole, namastatus, catatan);
            axios.put(`http://127.0.0.1:8000/api/persetujuan/confirmBookingAlat2/${peminjamanid}/${userrole}/${namastatus}/${catatan}`, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).
                then(response => {
                    if (response.status === 200) {
                        console.log("Konfirmasi success:", response.data);
                        this.loadingAlat2 = false;
                    } else {
                        console.error("Error konfirmasi failed:", response.data.message);
                        this.loadingAlat2 = false;
                    }
                })
                .catch(error => {
                    console.error("Error konfirmasi failed:", error);
                    this.loadingAlat2 = false;
                });

            //console.log('berhasil memperbarui');
            //}
            console.log('berhasil memperbarui semuanya');
            //this.loadingAlat2 = false;
            this.konfirmasiTools = false;
            this.editActionAlat = false;
            this.loadingAlat = false;
        },
        async getAllDataofRoom() {
            try {
                await axios.get("http://127.0.0.1:8000/api/ruangan/")
                    .then(response => {
                        this.allRoom = response.data.map(room =>
                            room.Nama_ruangan
                        );
                        console.log(this.allRoom);
                    })
                    .catch(error => {
                        console.error("Error gagal mengambil data semua ruangan", error);
                    });
            } catch {
                console.error()
            }
        },
        async getAllDataofTools() {
            try {
                await axios.get("http://127.0.0.1:8000/api/alat/")
                    .then(response => {
                        this.allTool = response.data.map(tool => tool.Nama);
                        console.log(this.allTool);
                    })
                    .catch(error => {
                        console.error("Error gagal mengambil data alat", error);
                    });
            } catch {
                console.error()
            }
        },
        updateCurrentPageAlat(val) {
            this.currentPageAlat = val;
        },
        updateCurrentPageRuangan(val) {
            this.currentPageDetailAlat = val;
        },
    },
    mounted() {
        Promise.all([
            this.getAllPeminjamanRuangan(),
            this.getAllPeminjamanAlat(),
            this.getAllDataofRoom(),
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
        filteredRooms() {
            let filteredRooms = this.allPeminjamanRuangan.slice();

            /* filter nama ruangan */
            if (this.filterNamaRuangan) {
                filteredRooms = filteredRooms.filter((room) => room.namaruangan === this.filterNamaRuangan);
            }

            /* filter action */
            if (this.filterActionRuangan) {
                filteredRooms = filteredRooms.filter((room) => room.histori.some(historiItem => historiItem.Namastatus === this.filterActionRuangan));
            }

            return filteredRooms;
        },
        filteredTools() {
            let filteredTools = this.allPeminjamanAlat;

            /* filter nama ruangan */
            if (this.filterNamaAlat) {
                filteredTools = filteredTools.filter((tool) => tool.namaalat === this.filterNamaAlat);
            }

            /* filter action */
            if (this.filterActionAlat) {
                filteredTools = filteredTools.filter((tool) => tool.histori.some(historiItem => historiItem.Namastatus === this.filterActionAlat));
            }

            return filteredTools;
        },
        paginatedRooms() {
            const startIndex = (this.currentPageRuangan - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.filteredRooms.slice(startIndex,
                endIndex);
        },
        paginatedTools() {
            const startIndex = (this.currentPageAlat - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.filteredTools.slice(startIndex,
                endIndex);
        },
    },
}
</script>

<style lang="scss" scoped></style>
<template>
    <headerUser v-if="User_role === 'Mahasiswa' || User_role === 'Dosen' || User_role === 'Staff'" style="z-index: 1"></headerUser>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1"></headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan'|| User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'" style="z-index: 1"></headerDekanat>

    <div style="margin-top: 80px; height: 80%">
        <router-link to="/berandaUser"
            style="width: 200px; font-size:17px; color: rgb(2,39, 10, 0.9); margin-left: 20px; margin-top: 70px; font-family: 'Lexend-Regular'"><v-icon
                style="font-size: 25px;">mdi-keyboard-backspace</v-icon> Beranda</router-link>

        <div style="display: flex; height: 100%;">
            <v-container style="font-family: Lexend-Regular; width: 50%; margin-left:-250px; margin-right: 30px;">
                <div>
                    <v-form @submit.prevent="saveItem" ref="peminjamanForm" method="post">
                        <div v-for="item, index in form" :key="item">
                            <div
                                style="font-size: 25px; font-family: Lexend-Medium; margin-top: 20px; margin-left: 300px; margin-right: 200px; margin-bottom: -80px;width: 60%">
                                Peminjaman Alat FTI UKDW {{ index + 1 }}</div>
                            <div style="display: flex; align-items: center; grid-column: span 4; width: 117.7%;">
                                <v-text-field type="datetime-local" label="Tanggal Pakai Awal"
                                    v-model="item.tanggalAwal" variant="outlined"
                                    style="width: 280px; margin-left: 300px; margin-top: 100px; margin-right: 80px;"></v-text-field>
                                <v-text-field type="datetime-local" label="Tanggal Selesai"
                                    v-model="item.tanggalSelesai" variant="outlined"
                                    style="width: 300px; margin-left: -75px; margin-top: 100px;"></v-text-field>

                                <v-tooltip text="Perhatikan WAKTU PADA TANGGAL PAKAI AWAL adalah termasuk WAKTU PENGAMBILAN ALAT
                            dan WAKTU PADA TANGGAL SELESAI adalah WAKTU PENGEMBALIAN ALAT." location="end" :width="300"
                                    style="font-family: 'Lexend-Regular';">
                                    <template v-slot:activator="{ props }">
                                        <v-btn v-bind="props" icon="mdi-information"
                                            style="box-shadow: none; margin-top: 75px;"></v-btn>
                                    </template>
                                </v-tooltip>
                            </div>

                            <v-textarea v-model="item.keterangan" style="margin-left: 303px; margin-right: -80px;"
                                label="Keterangan" row-height="25" rows="5" variant="outlined" auto-grow
                                shaped></v-textarea>

                            <div style="margin-left: 303px; margin-right: -80px;">
                                <label for="isPersonal">Apakah peminjaman dilakukan untuk keperluan pribadi (belajar,
                                    skripsian, kerja
                                    kelompok)?</label>
                                <v-radio-group v-model="item.selectedOptionPersonal" id="isPersonal">
                                    <v-row>
                                        <v-col cols="auto">
                                            <v-radio label="Ya" value="True"></v-radio>
                                        </v-col>
                                        <v-col cols="auto">
                                            <v-radio label="Tidak" value="False"></v-radio>
                                        </v-col>
                                    </v-row>
                                </v-radio-group>

                                <label for="isOrganisation">Apakah peminjaman dilakukan untuk keperluan
                                    organisasi?</label>
                                <v-radio-group v-model="item.selectedOptionOrganisation" id="isOrganisation">
                                    <v-row>
                                        <v-col cols="auto">
                                            <v-radio label="Ya" value="True"></v-radio>
                                        </v-col>
                                        <v-col cols="auto">
                                            <v-radio label="Tidak" value="False"></v-radio>
                                        </v-col>
                                    </v-row>
                                </v-radio-group>

                                <label for="isEksternal">Apakah peminjaman digunakan bersama dengan pihak Eksternal /
                                    Luar
                                    Kampus?</label>
                                <v-radio-group v-model="item.selectedOptionEksternal" id="isEksternal">
                                    <v-row>
                                        <v-col cols="auto">
                                            <v-radio label="Ya" value="True"></v-radio>
                                        </v-col>
                                        <v-col cols="auto">
                                            <v-radio label="Tidak" value="False"></v-radio>
                                        </v-col>
                                    </v-row>
                                </v-radio-group>
                            </div>

                            <div v-for="(alatItem, alatIndex) in item.alat" :key="alatIndex"
                                style="display: flex; align-items: center; grid-column: span 4; width: 145%;">
                                <v-combobox v-model="alatItem.nama" :items="item.daftarAlat"
                                    label="Alat yang ingin dipinjam" clearable variant="outlined"
                                    style="margin-left: 303px; margin-right: -5px; width: 50px;">
                                </v-combobox>

                                <v-text-field type="number" label="Jumlah" v-model="alatItem.jumlahPinjam"
                                    variant="outlined" clearable
                                    style="margin-right: -25px; width: 40px; margin-left: 10px;"></v-text-field>

                                <v-btn @click="tambahAlat(index)" style="font-size: 18px; margin-left: 25px; margin-right: 90px; border-radius: 50%; width: 60px; height: 60px; background-color: none; box-shadow: none;
                            margin-top: -18px;"><v-icon>mdi-plus-circle</v-icon></v-btn>

                                <v-btn @click="hapusAlat(index, alatIndex)" style="font-size: 18px; margin-left: -90px; margin-right: 120px; border-radius: 50%; width: 60px; height: 60px; background-color: none; box-shadow: none;
                            margin-top: -18px;">
                                    <v-icon>mdi-minus-circle</v-icon></v-btn>
                            </div>

                            <v-file-input
                                v-if="item.selectedOptionOrganisation === 'True' || item.selectedOptionEksternal === 'True'"
                                type="file" accept="file/pdf" :no-icon="true" v-model="item.dokumen"
                                style="width: 505px; margin-left: 303px; margin-top: 5px;" variant="outlined"
                                label="Surat Peminjaman"></v-file-input>

                            <div
                                style="display: flex; justify-content: space-between; margin-left: 320px; margin-right: 20px; margin-bottom: 50px;">
                                <v-btn @click="addNewForm" id="tambah" style="margin-right: 10px; margin-left: -5px;"
                                    prepend-icon=mdi-plus-circle color="primary">Tambah
                                    Peminjaman</v-btn>
                                <v-btn @click="removeForm(index)" id="hapus" prepend-icon="mdi-minus-circle"
                                    color="error">Hapus
                                    Peminjaman</v-btn>
                            </div>
                        </div>

                        <v-checkbox label="Apabila terjadi kerusakan alat atau kehilangan alat 
                        maka bersedia untuk ganti rugi sesuai dengan persyaratan yang telah ditentukan." value="true"
                            style="margin-left: 295px; margin-right: -80px;" v-model="this.ketentuan"></v-checkbox>

                        <v-btn @click="saveItem" id="simpan" type="submit"
                            style="margin-left: 430px; margin-top: -5px; border-radius: 20px; font-size: 15px; width: 250px;"
                            color="primary">
                            Pinjam Alat </v-btn>
                    </v-form>
                </div>
            </v-container>

            <v-container style="font-family: Lexend-Regular; width: 45%; margin-left: 300px; margin-right: 20px;">
                <div v-for="(item, index) in form" :key="'alattersedia-' + index" :id="'alattersedia-' + index"
                    style="border-radius: 10px; border: 1px solid; padding: 30px; margin-bottom: 750px;">
                    <p style="font-size: 20px; font-family: Lexend-Medium; margin-bottom: 20px;">Daftar Peralatan FTI
                        UKDW</p>
                    <v-card
                        style="border-radius: 10px; background-color: rgb(3, 138, 33, 0.3); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3)">
                        <v-table style="overflow: hidden;">
                            <thead style="font-family: Lexend-Regular; font-size: 15px;">
                                <tr>
                                    <th class="text-center"
                                        style="background-color: rgb(3, 138, 33, 0.1); width: 20px;">No</th>
                                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Nama Alat
                                    </th>
                                    <th class="text-center" style="background-color: rgb(3, 138, 33, 0.1)">Jumlah
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(alat, index) in item.items.slice(1)" :key="index"
                                    style="background-color: white; font-family: 'Lexend-Regular; font-size: 15px;">
                                    <td style="width: 20px; text-align: center;"> {{ index + 1 }}
                                    </td>
                                    <td style="width: 50px; "> {{ alat.nama }} </td>
                                    <td style="width: 50px; "> {{ alat.jumlahKetersediaan }} </td>
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
import { reactive, onMounted } from 'vue';
import headerUser from '../components/headerUser.vue'
import footerPage from '../components/footerPage.vue'
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerDekanat from '../components/headerDekanat.vue'
import headerAdmin from '../components/headerAdmin.vue'

export default {
    name: "peminjamanAlat",
    components: {
        headerUser,
        footerPage,
        headerSuperAdmin,
        headerDekanat,
        headerAdmin
    },
    setup() {
        const form = reactive([
            {
                tanggalAwal: '',
                modal: false,
                tanggalSelesai: '',
                isPersonal: '',
                isOrganisation: '',
                isEksternal: '',
                selectedOptionPersonal: '',
                selectedOptionEksternal: '',
                selectedOptionOrganisation: '',
                items: [{}],
                daftarAlat: [],
                alat: reactive([{
                    nama: '',
                    jumlahKetersediaan: 0,
                }]),
                keterangan: '',
                dokumen: null,
            }
        ])

        const addNewForm = () => {
            form.push({
                tanggalAwal: '',
                modal: false,
                tanggalSelesai: '',
                isPersonal: '',
                isOrganisation: '',
                isEksternal: '',
                selectedOptionPersonal: '',
                selectedOptionEksternal: '',
                selectedOptionOrganisation: '',
                items: [{}],
                alat: reactive([{
                    nama: '',
                    jumlahPinjam: 0,
                }]),
                keterangan: '',
                dokumen: null,
                daftarAlat: [],
            })
        }

        const removeForm = (index) => {
            if (form.length > 1) {
                form.splice(index, 1)
            }
        }

        const saveItem = async () => {
            const savedItems = [];
            const dataSend = [];

            const UserID = localStorage.getItem('UserID');

            for (const formData of form) {
                const dataToSave = {
                    tanggalSelesai: formData.tanggalSelesai,
                    tanggalAwal: formData.tanggalAwal,
                    selectedOptionPersonal: formData.selectedOptionPersonal,
                    selectedOptionEksternal: formData.selectedOptionEksternal,
                    selectedOptionOrganisation: formData.selectedOptionOrganisation,
                    alat: formData.alat,
                    keterangan: formData.keterangan,
                    dokumen: formData.dokumen,
                    UserID: UserID
                };
                dataSend.push(dataToSave);
            }

            for (const formData of form) {
                const dataToSave = {
                    tanggalSelesai: formData.tanggalSelesai,
                    tanggalAwal: formData.tanggalAwal,
                    selectedRuangan: formData.selectedRuangan,
                    selectedOptionPersonal: formData.selectedOptionPersonal,
                    selectedOptionEksternal: formData.selectedOptionEksternal,
                    selectedOptionOrganisation: formData.selectedOptionOrganisation,
                    alat: formData.alat,
                    keterangan: formData.keterangan,
                    dokumen: formData.dokumen,
                    UserID: UserID
                };

                console.log(dataToSave);
                try {
                    const response = await axios({
                        method: 'POST',
                        url: 'http://localhost:8000/api/peminjamanAlat/',
                        data: dataSend,
                        headers: {
                            'Access-Control-Allow-Origin': '*',
                            'Content-Type': 'application/json'
                        },
                    });
                    savedItems.push(response.data);
                    console.log('Peminjaman saved successfully:', response.data);
                } catch (error) {
                    console.error('Error menyimpan data peminjaman alat', error);
                }
            }
        }

        const fetchAlat = () => {
            axios.get("http://127.0.0.1:8000/api/alat/")
                .then(response => {
                    form.forEach(item => {
                        const dataAlat = response.data;
                        dataAlat.forEach(alat => {
                            item.items.push({
                                nama: alat.Nama,
                                jumlahKetersediaan: alat.Jumlah_ketersediaan
                            })
                        })
                        item.daftarAlat = response.data.map(alat => alat.Nama);
                        console.log("item", item.items);
                    });
                })
                .catch(error => {
                    console.error("Error gagal mengambil data Alat", error);
                });
        }

        const availableRoom = async (tanggalAwal, tanggalSelesai) => {
            if (tanggalAwal && tanggalSelesai) {
                try {
                    const response = await axios.get(
                        `http://127.0.0.1:8000/api/peminjamanRuangan/jadwalPeminjaman/${tanggalAwal}/${tanggalSelesai}`
                    );
                    const availableRuangan = response.data.availableRoom;
                    const roomdetail = response.data.detailRuangan;

                    console.log(availableRuangan);
                    console.log(roomdetail);

                    const index = form.findIndex(item => item.tanggalAwal === tanggalAwal && item.tanggalSelesai === tanggalSelesai);
                    if (index > -1) {
                        form[index].Ruangan = availableRuangan;
                        form[index].detailRuangan = roomdetail;
                    } else {
                        console.warn("Could not find matching form item for fetched available rooms.");
                    }

                } catch (error) {
                    console.error("Error fetching available rooms:", error);
                }
            }
        };

        const tambahAlat = (index) => {
            console.log('Index:', index);
            console.log('Form length:', form.length);
            console.log('Form', form[index])

            const alat = form[index].alat;
            const newAlat = {
                nama: '',
                jumlahPinjam: 0,
            };

            alat.push(newAlat);
        };

        const hapusAlat = (index, alatIndex) => {
            if (form.length > 0 && form[index].alat.length > 1) {
                form[index].alat.splice(alatIndex, 1);
                console.log('Form', form[index]);
            } else {
                // kalau tinggal 1 peminjaman
                console.warn('Cannot remove the last alat or form.');
            }
        }

        onMounted(() => {
            fetchAlat();
        });

        return { form, addNewForm, removeForm, fetchAlat, saveItem, availableRoom, tambahAlat, hapusAlat }
    },
    data() {
        return {
            ketentuan: false,
            User_role: localStorage.getItem('User_role'),
        }
    }
}
</script>

<style lang="scss" scoped></style>